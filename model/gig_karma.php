<?php

/*
 *  gig karma
 *
 * In the past this was represented by an sql view, like this:

		CREATE VIEW 
			wp_gig_karma
		AS
		SELECT 
			wp_gig_attendance.gigid AS gigid,
			wp_posts.post_title AS title,
			wp_gig_attendance.user_id AS userid,
			wp_postmeta.meta_value AS date,
			POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , wp_postmeta.meta_value) ) ) AS karma 
		FROM 
			wp_gig_attendance, wp_postmeta, wp_posts
		WHERE 
			wp_gig_attendance.gigid = wp_postmeta.post_id
			AND wp_gig_attendance.gigid = wp_posts.ID
			AND wp_postmeta.meta_key =  "cbg_date"
			AND ( wp_gig_attendance.deleted IS NULL OR  wp_gig_attendance.deleted = 0 )

 *
 */       
class carnieKarmaGigKarma {

	function get_rows($user_id) {
		global $wpdb;

		$posts_name = $wpdb->prefix . "posts";
		$postmeta_name = $wpdb->prefix . "postmeta";
		$gig_attendance_name = $wpdb->prefix . "gig_attendance";

		$sql = $wpdb->prepare("
			SELECT 
				$gig_attendance_name.gigid AS gigid,
				$posts_name.post_title AS title,
				$gig_attendance_name.user_id AS userid,
				$postmeta_name.meta_value AS date,
				POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , $postmeta_name.meta_value) ) ) AS karma 
			FROM 
				$gig_attendance_name, $postmeta_name, $posts_name
			WHERE 
				$gig_attendance_name.user_id  = %d
				AND $gig_attendance_name.gigid = $postmeta_name.post_id
				AND $gig_attendance_name.gigid = $posts_name.ID
				AND $postmeta_name.meta_key =  \"cbg_date\"
				AND ( $gig_attendance_name.deleted IS NULL OR  $gig_attendance_name.deleted = 0 )
			        ORDER BY date DESC
		", $user_id);

		$results = $wpdb->get_results($sql, ARRAY_A);

		return $results;
	}
}

?>
