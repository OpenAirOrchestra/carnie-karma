<?php

/*
 * This class handles simple rendering the form for exporting to CSV.
 */
class carnieKarmaCsvExportView {

	function exportKarmaBalanceForm() {
		$action = plugins_url( 'balance_csv.php' , dirname(__FILE__) );
?>
		<form name="export_csv_form"
			method="POST"
<?php
		echo 'action="' . $action . '">';
		echo '<input type="hidden" name="karma-balance-csv-verify-key" id="karma-balance-csv-verify-key"
						value="' . wp_create_nonce('karma-balance-csv-verify-key') . '" />';
?>
		<p class="submit"><input type="submit" name="submit" class="button" value="Download Karma Balance File" />
<?php

		print "</form>\n";
	}
}
?>
