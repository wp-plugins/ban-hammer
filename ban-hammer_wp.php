<?php

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

// Now that we have all the code, we get to make columns!

// This will show if a user is a spammer on the normal users page
if (get_option('banhammer_showsfsusers') != '0' ) {


	// Add in a column header
	function stopforumspam_status($column_headers) {
		$column_headers['stopforumspam_status'] = __('Spammer', 'stopforumspam_status');
		return $column_headers;
	}
	add_filter('manage_users_columns', 'stopforumspam_status');

	// Display the column content
   function stopforumspam_columns($value, $column_name, $user_id) {
		if ( 'stopforumspam_status' != $column_name )
			return $value;
	
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
			$spammer = '<em>'.__('YES', 'stopforumspam_status').'</em>';
			return $spammer;
		}
	}
	add_action('manage_users_custom_column',  'stopforumspam_columns', 10, 3);
   
}
?>