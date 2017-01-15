<?php

/*
 *  workshop karma
 *
 * In the past this was represented by an sql view, like this:

		CREATE VIEW 
			wp_workshop_karma
		AS
		SELECT 
			wp_workshops.id AS workshop_id, 
			wp_workshops.title AS title, 
			wp_workshops.date AS DATE, 
			wp_workshop_attendance.user_id AS user_id, 
			POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , wp_workshops.date ) )) AS karma
		FROM 
			wp_workshops, wp_workshop_attendance
		WHERE 
			wp_workshop_attendance.workshopid = wp_workshops.id
 *
 */       
class carnieKarmaWorkshopKarma {

	function get_rows($user_id) {
		global $wpdb;

		$workshops_name = $wpdb->prefix . "workshops";
		$workshop_attendance_name = $wpdb->prefix . "workshop_attendance";

		$sql = $wpdb->prepare(
			"SELECT " .
			$workshops_name . ".id AS workshop_id, " .
			$workshops_name . ".title AS title, " .
			$workshops_name . ".date AS date, " .
			$workshop_attendance_name . ".user_id AS user_id, " .
			"POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , " . $workshops_name . ".date ) ) ) AS karma " .
			"FROM " .
			$workshops_name . " , " . $workshop_attendance_name . " " . 
			"WHERE " .
			$workshop_attendance_name . ".user_id = %d " .
			"AND " .
			$workshops_name . ".id = " . $workshop_attendance_name . ".workshopid " .
			"ORDER BY date DESC",
			$user_id);

		$results = $wpdb->get_results($sql, ARRAY_A);

		return $results;
	}
}

?>
