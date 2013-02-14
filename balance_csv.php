<?php

/*
 * This php is called directly with POST to get verified attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';

global $wpdb;
global $current_user;

$Filename = "KarmaBalances.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if ( wp_verify_nonce($_POST['karma-balance-csv-verify-key'], 'karma-balance-csv-verify-key') ) {


	if (current_user_can('read_private_posts')) {

	/*
        foreach ($results as $row) {
                $separator = "";
                foreach ($row as $fieldname=>$field) {
			echo $separator;

			// handle NULL
			if ($field != NULL) {
				// escape " character in field
				$field = str_replace("\"", "\"\"", $field);
				// strip newlines in field
				$field = str_replace(array('\n', '\r'), " ", $field);

			}
			echo "\"" . stripslashes($field) . "\"";
			$separator = ",";
                }
                echo "\n";
        }
	*/
	} else {
		echo '"security failure", "permissions"';
	}

} else {
	echo '"security failure", "nonce"';
}

?>
