<?php
/**
 * Plugin Name: Carnie Karma 
 * Plugin URI: https://github.com/OpenAirOrchestra/carnie-karma
 * Description: A plugin to calculate and display participation Karma for The Carnival Band
 * Version: 1.1
 * Author: DarrylF
 * Author URI: http://www.thecarnivalband.com
 * License: GPL2
 * GitHub Plugin URI: https://github.com/OpenAirOrchestra/carnie-karma
 **/
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
require_once $include_folder . '/model/gig_karma.php';
require_once $include_folder . '/model/karmic_load.php';
require_once $include_folder . '/model/multipliers.php';
require_once $include_folder . '/model/workshop_karma.php';
require_once $include_folder . '/views/users.php';
require_once $include_folder . '/views/user.php';
require_once $include_folder . '/views/workshops.php';
require_once $include_folder . '/views/gigs.php';
require_once $include_folder . '/views/load_details.php';
require_once $include_folder . '/views/load.php';
require_once $include_folder . '/views/tables.php';
require_once $include_folder . '/views/export_csv_form.php';
require_once $include_folder . '/controllers/workshops.php';
require_once $include_folder . '/controllers/gigs.php';
require_once $include_folder . '/controllers/load.php';
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
			if ($version < CARNIE_KARMA_DB_VERSION) {
	
				if ($version <= 1) {
					$this->migrate_load_ledger_v1();
					$version = 2;
				}

				if ($version <= 2) {
					$this->migrate_views_v2();
					$version = 3;
				}

				// save new db version
				update_option("carniekarma_db_version", CARNIE_KARMA_DB_VERSION);
			}
		} else {
			$this->create_tables();

			add_option("carniekarma_db_version", CARNIE_KARMA_DB_VERSION);
		}

	}

	/*
	 * Update the math for the calculated karmic load to gracefully
	 * handle entries with a future date
	 */
	function migrate_load_ledger_v1() {
		global $wpdb;
		$karma_load_table_name = $wpdb->prefix . "karmic_load_ledger";
		$karma_load_view_name = $wpdb->prefix . "karmic_load";

		/*
		The change is to add in the GREATEST ( blah, 0) as follows:
		wp_karmic_load_ledger.initial_load * 
		POW( 0.998, 
		     GREATEST( DATEDIFF( CURRENT_DATE( ) , wp_karmic_load_ledger.date ), 
		       0))

		 */
		$sql = "
			ALTER VIEW 
			$karma_load_view_name
			AS
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
				$karma_load_table_name.deleted IS NULL OR  $karma_load_table_name.deleted = 0 
		";

		$wpdb->query($sql);
	}

	function migrate_views_v2() {
		global $wpdb;

		// Delete views that are no longer used.
		$workshop_karma_view_name = $wpdb->prefix . "workshop_karma";
		$gig_karma_view_name = $wpdb->prefix . "gig_karma";
		$karma_load_view_name = $wpdb->prefix . "karmic_load";

		$wpdb->query( "
			DROP  VIEW
			$workshop_karma_view_name " );

		$wpdb->query( "
			DROP  VIEW
			$gig_karma_view_name " );

		$wpdb->query( "
			DROP  VIEW
			$karma_load_view_name " );
	}

	/*
		Get list of active users (i.e. current authors)
	*/
	public static function users() {
		global $wpdb;
		$users_name = $wpdb->prefix . "users";
		$usermeta_name = $wpdb->prefix . "usermeta";
		
		$sql = $wpdb->prepare("SELECT u.display_name, u.ID, u.user_email, u.user_login, u.user_nicename
								FROM  `$users_name` u
								JOIN  `$usermeta_name` m ON u.id = m.user_id AND m.meta_key IN ('wp_capabilities')								
								WHERE u.id <> 1 
								AND 
									(
										m.meta_value LIKE '%%author%%'
										OR m.meta_value LIKE '%%editor%%'
										OR m.meta_value LIKE '%%administrator%%'
									)								
								ORDER BY u.user_nicename");
		$users = $wpdb->get_results( $sql, ARRAY_A );
		return $users;
	}
	
	/*
	 * Renders a list of users, each linking to a karma repors
sss for that user.
	 */
	function list_users() {
		$users = carnieKarma::users();
		$usersView = new carnieKarmaUsersView;
		$usersView->render($users);
	}

	/*
	 * Renders karma for a user, linking to detailed karma reports
	 */
	function render_user($user_id) {
		
		global $wpdb;

		$current_user = wp_get_current_user();

                $karma_list_nonce = $_REQUEST['karma_list_nonce'];
                if ( ($user_id == $current_user->ID) ||
		     (wp_verify_nonce($karma_list_nonce, 'karma_list_nonce')) ) {

			// Get summary data For workshops
			$workshopKarma = new carnieKarmaWorkshopKarma;
			$workshop_karma_rows = $workshopKarma->get_rows($user_id);
			$workshop_count = count($workshop_karma_rows);
			$workshop_karma = array_reduce($workshop_karma_rows, function(&$res, $item) {
    						return $res + doubleval($item['karma']) * CARNIE_KARMA_WORKSHOP_MULTIPLIER;
						}, 0.0);
			

			// Get summary data for karmic load
			$karmicLoad = new carnieKarmaKarmicLoad;
			$karmic_load_rows = $karmicLoad->get_rows($user_id);
			$karmic_load_count = count($karmic_load_rows);
			$karmic_load = array_reduce($karmic_load_rows, function(&$res, $item) {
    						return $res + doubleval($item['karma']) * CARNIE_KARMA_LOAD_MULTIPLIER;
						}, 0.0);

			// Get summary data For gigs 
			$gigKarma = new carnieKarmaGigKarma;
			$gig_karma_rows = $gigKarma->get_rows($user_id);
			$gig_karma_count = count($gig_karma_rows);
			$gig_karma = array_reduce($gig_karma_rows, function(&$res, $item) {
    						return $res + doubleval($item['karma']) * CARNIE_KARMA_GIG_MULTIPLIER;
						}, 0.0);

			$userView = new carnieKarmaUserView;
			$userView->render($user_id, 
				$workshop_count, $workshop_karma,
				// $gig_row['gigs'], $gig_row['gig_karma'],
				$gig_karma_count, $gig_karma,
				$karmic_load_count, $karmic_load
			);
		} else {
			echo "<h2>Security error: nonce</h2>";
		}

		// DFDF $this->debug_sql($user_id);

		$this->explain_karma();
	}

	/*
	 * Do a detailed report of workshop karma for a user
	 */
	function workshop_detail($user_id) {
		$workshopsController = new carnieKarmaWorkshopsController;
		$workshopsController->report($user_id);
	}

	/*
	 * Do a detailed report of gig karma for a user
	 */
	function gig_detail($user_id) {
		$gigsController = new carnieKarmaGigsController;
		$gigsController->report($user_id);
	}

	/*
	 * Do a detailed report of karmic load for a user
	 */
	function load_detail($user_id) {
		$loadController = new carnieKarmaLoadController;
		$loadController->report($user_id);
	}

	/*
	 * Do a detailed report of karma of a type for a user
	 */
	function detail($user_id, $type) {
                $karma_detail_nonce = $_REQUEST['karma_detail_nonce'];
                if ( wp_verify_nonce($karma_detail_nonce, 'karma_detail_nonce') ) {

			$karma_detail = $_REQUEST['karma_detail'];

			if (strcmp($karma_detail, "workshop") == 0) {
				$this->workshop_detail($user_id);
			} else if (strcmp($karma_detail, "gig") == 0) {
				$this->gig_detail($user_id);
			} else if (strcmp($karma_detail, "load") == 0) {
				$this->load_detail($user_id);
			} else {
				echo "<h2>Error, unknown karma detail type</h2>";
			}
		} else {
			echo "<h2>Security error: nonce</h2>";
		}
	}

	function export_karma_page() {
                if (!current_user_can('read_private_posts'))  {
                        wp_die( __('You do not have sufficient permissions to access this page.') );
                }

		$exportCsvFormView = new carnieKarmaCsvExportView;

                echo '<div class="wrap">';
                echo "<h2>Export Carnie Karma Totals</h2>";
                echo "<p>When you click the button below WordPress will create a CSV file for you to save to your computer.</p>";
                echo "<p>Once you have saved the download file, you can load  into a spreadsheet program like Excel.</p>";

		$exportCsvFormView->exportKarmaBalanceForm();
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
		} else if (isset($_REQUEST['user_id']) && $_REQUEST['user_id']) {
			$user_id = $_REQUEST['user_id'];
		}

		if (isset($_REQUEST['karma_detail']) && $_REQUEST['karma_detail']) {
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
	</p>
	<p>
	Karma from gigs and karma from workshops is wieghted differently.
	</p>
	<p>
	When the band funds
	you on tour, it incurrs karmic load, which weighs against
	karma accrued.  Karmic load, too, evaporates over time.
	</p>

	<p>
	Karmic balance is the sum of gig and workshop karma, less karmic load.  <br/>
	Gig Karma + Workshop Karma - Karmic Load = Karmic Balance
	</p>

	<p>
	Karma and karmic load evaporate gradually, 
	with a half life of about a year.  
	That means that all karma values diminish to about
	half of their original value after one year has elapsed.
	The math looks like this: 
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
	
	<p>
	Participation Karma has no monetary value.  The amount of karmic load
	incurred for a particlar amount funding is different for every tour.
	</p>

<?php
	}

	/*
         * Process karma load ledger post
         */
        function process_ledger_post() {
		$post_errors = array();
                if (current_user_can('add_users') && array_key_exists('karma_ledger_nonce', $_REQUEST)) {
                        $nonce = $_REQUEST['karma_ledger_nonce'];
                        if ($nonce && wp_verify_nonce($nonce, 'karma_ledger_nonce')) {
                                // Process post
                                $loadController = new carnieKarmaLoadController;
                                $post_errors = $loadController->process_post();
                        }
                }

		return $post_errors;
        }

	/*
     * Create admin menu(s) for this plugin.
     */
	function create_admin_menu() {

		// Add object page
		$page = add_menu_page('Karmic Load', 'Karmic Load', 'read', 'list-karmic-load', array($this, 'list_karmic_load'), plugins_url('images/karma16.png', __FILE__));

		// Add tools page
		add_management_page('Export Carnie Karma', 'Export Carnie Karma', 'read_private_posts', 'export-carnie-karma-tools', array($this, 'export_karma_page'));
	}

	   
	/*
 	 * Enqueue admin stylesheet	
     */
	function admin_styles($hook) {
		if ('toplevel_page_list-karmic-load' != $hook) 
			return;

	       wp_register_style( 'carnieKarmaAdminStylesheet', plugins_url('css/admin.css', __FILE__) );
	       wp_enqueue_style( 'carnieKarmaAdminStylesheet' );
	}

	/*
	 * Delete a row in the karma load table
	 */
	function delete_ledger_row($row_id) {

		global $wpdb;
		$table_name = $wpdb->prefix . "karmic_load_ledger";

                $sql = $wpdb->prepare("
			SELECT *
			FROM `$table_name` 
			WHERE id = %d 
			", $row_id);

		$row = $wpdb->get_row($sql, ARRAY_A);

		if ($row && !$row['deleted']) {


			// Delete the Row by setting 'deleted' to 1
			$wpdb->update( $table_name,
				array('deleted' => 1),
				array('id' => $row_id),
				array('%d'),
				array('%d')
			);
				

			// Add delete metadata
			$meta_table_name = $wpdb->prefix . "karmic_loadmeta";

			$wpdb->insert(
				$meta_table_name,
				array(
					'load_id'=>$row_id,
					'meta_key'=>'delete_date',
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
					'meta_key'=>'deleted_by',
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
	 
        /*
         * Create tools page that lists karmic load.
	 * This is the karmic load ledger.
         */
        function list_karmic_load() {

?>
        <div class="wrap">
                <div id="icon-edit" class="icon32"><br/></div>
                <h2>Karmic Load Ledger</h2>
<?php

		$post_errors = $this->process_ledger_post();

		 global $wpdb;

                 if ( array_key_exists("action", $_REQUEST) && array_key_exists("karma_ledger_delete_nonce", $_REQUEST) && strcasecmp($_REQUEST["action"], 'delete') == 0) {

                        $delete_nonce = $_REQUEST["karma_ledger_delete_nonce"];
                        $id = $_GET["row"];
                        if ($id && $delete_nonce &&
                                wp_verify_nonce($delete_nonce, "karma_ledger_delete_nonce")) {
                                $this->delete_ledger_row($id);

                        }
                 }

		// orderby=date&order=desc

                $orderBy = 'id';
                $order = 'DESC';
                if ( array_key_exists("orderby", $_REQUEST) && (
						strcasecmp($_REQUEST["orderby"], 'title') == 0 ||
                        strcasecmp($_REQUEST["orderby"], 'user_id') == 0 ||
                        strcasecmp($_REQUEST["orderby"], 'initial_load') == 0 ||
                        strcasecmp($_REQUEST["orderby"], 'deleted') == 0 ||
                        strcasecmp($_REQUEST["orderby"], 'id') == 0 ||
                        strcasecmp($_REQUEST["orderby"], 'date') == 0)) {
                        $orderBy = strtolower($_REQUEST["orderby"]);
                }
                if ( array_key_exists("order", $_REQUEST) && strcasecmp($_REQUEST["order"], 'asc') == 0) {
                        $order = "ASC";
                }

                $paged = 1;
                if (array_key_exists("paged", $_REQUEST) && $_REQUEST["paged"]) {
                        $paged = intval($_REQUEST['paged']);
                        if ($paged < 1) {
                                $paged = 1;
                        }
                }
               
		$limit = 31;
                $offset = $limit * ($paged - 1);

		$table_name = $wpdb->prefix . "karmic_load_ledger";

 		$all_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name;" );
                $filtered_count = $all_count;

                $sql = $wpdb->prepare("
			SELECT id, notes, date, user_id, deleted,
				(%d * initial_load) AS initial_load
			FROM `$table_name` 
			ORDER BY `$table_name`.`$orderBy` $order 
			LIMIT %d, %d 
			",
				CARNIE_KARMA_LOAD_MULTIPLIER,
				$offset,
				$limit);

		$rows = $wpdb->get_results( $sql, ARRAY_A );

		$loadView = new carnieKarmaLoadView;
		$loadView->render_table( $rows, $orderBy, $order, $all_count, $filtered_count, $limit, $paged );
		if (current_user_can('add_users')) {
			$loadView->render_add_form($post_errors);
		}

	}

	function create_tables() {

		global $wpdb;

		// Create tables for Karmic Load Ledger and Karmic Load Metadata
		$karma_load_table_name = $wpdb->prefix . "karmic_load_ledger";
		$sql = "CREATE TABLE $karma_load_table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) ,
			date date DEFAULT '0000-00-00' NOT NULL,
			initial_load double,
			notes text,
			deleted smallint(6),
			UNIQUE KEY id (id) );";

		$wpdb->query($sql);

		$karma_load_meta_table_name = $wpdb->prefix . "karmic_loadmeta";
		$sql = "CREATE TABLE $karma_load_meta_table_name (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			load_id bigint(20) ,
			meta_key text NOT NULL,
			meta_value text,
			UNIQUE KEY meta_id (meta_id) );";

		$wpdb->query($sql);
	}

	function debug_sql( $user_id ) {
?>
                <h2>Debugging SQL DFDF</h2>

		<p>Things are broken.  Sorry.  Watch Darryl debug</p>
<?php

		echo "<pre>";
	
			$gigKarma = new carnieKarmaGigKarma;
			$gig_karma_rows = $gigKarma->get_rows($user_id);

			// var_dump($gig_karma_rows);

			$gig_karma_count = count($gig_karma_rows);

			var_dump($gig_karma_count);

			$gig_karma = array_reduce($gig_karma_rows, function(&$res, $item) {
    						return $res + doubleval($item['karma']) * CARNIE_KARMA_GIG_MULTIPLIER;
						}, 0.0);

			var_dump($gig_karma);

		echo "</pre>";
	}


};

// instantiate class
$CARNIEKARMA = new carnieKarma;

// action hooks
add_action('admin_menu', array($CARNIEKARMA, 'create_admin_menu'));
add_action( 'admin_enqueue_scripts', array($CARNIEKARMA, 'admin_styles'));

// activation hook
register_activation_hook(__FILE__, array($CARNIEKARMA, 'activate'));

// shortcodes
add_shortcode('carniekarma', array($CARNIEKARMA, 'carniekarma_shortcode_handler'));


?>
