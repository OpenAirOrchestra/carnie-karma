<?php

/*
 * Controller for workshop karma
 */       
class carnieKarmaWorkshopsController {
 
	/*
	 * Does a detailed report of workshop karma for a user.
	 */
	function report($user_id) {

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

		$workshopKarma = new carnieKarmaWorkshopKarma;
		$workshop_karma_rows = $workshopKarma->get_rows($user_id);

		$count = count($workshop_karma_rows);
		$results = array_slice($workshop_karma_rows, $offset, $limit);

		$workshopsView = new carnieKarmaWorkshopsView;
		$workshopsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
