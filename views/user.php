<?php

/*
 * Renders a summary karma report for single user.
 * with links to detailed tables
 */       
class carnieKarmaUserView {
 
	/*
 	 * Renders a summary karma report for single user.
	 * with links to detailed tables
	 */
	function render($user_id) {
                $user_info = get_userdata($user_id);

		// Title
		print "<h2>Participation Karma for "; 
		if ($user_info->first_name || $user_info->last_name) {
			echo $user_info->first_name . ' ' . $user_info->last_name;
			echo ' (' . $user_info->user_login . ')';
		} else {
			echo $user->display_name;
			echo ' (' . $user-_info>user_login . ')';
		}
		print "</h2>";

		// Workshop participation Karma

		// Gig participation Karma

		// Tour karma burned

		// explanation of Karma decay
	}
}
?>
