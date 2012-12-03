<?php

/*
 * Renders a workshop karma report for a user.
 */       
class carnieKarmaWorkshopsView {
 
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

		echo "<table>";
		foreach ($workshops as $workshop) {
			echo "<tr>";
			echo "<td>" .  $workshop['date'] . " </td>";
			echo "<td>" .  $workshop['title'] . " </td>";
			echo "<td>" .  $workshop['karma'] . " </td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}
?>
