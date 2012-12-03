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

		// Get all the data (paged view will come later)

		$sql = $wpdb->prepare(
			"
			SELECT *
			  FROM  $workshop_karma_view_name
			  WHERE  user_id = %d
			  ORDER BY date DESC
			",
			$user_id
			);

		$results = $wpdb->get_results($sql, ARRAY_A);

		$workshopsView = new carnieKarmaWorkshopsView;
		$workshopsView->render($user_id, $results);
	}
}
?>
