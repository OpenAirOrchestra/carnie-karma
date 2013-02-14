<?php

/*
 * This php is called directly with POST to get verified attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';

global $wpdb;
global $current_user;

$Filename = "KarmaBalances.csv";
// header("Content-Type: text/csv");
// header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if ( wp_verify_nonce($_POST['karma-balance-csv-verify-key'], 'karma-balance-csv-verify-key') ) {

	if (current_user_can('read_private_posts')) {

		$users = get_users('orderby=nicename');
		foreach ($users as $user) {
			if ($user->ID != 1) {
			
				$user_info = get_userdata($user->ID);

				// user id
				echo '$user->ID' . ',';

				// user nicename
				$field = $user->nicename;
				if ($field != NULL) {
					$field = str_replace("\"", "\"\"", $field);
					$field = str_replace(array('\n', '\r'), " ", $field);
				}
				echo "\"" . stripslashes($field) . "\"";
				echo ",";

				// user firstname
				$field = $user_info->first_name;
				if ($field != NULL) {
					$field = str_replace("\"", "\"\"", $field);
					$field = str_replace(array('\n', '\r'), " ", $field);
				}
				echo "\"" . stripslashes($field) . "\"";
				echo ",";

				// user lastname
				$field = $user_info->first_name;
				if ($field != NULL) {
					$field = str_replace("\"", "\"\"", $field);
					$field = str_replace(array('\n', '\r'), " ", $field);
				}
				echo "\"" . stripslashes($field) . "\"";
				echo ",";

				// balance

                		echo "\n";
			}
		}

	} else {
		echo '"security failure", "permissions"';
	}

} else {
	echo '"security failure", "nonce"';
}

?>
