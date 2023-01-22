<?php

/*
 * Renders a gigs karma report for a user.
 */       
class carnieKarmaGigsView {

        /*
         * render table header/footer
         */
        function render_table_header_footer() {
			return "<tr><th>Date</th><th>Title</th><th>Karma</th></tr>";
	}

 
	/*
 	 * Renders a gig karma report for a user.
	 */
	function render($user_id, $gigs, $count, $paged, $limit) {

        $siteurl = get_bloginfo('url');
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;
        $user_info = get_userdata($user_id);

		// Title
		$karma_list_nonce = wp_create_nonce('karma_list_nonce');
		$result = "
        <form method=\"post\" action=\"" .  $_SERVER["REQUEST_URI"] . "\">
                <input type=\"hidden\" name=\"karma_list_nonce\" value=\"". $karma_list_nonce . "\" />
                <input type=\"hidden\" name=\"user_id\" value=\"" .  $user_id . "\" />
		<h2>Verified Gig Participation Karma for ";

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
		$result .= "
                <input type=\"submit\" value=\"Summary\"/>
		</h2>
        </form>";

        $karma_detail_nonce = wp_create_nonce('karma_detail_nonce');

		$tableView = new carnieKarmaTableView;
		$result .= $tableView->render_table_nav("top", "gig", $karma_detail_nonce, $user_id, $count, $paged, $limit);

		$result .= "<table>";
		$result .= "<thead>";
		$result .=$this->render_table_header_footer();
		$result .= "</thead>";
		$result .= "<tfoot>";
		$result .=$this->render_table_header_footer();
		$result .= "</tfoot>";
		$result .= "<tbody>";
		foreach ($gigs as $gig) {

			$karma = $gig['karma'];
			$karma = doubleval($karma) * CARNIE_KARMA_GIG_MULTIPLIER;
			$gig_url = get_permalink($gig['gigid']);

			$result .= "<tr>";
			$result .= "<td>" .  str_replace('-', '&#x2011;', $gig['date']) . " </td>";
			$result .= "<td>";
			$result .= '<a href="' . $gig_url . '">';
			$result .= $gig['title'];
			$result .= "</a>";
			$result .= "</td>";
			$result .= "<td>" .  (abs($karma) < 0.1 ? $karma : number_format($karma, 1)) . " </td>";
			$result .= "</tr>";
		}
		$result .= "</tbody>";
		$result .= "</table>";

		$result .= $tableView->render_table_nav("top", "gig", $karma_detail_nonce, $user_id, $count, $paged, $limit);
		return $result;
	}
}
?>
