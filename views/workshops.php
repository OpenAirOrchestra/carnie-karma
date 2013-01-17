<?php

/*
 * Renders a workshop karma report for a user.
 */       
class carnieKarmaWorkshopsView {

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
 	 * Renders a workshop karma report for a user.
	 */
	function render($user_id, $workshops, $count, $paged, $limit) {

                $siteurl = get_bloginfo('siteurl');
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;
                $user_info = get_userdata($user_id);

		// Title
		$karma_list_nonce = wp_create_nonce('karma_list_nonce');
?>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                <input type="hidden" name="karma_list_nonce" value="<?php echo $karma_list_nonce; ?>" />
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
		<h2>Workshop Participation Karma for 
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
		$tableView->render_table_nav("top", "workshop", $karma_detail_nonce, $user_id, $count, $paged, $limit);

		print "<table>";
		print "<thead>";
		$this->render_table_header_footer();
		print "</thead>";
		print "<tfoot>";
		$this->render_table_header_footer();
		print "</tfoot>";
		print "<tbody>";
		foreach ($workshops as $workshop) {

			$karma = $workshop['karma'];

			$workshop_url = $siteurl . '/wp-admin/admin.php?page=workshop&workshop=' . $workshop['workshop_id'];

			print "<tr>";
			echo "<td>" .  $workshop['date'] . " </td>";
			echo "<td>";
			echo '<a href="' . $workshop_url . '">';
			echo $workshop['title'];
			echo "</a>";
			echo "</td>";
			echo "<td>" .  (abs($karma) < 0.1 ? $karma : number_format($karma, 1)) . " </td>";
			print "</tr>";
		}
		print "</tbody>";
		print "</table>";


		$tableView->render_table_nav("top", "workshop", $karma_detail_nonce, $user_id, $count, $paged, $limit);

	}
}
?>
