<?php

/*
 * Renders karmic load ledger table 
 */       
class carnieKarmaLoadView {


        /*
         * render table navigation top or bottom
         */
        function render_table_nav( $position, $all_count, $filtered_count, $limit, $paged ) {
		// TODO
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
?>
		<tr>
			<td><?php echo $row["id"]; ?></td>
			<td><?php echo $row["date"]; ?></td>
			<td><?php echo $row["notes"]; ?></td>
			<td><?php echo $row["user_id"]; ?></td>
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
