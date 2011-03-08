<?php
/*
Plugin Name: Ban Hammer
Plugin URI: http://code.ipstenu.org/my-plugins/ban-hammer
Description: This plugin prevent people from registering with any email you list.
Version: 1.5
Author: Mika Epstein
Author URI: http://www.ipstenu.org/

Copyright 2009-10 Mika Epstein (email: ipstenu@ipstenu.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

$exit_msg_ms  = 'Sorry, but this plugin is not supported (and will not work) on WordPress MultiSite.';
$exit_msg_ver = 'Sorry, but this plugin is no longer supported on pre-3.0 WordPress installs.';

if( is_multisite() ) { exit($exit_msg_ms); }
if( version_compare($wp_version, "3.0", "<"))  { exit($exit_msg_ver); }

// Here's the basic plugin for WordPress SANS BuddyPress
function banhammer($user_login, $user_email, $errors) {

        // First we check the users against StopForumSpam but ONLY if they checked the box
        if (get_option('banhammer_stopforumspam') != '0' )
        {
                //Initialize the Curl session
                $ch = curl_init();
                $StopForumSpam = "http://www.stopforumspam.com/api?email=$user_email";
                //Set curl to return the data instead of printing it to the browser.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //Set the URL
                curl_setopt($ch, CURLOPT_URL, $StopForumSpam);
                //Execute the fetch
                $check = curl_exec($ch);
                //Close the connection
                curl_close($ch);

                $test = "yes";
                if ( strpos( $check, $test) > 0 )
                {
                        $errors->add('invalid_email', __( get_option('banhammer_message') ));
                        return;
                }
        }

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
function banhammer_bp_init() {
    require( dirname( __FILE__ ) . '/ban-hammer_bp.php' );
}
add_action( 'bp_include', 'banhammer_bp_init' );

// This will show if a user is a spammer on the normal users page
if (get_option('banhammer_showsfsusers') != '0' ) {

   function stopforumspam_columns($value, $column_name, $user_id) {
        if ( $column_name == 'stopforumspam_status' ) {
                $user = get_userdata( $user_id );
                $email = $user->user_email;

                // Check the users against StopForumSpam
                $ch = curl_init();
                $StopForumSpam = "http://www.stopforumspam.com/api?email=$email";
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $StopForumSpam);
                $check = curl_exec($ch);
                curl_close($ch);

                $test = "yes";
                if ( strpos( $check, $test) > 0 ){
                    $ret = '<em>'.__('YES!', 'stopforumspam_status').'</em>';
                    return $ret;
                } else {
                    $ret = '<em>'.__('&nbsp;', 'stopforumspam_status').'</em>';
                    return $ret;
                }
        }
        return $value;
   }

   // Add in a column header
   function stopforumspam_status($column_headers) {
    $column_headers['stopforumspam_status'] = __('Spammer', 'stopforumspam_status');
    return $column_headers;
   }

   add_filter('manage_users_columns', 'stopforumspam_status');
   add_action('manage_users_custom_column',  'stopforumspam_columns', 10, 3);

}


// Create the options for the message and spam assassin and set some defaults.
function banhammer_activate() {
        update_option('banhammer_stopforumspam', '0');
		update_option('banhammer_showsfsusers', '0');
        update_option('banhammer_message', '<strong>ERROR</strong>: Your email has been banned from registration.');
}

// Delete the options if the plugin is being turned off (pet peeve) - This DOES NOT wipe out your blacklist.
function banhammer_deactivate() {
        delete_option('banhammer_stopforumspam');
		delete_option('banhammer_showsfsusers');
        delete_option('banhammer_message');
}

// Load the options pages
function banhammer_optionsmenu() {
        if (function_exists('add_submenu_page')) {
          add_submenu_page('tools.php', 'Ban Hammer', 'Ban Hammer', '8', 'ban-hammer/ban-hammer_options.php');
        }
}
function banhammer_usersmenu() {
    if (function_exists('add_submenu_page')) {
      add_submenu_page('users.php', 'Ban Hammered', 'Ban Hammered', '8', 'ban-hammer/ban-hammer_users.php');
    }
}

// Hooks
add_action('admin_menu', 'banhammer_optionsmenu');
add_action('admin_menu', 'banhammer_usersmenu');
add_action('register_post', 'banhammer', 10, 3);

register_activation_hook( __FILE__, 'banhammer_activate' );
register_deactivation_hook( __FILE__, 'banhammer_deactivate' );
?>