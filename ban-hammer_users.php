<div class="wrap">
<h2><?php _e("Ban Hammer - Users", banhammer); ?></h2>

<?php
        global $wpdb;
        $aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY ID DESC"));
        $size = 40;
        $default = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name='avatar_default'");
?>

<h3><?php _e("Hammer the Users", banhammer); ?></h3>

<p><?php _e("Sometimes a user registers before StopForumSpam catches them. Check here to see who's on your naughty list!", banhammer); ?></p>

<?php
if (get_option('banhammer_stopforumspam') != '0' )
{
?>

<form action="<?php bloginfo('url'); ?>/wp-admin/users.php" method="get">
<input type="hidden" name="wp_http_referer" value="<?php bloginfo('url'); ?>/wp-admin/users.php" />
<?php wp_nonce_field('bulk-users') ?>
<input type="hidden" name="action" value="delete" /> <?php _e("Delete selected spammers", banhammer); ?>: <input type="submit" value="<?php _e("Submit", banhammer); ?>" class="button-secondary action" id="doaction" name="" />

<table class="widefat" cellspacing="0">
<thead>
<tr class="thead">
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
        <th scope="col" id="username" class="manage-column column-username" style=""><?php _e("Username", banhammer); ?></th>
        <th scope="col" id="fullname" class="manage-column column-fullname" style=""><?php _e("Name", banhammer); ?></th>
        <th scope="col" id="email" class="manage-column column-email" style=""><?php _e("E-mail", banhammer); ?></th>
        <th scope="col" id="registered" class="manage-column column-registered" style=""><?php _e("Registered", banhammer); ?></th>
</tr>
</thead>

<tbody id="users" class="list:user user-list">
<?php

        foreach ( $aUsersID as $iUserID ) :
                $user = get_userdata( $iUserID );
                $email = $user->user_email;
                $username = $user->display_name;
                $registered = strtotime($user->user_registered);
                $grav_url = "http://www.gravatar.com/avatar/".md5( strtolower($email) )."?d=".$default."&size=".$size;

                // Check the users against StopForumSpam
                $ch = curl_init();
                $StopForumSpam = "http://www.stopforumspam.com/api?email=$email";
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $StopForumSpam);
                $check = curl_exec($ch);
                curl_close($ch);

                $test = "yes";
                if ( strpos( $check, $test) > 0 )
                {
?>
                <tr id="<?php echo $iUserID; ?>">
                  <td class="check-column" scope="row"><input type="checkbox" value="<?php echo $iUserID; ?>" id="user_<?php echo $iUserID; ?>" name="users[]"></td>
                  <td class="username column-username"><img alt="Avatar" src="<?php echo $grav_url; ?>" class="avatar avatar-<?php echo $size; ?> photo" height="<?php echo $size; ?>" width="<?php echo $size; ?>" /> <strong><a href="user-edit.php?user_id=<?php echo $user->ID ?>&#038;wp_http_referer=%2Fblog%2Fwp-admin%2Fusers.php"><?php echo $user->user_login; ?></a></strong><br /><div class="row-actions"><span class='edit'><a href="user-edit.php?user_id=<?php echo $user->ID ?>&#038;wp_http_referer=%2Fblog%2Fwp-admin%2Fusers.php">Edit</a></span></div></td>
                  <td class="fullname column-fullname"><?php echo $username; ?></td>
                  <td class="email column-email"><?php echo $email; ?> <?php _e("is listed on StopForumSpam.com.", banhammer); ?></td>
                  <td class="registered column-registered"><?php echo date('d M Y \- g:h:s a', $registered); ?></td>
                </tr>
                  <?php
                }
                else { }
        endforeach;
?>
</table>
</form>
<?php
}
else { ?>
<p><?php _e("You're not using StopForumSpam.", banhammer); ?></p>

<?php } ?>
</div>