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

		return NULL;
	}
}

?>
