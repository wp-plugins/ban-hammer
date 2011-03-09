<?php
/*
Plugin Name: Ban Hammer
Plugin URI: http://code.ipstenu.org/my-plugins/ban-hammer
Description: This plugin prevent people from registering with any email you list.
Version: 1.5.1
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

// First we check to make sure you meet the requirements
global $wp_version;
$exit_msg_ms  = 'Sorry, but this plugin is not supported (and will not work) on WordPress MultiSite.';
$exit_msg_ver = 'Sorry, but this plugin is no longer supported on pre-3.0 WordPress installs.';
if( is_multisite() ) { exit($exit_msg_ms); }
if (version_compare($wp_version,"3.0","<")) { exit($exit_msg_ver); }



// Pull in the code for how it all works
require( dirname( __FILE__ ) . '/ban-hammer_wp.php' );

// And here's the plugin for BuddyPress
function banhammer_bp_init() {
    require( dirname( __FILE__ ) . '/ban-hammer_bp.php' );
}
add_action( 'bp_include', 'banhammer_bp_init' );



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