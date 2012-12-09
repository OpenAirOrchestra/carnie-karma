<?php

/*
 * Controller for karmic load
 */       
class carnieKarmaLoadController {
 
	/*
	 * Does a detailed report of karmic load for a user.
	 */
	function report($user_id) {

		global $wpdb;

		$karmic_load_view_name = $wpdb->prefix . "karmic_load";

		// Get paged and limit
		$paged = $_REQUEST['paged'];
		if ($_REQUEST['submit-first-page']) {
			$paged = $_REQUEST['first-page'];
		} else if ($_REQUEST['submit-previous-page']) {
			$paged = $_REQUEST['previous-page'];
		} else if ($_REQUEST['submit-next-page']) {
			$paged = $_REQUEST['next-page'];
		} else if ($_REQUEST['submit-last-page']) {
			$paged = $_REQUEST['last-page'];
		} 

		if (! $paged) {
			$paged = 1;
		}
		$limit = $_REQUEST['limit'];
		if (! $limit) {
			$limit = 30;
		}
		$offset = $limit * ($paged - 1);

		// Get all the data (paged view will come later)

		$sql = $wpdb->prepare(
			"
			SELECT *
			  FROM  $karmic_load_view_name
			  WHERE  userid = %d
			  ORDER BY date DESC
			  LIMIT %d, %d
			",
			$user_id, $offset, $limit
			);

		$results = $wpdb->get_results($sql, ARRAY_A);

		// what's to total count of all gigs?
		$sql = $wpdb->prepare(
			"
			SELECT COUNT(*)
			  FROM  $karmic_load_view_name
			  WHERE  userid = %d
			",
			$user_id
			);
		$count = $wpdb->get_var($sql);

		$gigsView = new carnieKarmaLoadDetailsView;
		$gigsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
