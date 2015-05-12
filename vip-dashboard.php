<?php
/*
Plugin Name: VIP Dashboard
Plugin URI: http://vip.wordpress.com
Description: WordPress VIP Dashboard
Author: Scott Evans, Filipe Varela
Version: 1.0
Author URI: http://vip.wordpress.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: vip-dashboard
Domain Path: /languages/
*/

function vip_dashboard_init() {

	// admin only
	if ( ! is_admin() )
		return;

	// dashboard menu
	add_action( 'admin_menu', 'vip_dashboard_menu' );

	add_action( 'admin_enqueue_scripts', 'vip_dashboard_admin_styles' );
	add_action( 'admin_enqueue_scripts', 'vip_dashboard_admin_scripts' );
}
add_action( 'plugins_loaded', 'vip_dashboard_init' );

function vip_dashboard_menu() {
	add_menu_page( __( 'VIP Dashboard', 'vip-dashboard' ), __( 'VIP Dashboard', 'vip-dashboard' ), 'read', 'vip-dashboard', 'vip_dashboard_page', 'dashicons-tickets', 5 );
}

function vip_dashboard_admin_styles() {
	wp_register_style( 'vip-dashboard-style', plugins_url( '/assets/css/style.css', __FILE__ ) , '1.0' );
	wp_enqueue_style( 'vip-dashboard-style' );
}

function vip_dashboard_admin_scripts() {
	wp_register_script( 'vip-dashboard-script', plugins_url( '/assets/js/vip-dashboard.js', __FILE__ ), array(), '1.0', true );
	wp_enqueue_script( 'vip-dashboard-script' );
}

function vip_dashboard_page() {

	?>
	<p class="show-if-nojs">REACT<p>
	<div id="app"></div>
	<?php
}