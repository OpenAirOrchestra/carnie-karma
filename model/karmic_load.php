<?php

/*
 *  karmic_load
 *
 * In the past this was represented by an sql view, like this:

		$karma_load_view_name = $wpdb->prefix . "karmic_load";

			CREATE VIEW 
				$karma_load_view_name
			AS
			SELECT 
				$karma_load_table_name.id AS id,
				$karma_load_table_name.notes AS notes,
				$karma_load_table_name.user_id AS userid,
				$karma_load_table_name.date AS date,
				$karma_load_table_name.initial_load AS initial_load,
				( $karma_load_table_name.initial_load * POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , $karma_load_table_name.date ) ) ) ) AS karma 
			FROM 
				$karma_load_table_name
			WHERE 
				$karma_load_table_name.deleted IS NULL OR  $karma_load_table_name.deleted = 0 
 *
 */       
class carnieKarmaKarmicLoad {

	function get_rows($user_id) {
		global $wpdb;
		$karma_load_table_name = $wpdb->prefix . "karmic_load_ledger";

		$sql = $wpdb->prepare("
			SELECT 
				$karma_load_table_name.id AS id,
				$karma_load_table_name.notes AS notes,
				$karma_load_table_name.user_id AS userid,
				$karma_load_table_name.date AS date,
				$karma_load_table_name.initial_load AS initial_load,
				( $karma_load_table_name.initial_load * 
                                  POW(0.998 , 
 				      GREATEST( DATEDIFF( CURRENT_DATE( ) , $karma_load_table_name.date ) , 0) 
 				   ) ) 
                                AS karma 
			FROM 
				$karma_load_table_name
			WHERE 
				$karma_load_table_name.user_id = %d
				AND $karma_load_table_name.deleted IS NULL OR  $karma_load_table_name.deleted = 0 
		", $user_id);

		$results = $wpdb->get_results($sql, ARRAY_A);

		return $results;
	}
}

?>
