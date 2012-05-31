<?php

// This is the uninstall script.

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit();

        delete_option('banhammer_stopforumspam');
	delete_option('banhammer_showsfsusers');
        delete_option('banhammer_message');
