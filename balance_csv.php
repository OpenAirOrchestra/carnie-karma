<?php

/*
 * This php is called directly with POST to get verified attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'model/multipliers.php';

global $wpdb;
global $current_user;

$Filename = "KarmaBalances.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if ( wp_verify_nonce($_POST['karma-balance-csv-verify-key'], 'karma-balance-csv-verify-key') ) {

	if (current_user_can('read_private_posts')) {

		$users = carnieKarma::users();
		foreach ($users as $user) {
			if ($user['ID'] != 1) {
			
				$user_info = get_userdata($user['ID']);

				// user id
				echo "$user['ID']" . ",";

				// user nicename
				$field = $user['user_nicename'];
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
				$field = $user_info->last_name;
				if ($field != NULL) {
					$field = str_replace("\"", "\"\"", $field);
					$field = str_replace(array('\n', '\r'), " ", $field);
				}
				echo "\"" . stripslashes($field) . "\"";
				echo ",";

				// balance
				$workshop_karma_view_name = $wpdb->prefix . "workshop_karma";
				$gig_karma_view_name = $wpdb->prefix . "gig_karma";
				$karma_load_view_name = $wpdb->prefix . "karmic_load";

				// Get summary data For workshops
				$sql = $wpdb->prepare(
					"
					SELECT ( %d * SUM(  karma ) ) AS workshop_karma
					  FROM  $workshop_karma_view_name
					  WHERE  user_id = %d
					",
					CARNIE_KARMA_WORKSHOP_MULTIPLIER,
					$user['ID']
					);

				$workshop_row = $wpdb->get_row($sql, ARRAY_A);

				// Get summary data For gigs
				$sql = $wpdb->prepare(
					"
					SELECT (%d * SUM(  karma )) AS gig_karma
					  FROM  $gig_karma_view_name
					  WHERE  userid = %d
					",
					CARNIE_KARMA_GIG_MULTIPLIER,
					$user['ID']
					);
				$gig_row = $wpdb->get_row($sql, ARRAY_A);

				// Get summary data For karmic load
				$sql = $wpdb->prepare(
					"
					SELECT (%d * SUM(  karma )) AS karmic_load
					  FROM  $karma_load_view_name
					  WHERE  userid = %d
					",
					CARNIE_KARMA_LOAD_MULTIPLIER,
					$user['ID']
					);
				$load_row = $wpdb->get_row($sql, ARRAY_A);

				$total = $gig_row['gig_karma'] + $workshop_row['workshop_karma'] - $load_row['karmic_load'];
				echo $total;
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
