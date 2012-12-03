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
	function render($user_id, $workshops) {

                $siteurl = get_bloginfo('siteurl');
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;
                $user_info = get_userdata($user_id);

		// Title
		print "<h2>Workshop Participation Karma for "; 
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

		print "<table>";
		print "<thead>";
		$this->render_table_header_footer();
		print "</thead>";
		print "<tfoot>";
		$this->render_table_header_footer();
		print "</tfoot>";
		print "<tbody>";
		foreach ($workshops as $workshop) {

			$workshop_url = $siteurl . '/wp-admin/admin.php?page=workshop&workshop=' . $workshop['workshop_id'];

			print "<tr>";
			echo "<td>" .  $workshop['date'] . " </td>";
			echo "<td>";
			echo '<a href="' . $workshop_url . '">';
			echo $workshop['title'];
			echo "</a>";
			echo "</td>";
			echo "<td>" .  $workshop['karma'] . " </td>";
			print "</tr>";
		}
		print "</tbody>";
		print "</table>";
	}
}
?>
