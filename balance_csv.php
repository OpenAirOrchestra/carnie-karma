<?php

/*
 * This php is called directly with POST to get verified attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'model/multipliers.php';
require_once 'model/gig_karma.php';
require_once 'model/karmic_load.php';
require_once 'model/workshop_karma.php';

global $wpdb;
global $current_user;

$Filename = "KarmaBalances.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

function outputField($field)
{
	if ($field != NULL) {
		$field = str_replace("\"", "\"\"", $field);
		$field = str_replace(array('\n', '\r'), " ", $field);
	}
	echo "\"" . stripslashes($field) . "\"";
	echo ",";	
}

// Verify nonce.
if ( wp_verify_nonce($_POST['karma-balance-csv-verify-key'], 'karma-balance-csv-verify-key') ) {

	if (current_user_can('read_private_posts')) {

		$users = carnieKarma::users();
		foreach ($users as $user) {
			if ($user['ID'] != 1) {
				$user_id = $user['ID'];
			
				$user_info = get_userdata($user['ID']);

				// user id
				outputField($user['ID']);

				// user nicename
				outputField($user['user_nicename']);

				// user firstname
				outputField($user['first_name']);

				// user lastname
				outputField($user['last_name']);

				// Get summary data For workshops
				$workshopKarma = new carnieKarmaWorkshopKarma;
				$workshop_karma_rows = $workshopKarma->get_rows($user_id);
				$workshop_count = count($workshop_karma_rows);
				$workshop_karma = array_reduce($workshop_karma_rows, function(&$res, $item) {
							return $res + doubleval($item['karma']) * CARNIE_KARMA_WORKSHOP_MULTIPLIER;
							}, 0.0);
				

				// Get summary data for karmic load
				$karmicLoad = new carnieKarmaKarmicLoad;
				$karmic_load_rows = $karmicLoad->get_rows($user_id);
				$karmic_load_count = count($karmic_load_rows);
				$karmic_load = array_reduce($karmic_load_rows, function(&$res, $item) {
							return $res + doubleval($item['karma']) * CARNIE_KARMA_LOAD_MULTIPLIER;
							}, 0.0);

				// Get summary data For gigs 
				$gigKarma = new carnieKarmaGigKarma;
				$gig_karma_rows = $gigKarma->get_rows($user_id);
				$gig_karma_count = count($gig_karma_rows);
				$gig_karma = array_reduce($gig_karma_rows, function(&$res, $item) {
							return $res + doubleval($item['karma']) * CARNIE_KARMA_GIG_MULTIPLIER;
							}, 0.0);

				$total = $gig_karma + $workshop_karma - $karmic_load;
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
