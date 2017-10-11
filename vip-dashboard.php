<?php
/*
 * Plugin Name: VIP Dashboard
 * Plugin URI: http://vip.wordpress.com
 * Description: WordPress VIP Go Dashboard
 * Author: Scott Evans, Filipe Varela
 * Version: 2.0.4
 * Author URI: http://vip.wordpress.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vip-dashboard
 * Domain Path: /languages/
*/

/**
 * Boot the new VIP Dashboard
 *
 * @return void
 */
function vip_dashboard_init() {

	if ( ! is_admin() ) {
		return;
	}

	// Enable menu for all sites using a VIP and a8c sites.
	add_action( 'admin_menu', 'wpcom_vip_admin_menu', 5 );
	add_action( 'admin_menu', 'wpcom_vip_rename_vip_menu_to_dashboard', 50 );

}
add_action( 'plugins_loaded', 'vip_dashboard_init' );

/**
 * Register master stylesheet (compiled via gulp)
 *
 * @return void
 */
function vip_dashboard_admin_styles() {
	wp_register_style( 'vip-dashboard-style', plugins_url( '/assets/css/style.css', __FILE__ ) , '1.0' );
	wp_enqueue_style( 'vip-dashboard-style' );
}

/**
 * Register master JavaScript (compiled via gulp)
 *
 * @return void
 */
function vip_dashboard_admin_scripts() {
	wp_register_script( 'vip-dashboard-script', plugins_url( '/assets/js/vip-dashboard.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'vip-dashboard-script' );
}

/**
 * Output the dashboard page, an empty div for React to initialise against
 *
 * @return void
 */
function vip_dashboard_page() {

	$current_user = wp_get_current_user();
	$name         = $current_user->display_name;
	$email        = $current_user->user_email;
	$ajaxurl      = add_query_arg( array( '_wpnonce' => wp_create_nonce( 'vip-dashboard' ) ), untrailingslashit( admin_url( 'admin-ajax.php' ) ) );
	?>
	<div id="app"
		data-ajaxurl="<?php echo esc_url( $ajaxurl ); ?>"
		data-asseturl="<?php echo esc_attr( plugins_url( '/assets/', __FILE__ ) ); ?>"
		data-email="<?php echo esc_attr( $email ); ?>"
		data-name="<?php echo esc_attr( $name ); ?>"
		data-adminurl="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>"
	></div>
	<?php
}

/**
 * Support/Contact form handler - sent from React to admin-ajax
 *
 * @return void
 */
function vip_contact_form_handler() {

	if ( ! isset( $_POST['body'], $_POST['subject'], $_GET['_wpnonce'] ) ) {
		$return = array(
			'status' => 'error',
			'message' => __( 'Please complete all required fields.', 'vip-dashboard' ),
		);
		echo wp_json_encode( $return );
		die();
	}

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'vip-dashboard' ) ) {
		$return = array(
			'status' => 'error',
			'message' => __( 'Security check failed. Make sure you should be doing this, and try again.', 'vip-dashboard' ),
		);
		echo wp_json_encode( $return );
		die();
	}

	$vipsupportemailaddy  = 'vip-support@wordpress.com';
	$cc_headers_to_kayako = '';

	$sendemail    = true;
	$emailsent    = false;
	$current_user = wp_get_current_user();

	$name          = ( ! empty( $_POST['name'] ) ) ? strip_tags( stripslashes( $_POST['name'] ) ) : $current_user->display_name;
	$email         = ( ! empty( $_POST['email'] ) ) ? strip_tags( stripslashes( $_POST['email'] ) ) : $current_user->user_email;

	if ( ! is_email( $email ) ) {
		$return = array(
			'status' => 'error',
			'message' => __( 'Please enter a valid email for your ticket.', 'vip-dashboard' ),
		);
		echo wp_json_encode( $return );
		die();
	}

	$subject       = ( ! empty( $_POST['subject'] ) ) ? strip_tags( stripslashes( $_POST['subject'] ) ) : '';
	$group         = ( ! empty( $_POST['type'] ) ) ? strip_tags( stripslashes( $_POST['type'] ) ) : 'Technical';
	$priority      = ( ! empty( $_POST['priority'] ) ) ? strip_tags( stripslashes( $_POST['priority'] ) ) : 'Medium';

	$ccemail       = ( ! empty( $_POST['cc'] ) ) ? strip_tags( stripslashes( $_POST['cc'] ) ) : '';
	$temp_ccemails = explode( ',', $ccemail );
	$temp_ccemails = array_filter( array_map( 'trim', $temp_ccemails ) );
	$ccemails      = array();

	if ( ! empty( $temp_ccemails ) ) {
		foreach ( array_values( $temp_ccemails ) as $value ) {
			if ( is_email( $value ) ) {
				$ccemails[] = $value;
			}
		}
	}
	$ccemails = apply_filters( 'vip_contact_form_cc', $ccemails );

	if ( count( $ccemails ) ) {
		$cc_headers_to_kayako .= 'CC: ' . implode( ',', $ccemails ) . "\r\n";
	}

	if ( empty( $subject ) ) {
		$return = array(
			'status' => 'error',
			'message' => __( 'Please enter a descriptive subject for your ticket.', 'vip-dashboard' ),
		);
		echo wp_json_encode( $return );
		die();
	}

	if ( '' === $_POST['body'] ) {
		$return = array(
			'status' => 'error',
			'message' => __( 'Please enter a detailed description of your issue.', 'vip-dashboard' ),
		);
		echo wp_json_encode( $return );
		die();
	}

	if ( 'Emergency' === $priority ) {
		$subject = sprintf( '[%s] %s', $priority, $subject );
	}
	$content = stripslashes( $_POST['body'] ) . "\n\n--- Ticket Details --- \n";

	if ( $priority ) {
		$content .= "\nPriority: " . $priority;
	}
	$content .= "\nUser: " . $current_user->user_login . ' | ' . $current_user->display_name;

	// VIP DB.
	$theme = wp_get_theme();
	$content .= "\nSite Name: " . get_bloginfo( 'name' );
	$content .= "\nSite URLs: " . site_url() . ' | ' . admin_url();
	$content .= "\nTheme: " . get_option( 'stylesheet' ) . ' | ' . $theme->get( 'Name' );

	// added for VIPv2.
	$content .= "\nPlatform: VIP Go";

	// send date and time.
	$content .= sprintf( "\n\nSent from %s on %s", home_url(), date( 'c', current_time( 'timestamp', 1 ) ) );

	// Filter from name/email. NOTE - not un-hooking the filter because we die() immediately after wp_mail()
	add_filter( 'wp_mail_from', function() use ( $email ) {
		return $email;
	});

	add_filter( 'wp_mail_from_name', function() use ( $name ) {
		return $name;
	});

	$headers = "From: \"$name\" <$email>\r\n";
	if ( wp_mail( $vipsupportemailaddy, $subject, $content, $headers . $cc_headers_to_kayako ) ) {
		$return = array(
			'status' => 'success',
			'message' => __( 'Your support request is on its way, we will be in touch soon.', 'vip-dashboard' ),
		);

		echo wp_json_encode( $return );
		die();

	} else {
		$manual_link = vip_echo_mailto_vip_hosting( __( 'Please send in a request manually.', 'vip-dashboard' ), false );
		$return = array(
			'status' => 'error',
			'message' => sprintf( __( 'There was an error sending the support request. %1$s', 'vip-dashboard' ),  $manual_link ),
		);

		echo wp_json_encode( $return );
		die();
	}

	die();
}
add_action( 'wp_ajax_vip_contact', 'vip_contact_form_handler' );

/**
 * Generate a manual email link if the send fails
 *
 * @param string $linktext the text for the link.
 * @param bool   $echo echo or return.
 * @return html
 */
function vip_echo_mailto_vip_hosting( $linktext = 'Send an email to VIP Hosting.', $echo = true ) {

	$current_user = get_currentuserinfo();

	$name = '';
	if ( isset( $_POST['name'] ) ) {
		$name = sanitize_text_field( $_POST['name'] );
	} elseif ( isset( $current_user->display_name ) ) {
		$name = $current_user->display_name;
	}

	$useremail = '';
	if ( isset( $_POST['email'] ) && is_email( $_POST['email'] ) ) {
		$useremail = sanitize_email( $_POST['email'] );
	} elseif ( isset( $current_user->user_email ) ) {
		$name = $current_user->user_email;
	}

	$email  = "\n\n--\n";
	$email .= 'Name: ' . $name . "\n";
	$email .= 'Email: ' . $useremail . "\n";
	$email .= 'URL: ' . home_url() . "\n";
	$email .= 'IP Address: ' . $_SERVER['REMOTE_ADDR'] . "\n";
	$email .= 'Server: ' . php_uname( 'n' ) . "\n";
	$email .= 'Browser: ' . $_SERVER['HTTP_USER_AGENT'] . "\n";
	$email .= 'Platform: VIP Go';

	$url = add_query_arg( array( 'subject' => __( 'Descriptive subject please', 'vip-dashboard' ), 'body' => rawurlencode( $email ) ), 'mailto:vip-support@wordpress.com' );

	// $url not escaped on output as email formatting is borked by esc_url:
	// https://core.trac.wordpress.org/ticket/31632
	$html = '<a href="' . $url . '">' . esc_html( $linktext ) . '</a>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Create admin menu, enqueue scripts etc
 *
 * @return void
 */
function wpcom_vip_admin_menu() {
	$vip_page_slug = 'vip-dashboard';
	$vip_page_cap  = 'publish_posts';

	if ( ! current_user_can( $vip_page_cap ) ) {
		return;
	}

	$page = add_menu_page( __( 'VIP Dashboard' ), __( 'VIP' ), $vip_page_cap, $vip_page_slug, 'vip_dashboard_page', 'dashicons-tickets' );

	add_action( 'admin_print_styles-' . $page, 'vip_dashboard_admin_styles' );
	add_action( 'admin_print_scripts-' . $page, 'vip_dashboard_admin_scripts' );

	add_filter( 'custom_menu_order', '__return_true' );
	add_filter( 'menu_order',        'wpcom_vip_menu_order' );
}

/**
 * Rename the first (auto-added) entry in the Dashboard. Kinda hacky, but the menu doesn't have any filters
 *
 * @return void
 */
function wpcom_vip_rename_vip_menu_to_dashboard() {
	global $submenu;

	if ( isset( $submenu['vip-dashboard'][0][0] ) ) {
		$submenu['vip-dashboard'][0][0] = __( 'Dashboard' );
	}
}

/**
 * Set the menu order for the VIP Dashboard
 *
 * @param  array $menu_ord order of menu.
 * @return array
 */
function wpcom_vip_menu_order( $menu_ord ) {

	if ( empty( $menu_ord ) ) {
		return false;
	}

	$vip_order     = array();
	$previous_item = false;

	$vip_dash  = 'vip-dashboard';
	$dash_menu = 'index.php';

	foreach ( $menu_ord as $item ) {
		if ( $dash_menu === $previous_item ) {
			$vip_order[] = $vip_dash;
			$vip_order[] = $item;
			unset( $menu_ord[ $vip_dash ] );
		} elseif ( $item !== $vip_dash ) {
			$vip_order[] = $item;
		}

		$previous_item = $item;
	}

	return $vip_order;
}

/**
 * Retrieve featured plugins from the vip.wordpress.com API
 *
 * @return array an array of plugins
 */
function wpcom_vip_fetch_vip_featured_plugins() {
	$plugins = wp_cache_get( 'wpcom_vip_featured_plugins' );

	if ( false === $plugins ) {
		$plugins = array();
		$url_for_featured_plugins = 'https://vip.wordpress.com/wp-json/vip/v1/plugins?type=technology';
		$response = vip_safe_wp_remote_get( $url_for_featured_plugins, false, 3, 5 );

		if ( ! $response || is_wp_error( $response ) ) {
			trigger_error( 'The API on vip.wordpress.com is not responding (' . esc_url( $url_for_featured_plugins ) . ')' );
			return false;
		}

		$plugins = json_decode( $response['body'] );

		if ( empty( $plugins ) ) {
			return false;
		}

		wp_cache_set( 'wpcom_vip_featured_plugins', $plugins, '', HOUR_IN_SECONDS * 4 );
	}

	return $plugins;
}

/**
 * Render the featured partner plugins to the plugins screenReaderText
 * Uses the notice hooks as that is all we have on these pages
 *
 * @return null
 */
function wpcom_vip_render_vip_featured_plugins() {
	$screen = get_current_screen();

	if ( ! ( 'plugins' === $screen->id || 'plugins-network' === $screen->id ) ) {
		return;
	}

	$plugins = wpcom_vip_fetch_vip_featured_plugins();

	if ( ! $plugins ) {
		return;
	}

	?>
	<div class="featured-plugins notice">
		<h3><?php _e( 'VIP Featured Plugins', 'vip-dashboard' ); ?></h3>
		<?php
		foreach ( $plugins as $key => $plugin ) {
			?>
			<div class="plugin">
				<a class="fp-content" href="<?php echo esc_attr( $plugin->meta->plugin_url ); ?>" target="_blank">
					<img src="<?php echo esc_attr( $plugin->meta->listing_logo ); ?>" alt="<?php echo esc_attr( $plugin->post_title ); ?>" />
					<h4><?php echo esc_html( $plugin->post_title ); ?></h4>
					<p><?php echo esc_html( $plugin->meta->listing_description ); ?></p>
				</a>
				<a class="fp-overlay" href="<?php echo esc_attr( $plugin->meta->plugin_url ); ?>" target="_blank">
					<div class="fp-overlay-inner">
						<div class="fp-overlay-cell">
							<span>	
								<?php _e( 'More Information', 'vip-dashboard' ); ?>
							</span>
						</div>
					</div>
				</a>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
add_action( 'admin_notices', 'wpcom_vip_render_vip_featured_plugins', 99 );
add_action( 'network_admin_notices', 'wpcom_vip_render_vip_featured_plugins', 99 );

/**
 * Returns a filtered list of code activated plugins similar to core plugins Option
 *
 * @return array list of filtered plugins
 */
function wpcom_vip_get_filtered_loaded_plugins() {
	$code_plugins = wpcom_vip_get_loaded_plugins();
	foreach ( $code_plugins as $key => $plugin ) {
		if ( substr( $plugin, 0, 8 ) === 'plugins/' ) {
			$code_plugins[ $key ] = preg_replace( '/^(plugins\/)/i', '', $plugin );
		} else {
			unset( $code_plugins[ $key ] );
		}
	}

	return $code_plugins;
}

/**
 * Returns a filtered list of code activated plugins similar to network plugins option
 *
 * @return array list of filtered, active plugins
 */
function wpcom_vip_get_network_filtered_loaded_plugins() {
	$code_plugins = wpcom_vip_get_filtered_loaded_plugins();
	foreach ( $code_plugins as $key => $plugin ) {
		unset( $code_plugins[ $key ] );
		$code_plugins[ $plugin ] = filemtime( __FILE__ );
	}

	return $code_plugins;
}

/**
 * Ensure code activated plugins are shown as such on core plugins screens
 *
 * @param  array $actions
 * @param  string $plugin_file
 * @param  array $plugin_data
 * @param  string $context
 * @return array
 */
function wpcom_vip_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
	$screen = get_current_screen();
	if ( in_array( $plugin_file, wpcom_vip_get_filtered_loaded_plugins(), true ) ) {
		if ( array_key_exists( 'activate', $actions ) ) {
			unset( $actions['activate'] );
		}
		if ( array_key_exists( 'deactivate', $actions ) ) {
			unset( $actions['deactivate'] );
		}
		$actions['vip-code-activated-plugin'] = __( 'Enabled via code', 'vip-dashboard' );

		if ( 'plugins' === $screen->id ) {
			unset( $actions['network_active'] );
		}
	}

	return $actions;
}
add_filter( 'plugin_action_links', 'wpcom_vip_plugin_action_links', 10, 4 );
add_filter( 'network_admin_plugin_action_links', 'wpcom_vip_plugin_action_links', 10, 4 );

/**
 * Merge code activated plugins with database option for better UI experience
 *
 * @param  array $value
 * @param  string $option
 * @return array
 */
function wpcom_vip_option_active_plugins( $value, $option ) {
	$code_plugins = wpcom_vip_get_filtered_loaded_plugins();
	$value = array_merge( $code_plugins, $value );

	return $value;
}
add_filter( 'option_active_plugins', 'wpcom_vip_option_active_plugins', 10, 2 );

/**
 * Merge code activated plugins with network database option for better UI experience
 *
 * @param  array $value
 * @param  string $option
 * @return array
 */
function wpcom_vip_site_option_active_sitewide_plugins( $value, $option ) {
	$code_plugins = wpcom_vip_get_network_filtered_loaded_plugins();
	$value = array_merge( $code_plugins, $value );

	return $value;

}
add_filter( 'site_option_active_sitewide_plugins', 'wpcom_vip_site_option_active_sitewide_plugins', 10, 2 );

/**
 * Unmerge code activated plugins from active plugins option (reverse of the above)
 *
 * @param  array $value
 * @param  array $old_value
 * @param  string $option
 * @return array
 */
function wpcom_vip_pre_update_option_active_plugins( $value, $old_value, $option ) {
	$code_plugins = wpcom_vip_get_filtered_loaded_plugins();
	$value = array_diff( $value, $code_plugins );

	return $value;
}
add_filter( 'pre_update_option_active_plugins', 'wpcom_vip_pre_update_option_active_plugins', 10, 3 );

/**
 * Unmerge code activated plugins from network active plugins option (reverse of the above)
 *
 * @param  array $value
 * @param  array $old_value
 * @param  string $option
 * @param  int $network_id
 * @return array
 */
function wpcom_vip_pre_update_site_option_active_sitewide_plugins( $value, $old_value, $option, $network_id ) {
	$code_plugins = wpcom_vip_get_network_filtered_loaded_plugins();
	$value = array_diff( $value, $code_plugins );

	return $value;
}
add_filter( 'pre_update_site_option_active_sitewide_plugins', 'wpcom_vip_pre_update_site_option_active_sitewide_plugins', 10, 4 );

/**
 * Custom CSS and JS for the plugins UIs
 *
 * @return null
 */
function wpcom_vip_plugins_ui_admin_enqueue_scripts() {
	$screen = get_current_screen();
	if ( 'plugins' === $screen->id || 'plugins-network' === $screen->id ) {
		wp_enqueue_style( 'vip-plugins-style', plugins_url( '/assets/css/plugins-ui.css', __FILE__ ) , '3.0' );
		wp_enqueue_script( 'vip-plugins-script', plugins_url( '/assets/js/plugins-ui.js', __FILE__ ), array( 'jquery' ), '3.0', true );
	}

}
add_action( 'admin_enqueue_scripts', 'wpcom_vip_plugins_ui_admin_enqueue_scripts' );
