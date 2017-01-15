<?php

/*
 * Controller for gig karma
 */       
class carnieKarmaGigsController {
 
	/*
	 * Does a detailed report of workshop karma for a user.
	 */
	function report($user_id) {

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

		$gigKarma = new carnieKarmaGigKarma;
		$gig_karma_rows = $gigKarma->get_rows($user_id);

		$count = count($gig_karma_rows);
		$results = array_slice($gig_karma_rows, $offset, $limit);

		$gigsView = new carnieKarmaGigsView;
		$gigsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
