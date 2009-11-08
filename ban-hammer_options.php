<div class="wrap">

<h2>Ban Hammer</h2>

<?php
global $wpdb;

        if (isset($_POST['update']))
        {
        // Update the Blacklist
                if ($banhammer_keys = $_POST['blacklist_keys'])
                {
                        $banhammer_array = explode("\n", $banhammer_keys);
                        sort ($banhammer_array);
                        $banhammer_string = implode("\n", $banhammer_array);
                        update_option('blacklist_keys', $banhammer_string);
                }

        // Update Stop Forum Spam
                if ($banhammer_newsfs = $_POST['banhammer_newsfs'])
                {
                        update_option('banhammer_stopforumspam', $banhammer_newsfs);
                }
                else
                {
                        update_option('banhammer_stopforumspam', '0');
                }

        // Update Ban Message
                if ($banhammer_newmess = $_POST['banhammer_newmess'])
                {
                        update_option('banhammer_message', $banhammer_newmess);
                }

?>
        <div id="message" class="updated fade"><p><strong>Options Updated!</strong></p></div>
<?php
        }

        if (get_option('banhammer_stopforumspam') != '0' )
        {
                $banhammer_sfs = ' checked="checked"';
        } else {
                $banhammer_sfs = '';
        }
?>

<form method="post" width='1'>

<fieldset class="options">
<legend><h3>Personalize the Message</h3></legend>
<p>The message below is displayed to users who are not allowed to register on your blog. Edit is as you see fit, but remember you don't get a LOT of space so keep it simple.</p>

<textarea name='banhammer_newmess' cols='80' rows='2'><?php echo get_option('banhammer_message'); ?></textarea>
</fieldset>

<fieldset class="options">
<legend><h3>Use StopForumSpam.com?</h3></legend>
<p> <input type="checkbox" id="banhammer_newsfs" name="banhammer_newsfs" value="1" <?php echo $banhammer_sfs ?> /> <a href="http://www.stopforumspam.com/">StopForumSpam.com</a> is a repository for forum spambots.  Since a disturbingly high number of them also sign up on blogs, some people may want to block them here as well.  If you do, check the box. If not, leave it alone (which is the default).</p>
</fieldset>

<?php
if (get_option('banhammer_stopforumspam') != '0' )
        {

$szSort = "user_nicename";
$aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY %s ASC", $szSort ));

foreach ( $aUsersID as $iUserID ) :
                $user = get_userdata( $iUserID );
                $email = $user->user_email;
                $username = $user->display_name;
                
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
                        echo '<li>' . $username . '</li>';
                }

endforeach; 
        }

?>

<fieldset class="options">
<legend><h3>Blacklisted Domains</h3></legend>
<p>The domains added below will <strong>not</strong> be allowed to be used during registration. These are the same as the ones listed in the <em>Comment Blacklist</em> on the <a href=options-discussion.php>Discussion Settings</a> page, but y'know how it is.</p>

<textarea name="blacklist_keys" cols="40" rows="15"><?php
        $blacklist = get_option('blacklist_keys');
        echo $blacklist;
?></textarea>
</fieldset>
        <p class="submit"><input type="submit" name="update" value="Update Options" /></p>

</form>

</div>