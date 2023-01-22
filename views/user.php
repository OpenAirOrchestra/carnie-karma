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
		if (! $gig_karma) {
			$gig_karma = 0;
		}
		return "
		<h3>Verified Gig Participation Karma</h3>

		<form method=\"post\" action=\"" . $_SERVER["REQUEST_URI"] . "\">
			<input type=\"hidden\" name=\"karma_detail\" value=\"gig\" />
			<input type=\"hidden\" name=\"user_id\" value=\"" . $user_id . "\" />
			<input type=\"hidden\" name=\"karma_detail_nonce\" value=\"" .  $karma_detail_nonce . "\" />
			<p>
				Verified Gigs: " . ($gigs ? $gigs : 0) . "
				<br/>
				Verified Gig Participation Karma: " . (abs($gig_karma) < 0.1 ? $gig_karma : number_format($gig_karma, 1))  . "
				<br/>
                		<input type=\"submit\" value=\"Details\" />
			</p>
		</form>
		";

	}

	/*
 	 * Renders a summary of users' workshop participation karma 
	 * with link to detailed table
	 */
	function render_workshop_summary($user_id, $workshops, $workshop_karma, $karma_detail_nonce) {


		return "
		<h3>Workshop Participation Karma</h3>

		<form method=\"post\" action=\"" . $_SERVER["REQUEST_URI"] . "\">
			<input type=\"hidden\" name=\"karma_detail\" value=\"workshop\" />
			<input type=\"hidden\" name=\"user_id\" value=\"" . $user_id . "\" />
			<input type=\"hidden\" name=\"karma_detail_nonce\" value=\"" .  $karma_detail_nonce . "\" />
			<p>
				Workshops: " . ($workshops ? $workshops : 0) . "
				<br/>
				Workshop Participation Karma: " .  ($workshop_karma ? number_format($workshop_karma, 1) : 0) . "
				<br/>
				<input type=\"submit\" value=\"Details\" />
				</p>
		</form>
        ";

	}

	/*
 	 * Renders a summary of users' tour karma burned
	 * with link to detailed table
	 */
	function render_tour_summary($user_id, $tours, $karmic_load, $karma_detail_nonce) {
		return "
		<h3>Tour Karmic Load</h3>

		<form method=\"post\" action=\"" . $_SERVER["REQUEST_URI"] . "\">
		<input type=\"hidden\" name=\"karma_detail\" value=\"load\" />
		<input type=\"hidden\" name=\"user_id\" value=\"" . $user_id . "\" />
		<input type=\"hidden\" name=\"karma_detail_nonce\" value=\"" .  $karma_detail_nonce . "\" />
		<p>
		Workshops: " . ($tours ? $tours : 0) . "
		<br/>
				Karmic Load: " .  ($karmic_load ? number_format($karmic_load, 1) : 0) . "
				<br/>
				<input type=\"submit\" value=\"Details\" />
			</p>
		</form>
        ";
	}

	/*
 	 * Renders Karmic balance
	 */
	function render_balance($user_id, $workshop_karma, $gig_karma, $karmic_load) {
		$balance = $workshop_karma + $gig_karma - $karmic_load;
		return "

		<h3>Karmic Balance</h3>
		<p>
			Karmic Balance: " .  abs($balance) < 0.1 ? $balance : number_format($balance, 1) . "
		</p>
        ";
	}

	/*
 	 * Renders a summary karma report for single user.
	 * with links to detailed tables
	 */
	function render($user_id, $workshops, $workshop_karma, $gigs, $gig_karma, $tours, $karmic_load) {
                $user_info = get_userdata($user_id);

                $siteurl = get_bloginfo('url');
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;

		// Title
		$result =  "<h2>Participation Karma for "; 
		if ($user_info->first_name || $user_info->last_name) {
			$result .= $user_info->first_name . ' ' . $user_info->last_name;
			$result .= ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				'</a>)';
		} else {
			$result .= $user->display_name;
			$result .= ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				'</a>)';
		}
		$result .= "</h2>";

        $karma_detail_nonce = wp_create_nonce('karma_detail_nonce');

		// Workshop participation Karma
		$result .= $this->render_workshop_summary($user_id, $workshops, $workshop_karma, $karma_detail_nonce);

		// Gig participation Karma
		$result .= $this->render_gig_summary($user_id, $gigs, $gig_karma, $karma_detail_nonce);

		// Tour karma burned
		$result .= $this->render_tour_summary($user_id, $tours, $karmic_load, $karma_detail_nonce);

		// Karmic Balance
		$result .= $this->render_balance($user_id, $workshop_karma, $gig_karma, $karmic_load);

		return $result;
	}
}
?>
