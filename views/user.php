<?php

/*
 * Renders a summary karma report for single user.
 * with links to detailed tables
 */       
class carnieKarmaUserView {
 
	/*
 	 * Renders a summary of users' gig participation karma 
	 * with link to detailed table
	 */
	function render_gig_summary($user_id) {
		print "<h3>Gig Participation Karma</h3>";
		print "<p>Not Done Yet</p>";
	}

	/*
 	 * Renders a summary of users' workshop participation karma 
	 * with link to detailed table
	 */
	function render_workshop_summary($user_id) {
		print "<h3>Workshop Participation Karma</h3>";
		print "<p>Not Done Yet</p>";
	}

	/*
 	 * Renders a summary of users' tour karma burned
	 * with link to detailed table
	 */
	function render_tour_summary($user_id) {
		print "<h3>Tour Karmic Load</h3>";
		print "<p>Not Done Yet</p>";
	}

	/*
 	 * Renders Karmic balance
	 */
	function render_balance($user_id) {
		print "<h3>Karmic Balance</h3>";
		print "<p>Not Done Yet</p>";
	}

	/*
 	 * Renders a summary karma report for single user.
	 * with links to detailed tables
	 */
	function render($user_id) {
                $user_info = get_userdata($user_id);

                $siteurl = get_bloginfo('siteurl');
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;


		// Title
		print "<h2>Participation Karma for "; 
		if ($user_info->first_name || $user_info->last_name) {
			echo $user_info->first_name . ' ' . $user_info->last_name;
			echo ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				'</a>)';
		} else {
			echo $user->display_name;
			echo ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				'</a>)';
		}
		print "</h2>";

		// Workshop participation Karma
		$this->render_workshop_summary($user_id);

		// Gig participation Karma
		$this->render_gig_summary($user_id);

		// Tour karma burned
		$this->render_tour_summary($user_id);

		// Karmic Balance
		$this->render_balance($user_id);
	}
}
?>
