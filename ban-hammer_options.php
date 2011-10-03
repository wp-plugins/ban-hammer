<?php
/*
Ban Hammer Options File
Copyright 2009-11 Mika Epstein (email: ipstenu@ipstenu.org)

    This file is part of Ban Hammer, a plugin for WordPress.

    Ban Hammer is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ban Hammer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WordPress.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div class="wrap">

<h2>Ban Hammer</h2>

<?php
global $wpdb;

// Checks for presence of the cURL extension.
function _iscurlinstalled() {
	if  (in_array  ('curl', get_loaded_extensions())) {
		return true;
	}
	else{
		return false;
	}
}

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

        // Update  Show Stop Forum Spam in the users lists
                if ($banhammer_newsfsusers = $_POST['banhammer_newsfsusers'])
                {
                        update_option('banhammer_showsfsusers', $banhammer_newsfsusers);
                }
                else
                {
                        update_option('banhammer_showsfsusers', '0');
                }

				
        // Update Ban Message
                if ($banhammer_newmess = $_POST['banhammer_newmess'])
                {
                        update_option('banhammer_message', $banhammer_newmess);
                }

?>
        <div id="message" class="updated fade"><p><strong><?php _e('Options Updated!', banhammer); ?></strong></p></div>
<?php
        }

        if (get_option('banhammer_stopforumspam') != '0' )
        {
                $banhammer_sfs = ' checked="checked"';
        } else {
                $banhammer_sfs = '';
        }
        if (get_option('banhammer_showsfsusers') != '0' )
        {
                $banhammer_sfsusers = ' checked="checked"';
        } else {
                $banhammer_sfsusers = '';
        }
?>

<form method="post" width='1'>

<fieldset class="options">
<legend><h3><?php _e('Personalize the Message', banhammer); ?></h3></legend>
<p><?php _e("The message below is displayed to users who are not allowed to register on your blog. Edit is as you see fit, but remember you don't get a LOT of space so keep it simple.", banhammer); ?></p>

<textarea name='banhammer_newmess' cols='80' rows='2'><?php echo get_option('banhammer_message'); ?></textarea>
</fieldset>

<?php if (_iscurlinstalled()) { ?>
<fieldset class="options">
<legend><h3><?php _e('Use StopForumSpam.com?', banhammer); ?></h3></legend>
<p> <input type="checkbox" id="banhammer_newsfs" name="banhammer_newsfs" value="1" <?php echo $banhammer_sfs ?> /> <a href="http://www.stopforumspam.com/">StopForumSpam.com</a> is a repository for forum spambots.  Since a disturbingly high number of them also sign up on blogs, some people may want to block them here as well.  If you do, check the box. If not, leave it alone (which is the default).</p>
</fieldset>
<?php 	}
	else { ?>
		<p><?php _e("It does not appear that you have cURL installed on your server.  The StopForumSpam checker will not run without it, and as such, you cannot use it.  Sorry about that. Ask your webhost about getting cURL installed.", banhammer); ?></p>
<?php } ?>

<?php
if (get_option('banhammer_stopforumspam') != '0' )
        {
?>

<fieldset class="options">
<legend><h3><?php _e("Show StopForumSpam.com status in User List?", banhammer); ?></h3></legend>
<p> <input type="checkbox" id="banhammer_newsfsusers" name="banhammer_newsfsusers" value="1" <?php echo $banhammer_sfsusers ?> /> <?php _e("If you want to show the status of users (based on their email) as spammers or not in the regular user list, you can check this box.", banhammer); ?></p>
</fieldset>

<p><?php _e("For a list of all users who should have been blocked by StopForumSpam, see", banhammer); ?> <a href="users.php?page=ban-hammer/ban-hammer_users.php"><?php _e("Ban Hammer - Users", banhammer); ?></a>.  <?php _e("Depending on how many users you have registered, this may take a long time to run.", banhammer); ?></p>

<?php
        }

?>

<fieldset class="options">
<legend><h3><?php _e("Blacklisted Domains", banhammer); ?></h3></legend>
<p><?php _e("The domains added below will not be allowed to be used during registration.", banhammer); ?></p>

<textarea name="blacklist_keys" cols="40" rows="15"><?php
        $blacklist = get_option('blacklist_keys');
        echo $blacklist;
?></textarea>
</fieldset>
        <p class="submit"><input type="submit" name="update" value="<?php _e("Update Options", banhammer); ?>" /></p>

</form>

</div>