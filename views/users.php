<?php

/*
 * Renders a list of users, each linking to
 * a karma report for that user.
 */       
class carnieKarmaUsersView {
 
	/*
	 * Renders a list of users, each linking to a karma report
	 * for that user.
	 */
	function render($users) {

		print "<h2>Participants</h2>";

                $karma_list_nonce = wp_create_nonce('karma_list_nonce');


		print "<UL>";
		

	
		foreach ($users as $user) {
			if ($user['ID'] != 1) {
				print "<LI>";
?>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                <input type="hidden" name="karma_list_nonce" value="<?php echo $karma_list_nonce; ?>" />
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>" />
<?php
				$user_info = get_userdata($user['ID']);
				if ($user_info->first_name || $user_info->last_name) {
					echo $user_info->first_name . ' ' . $user_info->last_name;
					echo ' (' . $user['user_login'] . ')';
				} else {
					echo $user['display_name'];
					echo ' (' . $user['user_login'] . ')';
				}
				
?>
                <input type="submit" value="Details" />
	</form>
<?php

				print "</LI>";
			}
		}

		print "</UL>";
	}
}
?>
