<?php
/*
Plugin Name: Ban Hammer
Plugin URI: http://halfelf.org/plugins/ban-hammer/
Description: This plugin prevent people from registering with any email you list.
Version: 2.0
Author: Mika Epstein
Author URI: http://www.ipstenu.org/

Copyright 2009-11 Mika Epstein (email: ipstenu@ipstenu.org)

    This file is part of Ban Hammer, a plugin for WordPress.

    Ban Hammer is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    Ban Hammer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WordPress.  If not, see <http://www.gnu.org/licenses/>.

*/

// First we check to make sure you meet the requirements
global $wp_version;
$exit_msg_ms  = 'Sorry, but this plugin is not supported (and will not work) on WordPress MultiSite.';
$exit_msg_ver = 'Sorry, but this plugin is no longer supported on pre-3.0 WordPress installs.';
if( is_multisite() ) { exit($exit_msg_ms); }
if (version_compare($wp_version,"2.9","<")) { exit($exit_msg_ver); }

load_plugin_textdomain('banhammer', false, basename( dirname( __FILE__ ) ) . '/languages' );

// Here's the basic plugin for WordPress SANS BuddyPress
function banhammer($user_login, $user_email, $errors) {

        // Get blacklist
        $blacklist_string = get_option('blacklist_keys');
        $blacklist_array = explode("\n", $blacklist_string);
        $blacklist_size = sizeof($blacklist_array);

        // Go through blacklist
        for($i = 0; $i < $blacklist_size; $i++)
        {
                $blacklist_current = trim($blacklist_array[$i]);
                if(stripos($user_email, $blacklist_current) !== false)
                {
                        $errors->add('invalid_email', __( get_option('banhammer_message') ));
                        return;
                }
        }
}

// And here's the plugin for BuddyPress
// Due to how BuddyPress Works, I had to break this out. See the link for why.
// http://codex.buddypress.org/plugin-development/checking-buddypress-is-active/

add_action( 'bp_include', 'banhammer_bp_init' );

function banhammer_bp_init() {
    function banhammer_bp_signup( $result ) {
    	if ( banhammer_bp_bademail( $result['user_email'] ) )
    		$result['errors']->add('user_email',  __( get_option('banhammer_message') ) );
    	return $result;
    }
    add_filter( 'bp_core_validate_user_signup', 'banhammer_bp_signup' );
    
    function banhammer_bp_bademail( $user_email ) {
            // Get blacklist
            $blacklist_string = get_option('blacklist_keys');
            $blacklist_array = explode("\n", $blacklist_string);
            $blacklist_size = sizeof($blacklist_array);
    
            // Go through blacklist
            for($i = 0; $i < $blacklist_size; $i++)
            {
                    $blacklist_current = trim($blacklist_array[$i]);
                    if(stripos($user_email, $blacklist_current) !== false)
                    {
                     return true;
                    }
            }
    }
}

// Create the options for the message and spam assassin and set some defaults.
function banhammer_activate() {
        add_option('banhammer_message', '<strong>ERROR</strong>: Your email has been banned from registration.');
}

// Load the options pages
function banhammer_optionsmenu() {
    add_submenu_page('tools.php', 'Ban Hammer', 'Ban Hammer', 'moderate_comments', 'ban-hammer/ban-hammer_options.php');
}

// Hooks
add_action('admin_menu', 'banhammer_optionsmenu');
add_action('register_post', 'banhammer', 10, 3);

register_activation_hook( __FILE__, 'banhammer_activate' );

// donate link on manage plugin page
add_filter('plugin_row_meta', 'banhammer_donate_link', 10, 2);
function banhammer_donate_link($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
                $donate_link = '<a href="https://www.wepay.com/donations/halfelf-wp">Donate</a>';
                $links[] = $donate_link;
        }
        return $links;
}