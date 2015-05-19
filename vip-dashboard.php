<?php

/**
 * This file handles bootstrapping the VIP dashboard and UI components
 */

require __DIR__ . '/plugins-ui/plugins-ui.php';

// Enable menu for all sites using a VIP and a8c sites
add_action( 'admin_menu', 'wpcom_vip_admin_menu', 5 );
add_action( 'admin_head', 'wpcom_vip_admin_head' );
add_action( 'admin_menu', 'wpcom_vip_rename_vip_menu_to_dashboard', 50 );

function wpcom_vip_admin_menu() {
	$vip_page_slug = 'vip-dashboard';
	$vip_page_cap  = 'publish_posts';

	// Limit to admin users on a8c sites.
	if ( is_automattic() )
		$vip_page_cap = 'manage_options';

	if ( ! current_user_can( $vip_page_cap ) )
		return;

	$page = add_menu_page( __( 'VIP Dashboard' ), __( 'VIP' ), $vip_page_cap, $vip_page_slug, 'vip_admin_page', 'div' );

	// Domain management page should be located under the VIP menu because the Store is hidden
	if ( wpcom_is_vip() ) {
		add_filter( 'wpcom_domains_parent_page', function( $menu_slug ) use( $vip_page_slug ) { return $vip_page_slug; } );
	}

	if ( is_admin() && isset( $_GET['page'] ) && 'vip-page' == $_GET['page'] ) {
		wp_safe_redirect( menu_page_url( $vip_page_slug, false ) );
		exit;
	}

	// Add hooks to initialize the Dashboard
	add_action( 'admin_print_scripts-' . $page, 'vip_dashboard_scripts_init' );
	add_action( 'vip_dashboard_setup', 'vip_dashboard_widgets_init' );

	// Needed to move up VIP between Dashboard and Store
	add_filter( 'custom_menu_order', '__return_true'  );
	add_filter( 'menu_order',        'vip_menu_order' );
}

function wpcom_vip_rename_vip_menu_to_dashboard() {
	global $submenu;

	// Rename the first (auto-added) entry in the Dashboard. Kinda hacky, but the menu doesn't have any filters
	if( isset( $submenu['vip-dashboard'][0][0] ) )
		$submenu['vip-dashboard'][0][0] = __( 'Dashboard' );
}

function wpcom_vip_menu_order( $menu_ord ) {
	// Bail if some other plugin nooped the menu
	if ( empty( $menu_ord ) )
		return false;

	// Define local variables
	$vip_order     = array();
	$previous_item = false;

	// Get the index of our custom separator
	$vip_dash  = 'vip-dashboard';
	$dash_menu = 'index.php';

	foreach ( $menu_ord as $item ) {
		if ( $dash_menu == $previous_item ) {
			$vip_order[] = $vip_dash;
			$vip_order[] = $item;
			unset( $menu_ord[$vip_dash] );
		} elseif( $item != $vip_dash ) {
			$vip_order[] = $item;
		}

		$previous_item = $item;
	}

	return $vip_order;
}
