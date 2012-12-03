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
	function render_workshop_summary($user_id, $workshops, $workshop_karma) {

                $karma_workshop_summary_nonce = wp_create_nonce('karma_workshop_summary_nonce');

?>
		<h3>Workshop Participation Karma</h3>

		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="karma_detail" value="workshop" />
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
			<p>
<?php
		echo "Workshops: " . ($workshops ? $workshops : 0);
		print "<br/>";
		echo "Workshop Participation Karma: " . ($workshop_karma ? $workshop_karma : 0);
?>
                <input type="hidden" name="karma_workshop_summary_nonce" value="<?php echo $karma_workshop_summary_nonce; ?>" />
		<br/>
                <input type="submit" value="Details" />
		</p>
	</form>
<?php

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
	function render($user_id, $workshops, $workshop_karma) {
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
		$this->render_workshop_summary($user_id, $workshops, $workshop_karma);

		// Gig participation Karma
		$this->render_gig_summary($user_id);

		// Tour karma burned
		$this->render_tour_summary($user_id);

		// Karmic Balance
		$this->render_balance($user_id);
	}
}
?>
