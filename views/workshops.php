<?php

/*
 * Renders a workshop karma report for a user.
 */       
class carnieKarmaWorkshopsView {

	/*
         * render table navigation top
         */
        function render_table_nav( $position, $karma_detail, $karma_detail_nonce, $user_id, $count, $paged, $limit ) {

		$total_pages = ceil($count / $limit);

		$first_paged = 1;
		$prev_page = $first_page;

	        if ($paged > 1) {
			$prev_page = $paged - 1;
		}
		$last_page = $total_pages;
		$next_page = $last_page;

		if ($paged < $total_pages) {
			$next_page = $paged + 1;
		}
?>
		<div class="tablenav <?php echo $position; ?>">
			<div class="tablenav-pages">
	                <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<span class="displaying-num"><?php echo $count; ?> items</span>
				<span class="pagination-links">

		                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
		                        <input type="hidden" name="karma_detail" value="workshop" />
		                        <input type="hidden" name="karma_detail_nonce" value="<?php echo $karma_detail_nonce; ?>" />

					<input type="hidden" name="first-page" value="<?php echo $first_page; ?>" />
					<input type="hidden" name="previous-page" value="<?php echo $prev_page; ?>" />
					<input type="hidden" name="next-page" value="<?php echo $next_page; ?>" />
					<input type="hidden" name="last-page" value="<?php echo $last_page; ?>" />

					<input type="submit" name="submit-first-page" value="&laquo;" />
					<input type="submit" name="submit-previous-page" value="&lsaquo;" />

					 <span class='current-pages'>
					 <?php echo $paged; ?>
					</span>
					 of <span class='total-pages'>
					 <?php echo $total_pages; ?> </span>

					<input type="submit" name="submit-next-page" value="&rsaquo;" />
					<input type="submit" name="submit-last-page" value="&raquo;" />
				</span>

			</form>
			</div>
		</div>
<?php
	}

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

                $karma_detail_nonce = wp_create_nonce('karma_detail_nonce');

		$this->render_table_nav("top", "workshop", $karma_detail_nonce, $user_id, $count, $paged, $limit);

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


		$this->render_table_nav("top", "workshop", $karma_detail_nonce, $user_id, $count, $paged, $limit);

	}
}
?>
