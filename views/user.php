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
	function render_gig_summary($user_id, $gigs, $gig_karma, $karma_detail_nonce) {
?>
		<h3>Verified Gig Participation Karma</h3>

		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="karma_detail" value="gig" />
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
			<input type="hidden" name="karma_detail_nonce" value="<?php echo $karma_detail_nonce; ?>" />
			<p>
				Verified Gigs: <?php echo ($gigs ? $gigs : 0); ?>
				<br/>
				Verified Gig Participation Karma: <?php echo ($gig_karma ? $gig_karma : 0); ?>
				<br/>
                		<input type="submit" value="Details" />
			</p>
		</form>
<?php

	}

	/*
 	 * Renders a summary of users' workshop participation karma 
	 * with link to detailed table
	 */
	function render_workshop_summary($user_id, $workshops, $workshop_karma, $karma_detail_nonce) {


?>
		<h3>Workshop Participation Karma</h3>

		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="karma_detail" value="workshop" />
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
			<input type="hidden" name="karma_detail_nonce" value="<?php echo $karma_detail_nonce; ?>" />
			<p>
				Workshops: <?php echo ($workshops ? $workshops : 0); ?>
				<br/>
				Workshop Participation Karma: <?php echo ($workshop_karma ? $workshop_karma : 0); ?>
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
	function render_tour_summary($user_id, $tours, $karmic_load, $karma_detail_nonce) {
?>
		<h3>Tour Karmic Load</h3>

		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="karma_detail" value="load" />
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
			<input type="hidden" name="karma_detail_nonce" value="<?php echo $karma_detail_nonce; ?>" />
			<p>
				Tours: <?php echo ($tours ? $tours : 0); ?>
				<br/>
				Karmic Load: <?php echo ($karmic_load ? $karmic_load : 0); ?>
				<br/>
                		<input type="submit" value="Details" />
			</p>
		</form>
<?php
	}

	/*
 	 * Renders Karmic balance
	 */
	function render_balance($user_id) {
		print "<h3>Karmic Balance</h3>";
		print "<p>Not Done Yet</p>";
		print "<p>To calculate Karmic Balance, expect some math like <br/>500 * workshop karma + 1000 * gig karma - 1000 * tour karmic load</p>";
	}

	/*
 	 * Renders a summary karma report for single user.
	 * with links to detailed tables
	 */
	function render($user_id, $workshops, $workshop_karma, $gigs, $gig_karma, $tours, $karmic_load) {
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

                $karma_detail_nonce = wp_create_nonce('karma_detail_nonce');

		// Workshop participation Karma
		$this->render_workshop_summary($user_id, $workshops, $workshop_karma, $karma_detail_nonce);

		// Gig participation Karma
		$this->render_gig_summary($user_id, $gigs, $gig_karma, $karma_detail_nonce);

		// Tour karma burned
		$this->render_tour_summary($user_id, $tours, $karmic_load, $karma_detail_nonce);

		// Karmic Balance
		$this->render_balance($user_id);
	}
}
?>
