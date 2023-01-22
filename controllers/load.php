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

		// Get paged and limit
		$paged = 1;
		if (isset($_REQUEST['paged'])) {
			$paged = $_REQUEST['paged'];
		}

		if (isset($_REQUEST['submit-first-page'])) {
			$paged = $_REQUEST['first-page'];
		} else if (isset($_REQUEST['submit-previous-page'])) {
			$paged = $_REQUEST['previous-page'];
		} else if (isset($_REQUEST['submit-next-page'])) {
			$paged = $_REQUEST['next-page'];
		} else if (isset($_REQUEST['submit-last-page'])) {
			$paged = $_REQUEST['last-page'];
		} 

		$limit = 15;
		if (isset($_REQUEST['limit'])) {
			$limit = $_REQUEST['limit'];
		}
		
		$offset = $limit * ($paged - 1);

		// Get all the data (paged view will come later)
		$karmicLoad = new carnieKarmaKarmicLoad;
		$karmic_load_rows = $karmicLoad->get_rows($user_id);

		$count = count($karmic_load_rows);
		$results = array_slice($karmic_load_rows, $offset, $limit);

		$gigsView = new carnieKarmaLoadDetailsView;
		return $gigsView->render($user_id, $results, $count, $paged, $limit);
	}
}
?>
