<?php
/**
 * Plugin Name:       Scroll Triggered Widget Area
 * Plugin URI:        http://logeshkumar.com/scroll-triggered-widget-area/
 * Description:       A simple plugin which triggers a widget area in the footer with stunning animation options.
 * Version:           1.0.1
 * Author:            Logesh Kumar
 * Author URI:        http://logeshkumar.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-stwa-public.php' );

register_activation_hook( __FILE__, array( 'Scroll_Triggered_Widget_Area', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Scroll_Triggered_Widget_Area', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Scroll_Triggered_Widget_Area', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-stwa-admin.php' );
	add_action( 'plugins_loaded', array( 'stwa_Admin', 'get_instance' ) );

}
