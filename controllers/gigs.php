<?php

/*
 * Controller for gig karma
 */       
class carnieKarmaGigsController {
 
	/*
	 * Does a detailed report of workshop karma for a user.
	 */
	function report($user_id) {

		global $wpdb;

		$gig_karma_view_name = $wpdb->prefix . "gig_karma";

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
			SELECT gigid, title, userid, date,
				(karma * %d) AS karma 
			  FROM  $gig_karma_view_name
			  WHERE  userid = %d
			  ORDER BY date DESC
			  LIMIT %d, %d
			",
			CARNIE_KARMA_GIG_MULTIPLIER,
			$user_id, $offset, $limit
			);

		$results = $wpdb->get_results($sql, ARRAY_A);

		// what's to total count of all gigs?
		$sql = $wpdb->prepare(
			"
			SELECT COUNT(*)
			  FROM  $gig_karma_view_name
			  WHERE  userid = %d
			",
			$user_id
			);
		$count = $wpdb->get_var($sql);

		$gigsView = new carnieKarmaGigsView;
		$gigsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
