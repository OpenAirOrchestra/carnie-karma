<?php

/*
 * Renders karmic load ledger table 
 */       
class carnieKarmaLoadView {

        /*
         * Given an array of query parameters returns the current request
         * url with given query parameters added/replaced
         */
        function current_request_with_params($params) {
                $all_params = $_GET;
                foreach ($params as $key => $value) {
                        $all_params[$key] = $value;
                }
                $request = $_SERVER['REQUEST_URI'];
                $query = $_SERVER['QUERY_STRING'];
                if (strlen($query)) {
                        $request = str_replace( $query, "", $request);
                }
                $sep = "";
                foreach ($all_params as $key => $value) {
                        $request = $request . $sep;
                        $sep = '&';
                        $request = $request . $key . "=" . $value;
                }

                return $request;
        }

        /*
         * render table navigation top
         */
        function render_table_nav( $position, $all_count, $filtered_count, $limit, $paged ) {
                        $total_pages = ceil($filtered_count / $limit);

                        $first_page = $this->current_request_with_params(
                                array('paged' => 1 ) );

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
                                        <span class="displaying-num"><?php echo $filtered_count; ?> items</span>
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
        function render_table_header_footer($orderBy, $order) {
		// TODO
        }

        /*
         * render a row in workshop table
         */
        function render_row( $row, $karma_ledger_delete_nonce, $karma_ledger_nonce ) {
		$user_id = $row["user_id"];
		$user_info = get_userdata($user_id);
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;
                $user_info = get_userdata($user_id);

?>
		<tr>
			<td><?php echo $row["id"]; ?></td>
			<td><?php echo $row["date"]; ?></td>
			<td><?php echo $row["notes"]; ?></td>
			<td>
<?php
		if ($user_info->first_name || $user_info->last_name) {
			echo $user_info->first_name . ' ' . $user_info->last_name;
			echo ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				" " . $user_id .
				'</a>)';
		} else {
			echo $user->display_name;
			echo ' (<a href="' . $edit_url . '">' .
				$user_info->user_login . 
				" " . $user_id .
				'</a>)';
		}
?>
			</td>
			<td><?php echo $row["initial_load"]; ?></td>
			<td><?php if ($row['deleted']) { echo "Deleted"; } ?></td>
		</tr>
<?php
	}

        /*
         * render karmic load ledger table body
         */
        function render_table_body( $rows ) {
                echo '          <tbody id="the-list">';

                $karma_ledger_delete_nonce = wp_create_nonce('karma_ledger_delete_nonce');
                $karma_ledger_nonce = wp_create_nonce('karma_ledger_nonce');

                foreach ($rows as $row)
                {
                        $this->render_row($row, $karma_ledger_delete_nonce, $karma_ledger_nonce);
                }
                echo '          </tbody>';
        }


	/*
	 * Render karmic load as a table/ledger
 	 */
	function render_table( $rows, $orderBy, $order, $all_count, $filtered_count, $limit, $paged ) {

		$this->render_table_nav( "top", $all_count, $filtered_count, $limit, $paged );
?>

                        <table class="wp-list-table widefat fixt posts" cellspacing="0">
                                <thead>
                                        <?php $this->render_table_header_footer($orderBy, $order); ?>
                                </thead>
                                <tfoot>
                                        <?php $this->render_table_header_footer($orderBy, $order); ?>
                                </tfoot>
                                <?php $this->render_table_body($rows); ?>

                        </table>


<?php

		$this->render_table_nav( "bottom", $all_count, $filtered_count, $limit, $paged );
	}

}
?>
