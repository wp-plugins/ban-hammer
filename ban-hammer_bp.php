<?php
/*
Ban Hammer Options File for BuddyPress

	Due to how BuddyPress Works, I had to break this out. See the link for why 
	http://codex.buddypress.org/plugin-development/checking-buddypress-is-active/

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

function banhammer_bp_signup( $result ) {

	if ( banhammer_bp_bademail( $result['user_email'] ) )
		$result['errors']->add('user_email',  __( get_option('banhammer_message') ) );

	return $result;

}
add_filter( 'bp_core_validate_user_signup', 'banhammer_bp_signup' );

function banhammer_bp_bademail( $user_email ) {

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
                        return true;
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
                 return true;
                }
        }
}