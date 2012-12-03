<?php

/*
 * Renders a workshop karma report for a user.
 */       
class carnieKarmaWorkshopsView {

	/*
	 * TODO
  	 */
	function current_request_with_params() {
		return "TODO";
	}

	/*
         * render table navigation top
         */
        function render_table_nav( $position, $count, $paged, $limit ) {

		$total_pages = ceil($count / $limit);

		$first_page = $this->current_request_with_params( array('paged' => 1 ) );

		$prev_page = $first_page;

	        if ($paged > 1) {
			$prev_page = $this->current_request_with_params( 
				array('paged' => $paged - 1 ) );
		}
		$last_page = $this->current_request_with_params(
			array('paged' => $total_pages ) );
		$next_page = $last_page;
		if ($paged < $total_pages) {
			$next_page = $this->current_request_with_params( 
				array('paged' => $paged + 1 ) );
		}
?>
		<div class="tablenav <?php echo $position; ?>">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo $count; ?> items</span>
				<span class="pagination-links">
					<a class="first-page" title="Go to the first page" href="<?php echo $first_page; ?>">&laquo;</a>
					<a class="prev-page" title="Go to the previous page" href="<?php echo $prev_page?>">&lsaquo;</a>
					 <span class='current-pages'>
					 <?php echo $paged; ?>
					</span>
					 of <span class='total-pages'>
					 <?php echo $total_pages; ?> </span>

					<a class="next-page" title="Go to the next page" href="<?php echo $next_page; ?>">&rsaquo;</a>
					<a class="last-page" title="Go to the last page" href="<?php echo $last_page; ?>">&raquo;</a>
				</span>
			</div>
			<br class="clear"/>
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


		$this->render_table_nav("top", $count, $paged, $limit);

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


		$this->render_table_nav("top", $count, $paged, $limit);

	}
}
?>
