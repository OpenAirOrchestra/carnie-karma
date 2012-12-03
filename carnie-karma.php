<?php
/*
Plugin Name: Carnie Karma 
Plugin URI: http://www.thecarnivalband.com
Description: A plugin to calculate and display participation Karma for The Carnival Band
Version: 0.1
Author: DarrylF
Author URI: http://www.thecarnivalband.com
License: GPL2
*/
?>
<?php
/*  Copyright 2012  DarrylF (email : oaowebmonkey@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$include_folder = dirname(__FILE__);

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

require_once $include_folder . '/views/users.php';
require_once $include_folder . '/views/user.php';
require_once $include_folder . '/version.php';

/*
 * Main class for Carnie Karma Handles activation, hooks, etc.
 */
class carnieKarma {

        /*
         * Plugin is being activated
         * Here we will create table views needed for karma calculation
         */
        function activate() {
                $version = get_option("carniekarma_db_version");

		if ($version) {
			// Do upgrades

			update_option("carniekarma_db_version", CARNIE_KARMA_DB_VERSION);
		} else {
			// Create views
                        global $wpdb;

                        // Create table for verified attendees
                        $workshop_karma_view_name = $wpdb->prefix . "workshop_karma";
                        $workshops_name = $wpdb->prefix . "workshops";
                        $workshop_attendance_name = $wpdb->prefix . "workshop_attendance";

			$sql = "CREATE VIEW " .
				$workshop_karma_view_name . 
				" AS SELECT " .
				$workshops_name . ".id AS workshop_id, " .
				$workshops_name . ".title AS title, " .
				$workshops_name . ".date AS date, " .
				$workshop_attendance_name . ".user_id AS user_id, " .
				"POW(0.998 , ( DATEDIFF( CURRENT_DATE( ) , " . $workshops_name . ".date ) ) ) AS karma " .
				"FROM " .
				$workshops_name . " , " . $workshop_attendance_name . " " . 
				"WHERE " .
				$workshops_name . ".id = " . $workshop_attendance_name . ".workshopid ";


			/*
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
			*/
			$wpdb->query($sql);
			
			add_option("carniekarma_db_version", CARNIE_KARMA_DB_VERSION);
		}

	}

	/*
	 * Renders a list of users, each linking to a karma repors
sss for that user.
	 */
	function list_users() {
		$users = get_users('orderby=nicename');
		$usersView = new carnieKarmaUsersView;
		$usersView->render($users);
	}

	/*
	 * Renders karma for a user, linking to detailed karma reports
	 */
	function render_user($user_id) {
		
		global $wpdb;

                $karma_list_nonce = $_REQUEST['karma_list_nonce'];
                if ( wp_verify_nonce($karma_list_nonce, 'karma_list_nonce') ) {
			$workshop_karma_view_name = $wpdb->prefix . "workshop_karma";

			// Get summary data
			$sql = $wpdb->prepare(
				"
				SELECT COUNT(  workshop_id ) AS workshops , SUM(  karma ) AS workshop_karma
				  FROM  $workshop_karma_view_name
				  WHERE  user_id = %d
				",
				$user_id
				);
			$row = $wpdb->get_row($sql, ARRAY_A);
			$userView = new carnieKarmaUserView;
			$userView->render($user_id, $row['workshops'], $row['workshop_karma']);
		} else {
			echo "<h2>Security error: nonce</h2>";
		}

		$this->explain_karma();
	}

	/*
	 * Do a detailed karma of a type for a user
	 */
	function detail($user_id, $type) {
                $karma_detail_nonce = $_REQUEST['karma_detail_nonce'];
                if ( wp_verify_nonce($karma_detail_nonce, 'karma_detail_nonce') ) {

			$karma_detail = $_REQUEST['karma_detail'];

			if (strcmp($karma_detail, "workshop") == 0) {
				echo "<h2>Workshop Participation Karma For </h2>";
			} else if (strcmp($karma_detail, "gig") == 0) {
				echo "<h2>Gig Participation Karma For </h2>";
			} else if (strcmp($karma_detail, "load") == 0) {
				echo "<h2>Karma Load For </h2>";
			} else {
				echo "<h2>Error, unknown karma detail type</h2>";
			}


		} else {
			echo "<h2>Security error: nonce</h2>";
		}
	}

        /*
         * Handles carniekarma shortcode
         * examples:
         * [carniekarma]
         */
        function carniekarma_shortcode_handler($atts, $content=NULL, $code="") {

		/*
                extract( shortcode_atts( array(
                        'time' => 'all',
                        'display' => 'short'), $atts ) );
		*/

		$current_user = wp_get_current_user();
		$user_id = 0;
		
		if (! current_user_can('read_private_posts')) {
			$user_id = $current_user->ID;
		} else if ($_REQUEST['user_id']) {
			$user_id = $_REQUEST['user_id'];
		}

		if ($_REQUEST['karma_detail']) {
			$this->detail($user_id, $_REQUEST['karma_detail']);
		} else if ($user_id) {
			$this->render_user($user_id);
		} else {
			$this->list_users();
		}
	}

        /*
	 * prints a short explanation of Karnie Karmic Decay
         */
        function explain_karma() {
?>
	<h2>Participation Karma Explained</h2>

	<p>
	Participation Karma is a beautiful mystery.  It accrues with
	participation in gigs and workshops, so as you participate, you
	get more karma.  It evaporates (sublimes?)
	over time, so what you have done lately matters more than
	what you have done in the distant past. 
	Karma from gigs and karma from workshops is wieghted differently.
	</p>
	<p>
	When the band funds
	you on tour, it incurrs karmic load, which weighs against
	karma accrued.  Karmic load, too, evaporates over time.
	</p>

	<p>
	Karma and karmic load evaporate gradually, with a half life
	of about a year.  The math looks like this: 
	<blockquote>
	<code> 0.998 <sup>elapsed days</sup> </code>
	</blockquote>
	</p>

	<p>
	Here is a pretty graph of how a single dew-drop of karma
	dwindles over a three year span of days.
	</p>

	<a href="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images/decay.jpg'; ?>">
	<img src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images/decay.jpg'; ?>"/>
	</a>
	

<?php
	}

};

// instantiate class
$CARNIEKARMA = new carnieKarma;

// add_action('admin_menu', array($CARNIEKARMA, 'create_admin_menu'));

// activation hook
register_activation_hook(__FILE__, array($CARNIEKARMA, 'activate'));

// shortcodes
add_shortcode('carniekarma', array($CARNIEKARMA, 'carniekarma_shortcode_handler'));


?>
