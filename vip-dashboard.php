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

	$current_user = wp_get_current_user();
	$name    = ( ! empty( $_POST['vipsupport-name'] ) )    ? strip_tags( stripslashes( $_POST['vipsupport-name'] ) )   : $current_user->display_name;
	$email   = ( ! empty( $_POST['vipsupport-email'] ) )   ? strip_tags( stripslashes( $_POST['vipsupport-email'] ) )  : $current_user->user_email;
	?>
	<div id="app"
		data-asseturl="<?php echo plugins_url( '/assets/', __FILE__ ); ?>"
		data-name="<?php echo $name; ?>"
		data-email="<?php echo $email; ?>"
	></div>
	<?php
}

// legacy code

/**
 * When our VIP Dashboard contact form is submitted, this handles what do do with the data.
 */
function vip_contact_form_handler() {

	// If the contact form isn't being submitted, we shouldn't be running this...
	if ( !isset( $_POST['vipsupport-form'], $_POST['submit'], $_POST['_wpnonce'] ) )
		return;

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'vip-contact-support-form' ) )
		wp_die( __( 'Security check failed. Make sure you should be doing this, and try again.' ) );

	$vipsupportemailaddy  = 'vip-support@wordpress.com';
	$cc_headers_to_kayako = '';

	// Default values
	$sendemail    = true;                  // Should we send an e-mail? Tracks errors.
	$emailsent    = false;                 // Tracks wp_mail() results
	$new_tmp_name = false;                 // For an attachment
	$current_user = wp_get_current_user(); // Current user

	// Name & Email
	$name          = ( ! empty( $_POST['vipsupport-name']  ) ) ? strip_tags( stripslashes( $_POST['vipsupport-name']  ) ) : $current_user->display_name;
	$email         = ( ! empty( $_POST['vipsupport-email'] ) ) ? strip_tags( stripslashes( $_POST['vipsupport-email'] ) ) : $current_user->user_email;
	if ( !is_email( $email ) )
		add_settings_error( 'vipsupport', 'no_email', 'Please enter a valid email for your ticket.', 'error' );

	// Subject, Group, & Priority
	$subject       = ( ! empty( $_POST['vipsupport-subject']  ) ) ? strip_tags( stripslashes( $_POST['vipsupport-subject']  ) ) : '';
	$group         = ( ! empty( $_POST['vipsupport-group']    ) ) ? strip_tags( stripslashes( $_POST['vipsupport-group']    ) ) : 'Technical';
	$priority      = ( ! empty( $_POST['vipsupport-priority'] ) ) ? strip_tags( stripslashes( $_POST['vipsupport-priority'] ) ) : 'Medium';

	// People to copy
	$ccemail       = ( ! empty( $_POST['vipsupport-ccemail'] ) ) ? strip_tags( stripslashes( $_POST['vipsupport-ccemail'] ) ) : '';
	$ccusers       = ( ! empty( $_POST['vipsupport-ccuser']  ) ) ? (array) $_POST['vipsupport-ccuser'] : array();
	$ccusers       = array_map( 'stripslashes',        $ccusers );
	$ccusers       = array_map( 'sanitize_text_field', $ccusers );
	$temp_ccemails = explode( ',', $ccemail );
	$temp_ccemails = array_filter( array_map( 'trim', $temp_ccemails ) );
	$ccemails      = array();
	if ( !empty( $temp_ccemails ) ) {
		foreach( array_values( $temp_ccemails ) as $value ) {
			if ( is_email( $value ) ) {
				$ccemails[] = $value;
			}
		}
	}
	$ccemails = array_merge( $ccemails, $ccusers );
	$ccemails = apply_filters( 'vip_contact_form_cc', $ccemails );

	if ( count( $ccemails ) )
		$cc_headers_to_kayako .= 'CC: ' . implode( ',', $ccemails ) . "\r\n";

	if ( 'Emergency' === $priority )
		$subject = sprintf( '[%s] %s', $priority, $subject );

	$content = stripslashes( $_POST['vipsupport-details'] ) . "\n\n--- Ticket Details --- \n";

	// Priority
	if ( ! empty( $_POST['vipsupport-priority'] ) )
		$content .= "\nPriority: " . $priority;

	$content .= "\nUser: " . $current_user->user_login . ' | ' . $current_user->display_name;

	// VIP DB
	if ( get_vipdb_ids() ) {
		$content .= "\nSite Name: " . get_bloginfo( 'name' );
		$content .= "\nSite URLs: " . site_url() . ' | ' . admin_url();
		$content .= "\nTheme: " . get_option( 'stylesheet' ) . ' | '. get_current_theme();
	}

	$content .= sprintf( "\n\nSent from %s on %s", home_url(), date( 'c', current_time( 'timestamp', 1 ) ) );

	// Attachments
	$attachments = array();
	if ( ! empty( $_FILES['vipsupport-attachment'] ) && 4 != $_FILES['vipsupport-attachment']['error'] ) {
		if ( 0 != $_FILES['vipsupport-attachment']['error'] || empty( $_FILES['vipsupport-attachment']['tmp_name'] ) ) {
			$sendemail = false;

			switch ( $_FILES['vipsupport-attachment']['error'] ) {
				case 1:
				case 2:
					$max_upload_size = vip_dashboard_contact_form_get_max_upload_size();
					add_settings_error( 'vipsupport', 'attachment_error', sprintf( 'Your uploaded file was too large. Our ticketing system can only accept files up to %s big. Try using <a href="http://www.dropbox.com/">Dropbox</a>, <a href="https://www.yousendit.com/">YouSendIt</a>, or hosting it on a FTP server instead.', $max_upload_size['human'] ), 'error' );
					break;
				case 3:
					add_settings_error( 'vipsupport', 'attachment_error', 'Your uploaded file only partially uploaded. Please try again.', 'error' );
					break;
				default;
					add_settings_error( 'vipsupport', 'attachment_error', 'There was an error with the attachment upload.', 'error' );
			}
		} else {
			// We need the filename to be correct
			// Don't forget to delete the file manually when done since it's been renamed!
			$new_tmp_name = str_replace(
				basename( $_FILES['vipsupport-attachment']['tmp_name'] ),
				$_FILES['vipsupport-attachment']['name'],
				$_FILES['vipsupport-attachment']['tmp_name']
			);
			rename( $_FILES['vipsupport-attachment']['tmp_name'], $new_tmp_name );
			$attachments = array( $new_tmp_name );
		}
	}

	if ( empty( $subject ) ) {
		add_settings_error( 'vipsupport', 'missing_subject', 'Please enter a descriptive subject for your ticket.', 'error' );
		$sendemail = false;
	}

	if ( true === $sendemail ) {
		bump_stats_extras( 'vip_contact_form_tickets', $priority );

		$headers = "From: \"$name\" <$email>\r\n";
		if ( wp_mail( $vipsupportemailaddy, $subject, $content, $headers . $cc_headers_to_kayako, $attachments ) ) {
			echo '<script>document.location = "' . esc_url_raw( add_query_arg( 'ticketsent', 1 ) ) . '";</script>'; // hacky js redirect because we run this too late
			// No exit() here so that unlink() runs
			$emailsent = true;
		} else {
			add_settings_error( 'vipsupport', 'wp_mail_failed', 'There was an error sending the support request. ' . vip_echo_mailto_vip_hosting( 'Please send in a request manually.', false ), 'error' );
		}
	}

	// Remove the uploaded file. Has to be done manually since it was renamed.
	if ( $new_tmp_name )
		unlink( $new_tmp_name );

	// If the e-mail was sent, then a redirect header was sent and we can exit() now that we removed the attachment
	if ( true === $emailsent )
		exit();
}