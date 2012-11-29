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
		print "<UL>";
		
		foreach ($users as $user) {
			if ($user->ID != 1) {
				print "<LI>";
				$name = $user->display_name;
				$user_info = get_userdata($user->ID);
				if ($user_info->first_name || $user_info->last_name) {
					$name = $user_info->first_name . ' ' . $user_info->last_name;
				}
				echo $name;
				
				if ($user_info->user_description) {
					echo '<div class="details">' . $user_info->user_description . '</div>';
				}

				print "</LI>";
			}
		}

		print "</UL>";
	}
}
?>
