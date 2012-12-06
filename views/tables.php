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
					<input type="hidden" name="karma_detail" value="<?php echo $karma_detail; ?>" />
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
}
?>
