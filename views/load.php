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
         * render th
         */
        function render_th($column, $title, $orderBy, $order) {
                $newOrder = $order;
                if ( strcasecmp($order, 'desc') == 0) {
                        $newOrder = 'asc';
                } else {
                        $newOrder = 'desc';
                }
                echo '<th class="manage-column column-';
                echo $column;
                if (strcmp($column, $orderBy) == 0) {
                        echo ' sorted ';
                } else {
                        echo ' sortable ';
                }
                if (strcmp($order, "DESC") == 0) {
                        echo ' desc ';
                } else {
                        echo ' asc ';
                }
                echo '" scope="col">';
?>
                <a href="<?php
                        echo $this->current_request_with_params(
                                array('orderby' => $column, 'order' => $newOrder ) );
?>">
                        <span><?php echo $title; ?></span><span class="sorting-indicator"></span>
                </a>
<?php
                echo "</th>";

        }

        /*
         * render table header/footer
         */
        function render_table_header_footer($orderBy, $order) {
?>
                <tr>
<?php
                        $this->render_th('id', 'Row', $orderBy, $order);
                        $this->render_th('notes', 'Tour', $orderBy, $order);
                        $this->render_th('date', 'Date', $orderBy, $order);
                        $this->render_th('user_id', 'User', $orderBy, $order);
                        $this->render_th('initial_load', 'Inital Karmic Load', $orderBy, $order);
                        $this->render_th('deleted', 'Deleted', $orderBy, $order);
?>
                </tr>
<?php

        }

        /*
         * render a row in karmic load table
         */
        function render_row( $row, $karma_ledger_delete_nonce, $karma_ledger_nonce ) {
		$user_id = $row["user_id"];
		$user_info = get_userdata($user_id);
		$edit_url = $siteurl . '/wp-admin/user-edit.php?user_id=' . $user_id;
                $user_info = get_userdata($user_id);

		$rowid = $row["id"];
                $delete_url = get_admin_url() . "admin.php?page=list-karmic-load&row=$row_id&action=delete&karma_ledger_delete_nonce=$karma_ledger_delete_nonce";

		if ($row['deleted']) {
?>
			<tr class="deleted">
<?php 
		} else {
?>
			<tr>
<?php 
		}

?>
			<td class="id column-id">
<?php 
		echo $row["id"]; 
		if (current_user_can('edit_users') && !($row['deleted'])) {
?>
                                <div class="row-actions">
                                        <span class="trash">
<a href="<?php echo $delete_url; ?>" title="Delete this row">Delete Row</a> </span>
                                </div>

<?php
		}
?>

			</td>
			<td class="post-title page-title column-title">
				<strong>
				<?php echo $row["notes"]; ?>
				</strong>
			</td>
			<td class="date column-date"><?php echo $row["date"]; ?></td>
			<td class="user_id column-user-id">
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
			<td class="inital_load column-inital-load"><?php echo $row["initial_load"]; ?></td>
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
