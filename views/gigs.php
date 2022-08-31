<?php

/*
 * Renders a gigs karma report for a user.
 */       
class carnieKarmaGigsView {

        /*
         * render table header/footer
         */
        function render_table_header_footer() {
?>
                <tr>
			<th>Date</th>
			<th>Title</th>
			<th>Karma</th>
                </tr>
<?php
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
?>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                <input type="hidden" name="karma_list_nonce" value="<?php echo $karma_list_nonce; ?>" />
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
		<h2>Verified Gig Participation Karma for 
<?php
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
?>
                <input type="submit" value="Summary"/>
		</h2>
        </form>
<?php

                $karma_detail_nonce = wp_create_nonce('karma_detail_nonce');

		$tableView = new carnieKarmaTableView;
		$tableView->render_table_nav("top", "gig", $karma_detail_nonce, $user_id, $count, $paged, $limit);

		print "<table>";
		print "<thead>";
		$this->render_table_header_footer();
		print "</thead>";
		print "<tfoot>";
		$this->render_table_header_footer();
		print "</tfoot>";
		print "<tbody>";
		foreach ($gigs as $gig) {

			$karma = $gig['karma'];
			$karma = doubleval($karma) * CARNIE_KARMA_GIG_MULTIPLIER;
			$gig_url = get_permalink($gig['gigid']);

			print "<tr>";
			echo "<td>" .  str_replace('-', '&#x2011;', $gig['date']) . " </td>";
			echo "<td>";
			echo '<a href="' . $gig_url . '">';
			echo $gig['title'];
			echo "</a>";
			echo "</td>";
			echo "<td>" .  (abs($karma) < 0.1 ? $karma : number_format($karma, 1)) . " </td>";
			print "</tr>";
		}
		print "</tbody>";
		print "</table>";


		$tableView->render_table_nav("top", "gig", $karma_detail_nonce, $user_id, $count, $paged, $limit);

	}
}
?>
