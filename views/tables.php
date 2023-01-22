<?php

/*
 * Utilities for rendering a paged table.
 */       
class carnieKarmaTableView {

	/*
	* render table navigation top
	*/
	function render_table_nav( $position, $karma_detail, $karma_detail_nonce, $user_id, $count, $paged, $limit ) {

		$total_pages = ceil($count / $limit);

		$first_page = 1;
		$prev_page = $first_page;

	        if ($paged > 1) {
			$prev_page = $paged - 1;
		}
		$last_page = $total_pages;
		$next_page = $last_page;

		if ($paged < $total_pages) {
			$next_page = $paged + 1;
		}

		return "
		<div class=\"tablenav " . $position . "\">
			<div class=\"tablenav-pages\">
	                <form method=\"post\" action=\"" . $_SERVER["REQUEST_URI"] . "\">
				<span class=\"displaying-num\">" . $count . " items</span>
				<span class=\"pagination-links\">

		            <input type=\"hidden\" name=\"user_id\" value=\"" . $user_id . "\" />
					<input type=\"hidden\" name=\"karma_detail\" value=\"" . $karma_detail . "\" />
		            <input type=\"hidden\" name=\"karma_detail_nonce\" value=\"" . $karma_detail_nonce . "\" />

					<input type=\"hidden\" name=\"first-page\" value=\"" . $first_page . "\" />
					<input type=\"hidden\" name=\"previous-page\" value=\"" . $prev_page . "\" />
					<input type=\"hidden\" name=\"next-page\" value=\"" . $next_page . "\" />
					<input type=\"hidden\" name=\"last-page\" value=\"" . $last_page . "\" />

					<input type=\"submit\" name=\"submit-first-page\" value=\"&laquo;\" />
					<input type=\"submit\" name=\"submit-previous-page\" value=\"&lsaquo;\" />

					 <span class='current-pages'>
					" . $paged . "
					</span>
					 of <span class='total-pages'>
					 " .  $total_pages . "</span>

					<input type=\"submit\" name=\"submit-next-page\" value=\"&rsaquo;\" />
					<input type=\"submit\" name=\"submit-last-page\" value=\"&raquo;\" />
				</span>

			</form>
			</div>
		</div>
		";
	}
}
?>
