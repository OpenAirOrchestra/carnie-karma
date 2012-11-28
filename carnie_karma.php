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

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

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

};

// instantiate class
$CARNIEKARMA = new carnieKarma;

// add_action('admin_menu', array($CARNIEKARMA, 'create_admin_menu'));

register_activation_hook(__FILE__, array($CARNIEKARMA, 'activate'));

?>
