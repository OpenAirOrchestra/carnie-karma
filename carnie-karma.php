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

/*
 * Main class for Carnie Karma Handles activation, hooks, etc.
 */
class carnieKarma {

        /*
         * Plugin is being activated
         * Here we will create table views needed for karma calculation
         */
        function activate() {

	}

	/*
	 * Renders a list of users, each linking to a karma report
	 * for that user.
	 */
	function list_users() {
		$users = get_users('orderby=nicename');
		$usersView = new carnieKarmaUsersView;
		$usersView->render($users);
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

		if ($user_id) {
			$userView = new carnieKarmaUserView;
			$userView->render($user_id);
			$this->explain_karma();
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
	Karma and karmic load evaporates gradually, with a half life
	of about a year.  The math looks like this: 
	<blockquote>
	<code> 0.998 <sup>elapsed days</sup> </code>
	</blockquote>
	</p>

	<p>
	Here is a pretty graph of how a single dew-drop of karma
	dwindles over a three year span.
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
