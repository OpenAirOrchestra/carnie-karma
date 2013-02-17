<?php

/*
 * Controller for karmic load
 */       
class carnieKarmaLoadController {
	
	/*
	 * processes post information from karmic load ledger (admin)
	 */
	function process_post() {
		$post_errors = array();

		if ($_POST['action'] == 'add') {
			$user_id = $_POST['user_id'];
			$notes = $_POST['notes'];
			$initial_load = $_POST['initial_load'] / CARNIE_KARMA_LOAD_MULTIPLIER;

			$time = strtotime($_POST['date']);
			if ($time) {
				$date = date("Y-m-d", $time);
			} else {
				$post_errors['date'] = "Invalid date";
			}

			if (! $user_id) {
				$post_errors['user_id'] = "User is a required field";
			}
			if (! $notes) {
				$post_errors['notes'] = "Notes is a required field";
			}
			if (! $initial_load) {
				$post_errors['initial_load'] = "Initial Load is a required field";
			}
			
			if (count($post_errors) == 0) {

				global $wpdb;

				// Add the record
		                $table_name = $wpdb->prefix . "karmic_load_ledger";
				$wpdb->insert(
                                        $table_name,
                                        array(
                                                'user_id'=>$user_id,
                                                'date'=>$date,
                                                'notes'=>$notes,
                                                'initial_load'=>$initial_load,
                                        ),
                                        array(
                                                '%d',
                                                '%s',
                                                '%s',
                                                '%f'
                                        )
                                );
				$row_id = $wpdb->insert_id;
	
				// add the metadata
				$meta_table_name = $wpdb->prefix . "karmic_loadmeta";

				$wpdb->insert(
					$meta_table_name,
					array(
						'load_id'=>$row_id,
						'meta_key'=>'create_date',
						'meta_value'=>date("Y-m-d")
					),
					array(	
						'%d',
						'%s',
						'%s'
					)
				);
				$current_user = wp_get_current_user();
				$wpdb->insert(
					$meta_table_name,
					array(
						'load_id'=>$row_id,
						'meta_key'=>'created_by',
						'meta_value'=>$current_user->ID
					),
					array(	
						'%d',
						'%s',
						'%s'
					)
				);
			}
		}
		return $post_errors;
	}
 
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
			$limit = 15;
		}
		$offset = $limit * ($paged - 1);

		// Get all the data (paged view will come later)

		$sql = $wpdb->prepare(
			"
			SELECT id, notes, userid, date, 
			  ( %d * initial_load) AS initial_load,
			  ( %d * karma) AS karma
			  FROM  $karmic_load_view_name
			  WHERE  userid = %d
			  ORDER BY date DESC
			  LIMIT %d, %d
			",
			CARNIE_KARMA_LOAD_MULTIPLIER,
			CARNIE_KARMA_LOAD_MULTIPLIER,
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
