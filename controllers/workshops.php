<?php

/*
 * Controller for workshop karma
 */       
class carnieKarmaWorkshopsController {
 
	/*
	 * Does a detailed report of workshop karma for a user.
	 */
	function report($user_id) {

		global $wpdb;

		$workshop_karma_view_name = $wpdb->prefix . "workshop_karma";

		// Get paged and limit
		$paged = $_REQUEST['paged'];
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
			  FROM  $workshop_karma_view_name
			  WHERE  user_id = %d
			  ORDER BY date DESC
			  LIMIT %d, %d
			",
			$user_id, $offset, $limit
			);

		$results = $wpdb->get_results($sql, ARRAY_A);

		// what's to total count of all workshops?
		$sql = $wpdb->prepare(
			"
			SELECT COUNT(*)
			  FROM  $workshop_karma_view_name
			  WHERE  user_id = %d
			",
			$user_id
			);
		$count = $wpdb->get_var($sql);

		$workshopsView = new carnieKarmaWorkshopsView;
		$workshopsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
