<?php
/**
 * VIP plugins list table class for the VIP admin screen
 */
class WPCOM_VIP_Plugins_UI_List_Table extends WP_List_Table {

	/**
	 * Constructor. Sets up the list table.
	 */
	function __construct() {
		parent::__construct( array(
			'plural' => 'plugins',
		) );
	}

	/**
	 * Fetch the list of VIP plugins to display in the list table.
	 */
	public function prepare_items() {
		$active = $inactive = array();

		// The path has to be
		foreach ( get_plugins( '/../themes/vip/plugins' ) as $plugin_file => $plugin_data ) {

			$plugin_folder = basename( dirname( $plugin_file ) );

			// FPP is listed separately
			if ( isset( WPCOM_VIP_Plugins_UI()->fpp_plugins[ $plugin_folder ] ) )
				continue;

			$plugin_file = 'plugins/' . $plugin_file;

			$status = WPCOM_VIP_Plugins_UI()->is_plugin_active( $plugin_folder ) ? 'active' : 'inactive';

			// Don't want some plugins showing up in the list
			if ( 'inactive' == $status && in_array( $plugin_folder, WPCOM_VIP_Plugins_UI()->hidden_plugins ) )
				continue;

			// Translate, Don't Apply Markup, Sanitize HTML
			${$status}[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$this->items = array_merge( $active, $inactive );
	}

	/**
	 * Output an error message if no VIP plugins were found to show in the list table.
	 */
	public function no_items() {
		echo 'There was an error listing out plugins. Please try again in a bit.';
	}

	/**
	 * Return an array of CSS classes to apply to the list table.
	 *
	 * @return array
	 */
	public function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	/**
	 * Handles outputting the markup for each row of the list table.
	 */
	public function display_rows() {
		foreach ( $this->items as $plugin_file => $plugin_data )
			$this->single_row( $plugin_file, $plugin_data );
	}

	/**
	 * Handles outputting the markup for a single row of the list table.
	 *
	 * @param string $plugin_file The filename of the plugin being handled
	 * @param array $plugin_data Data from {@link https://core.trac.wordpress.org/browser/trunk/wp-admin/includes/plugin.php#L108}) for the plugin
	 */
	public function single_row( $plugin_file ) {
		$plugin = basename( dirname( $plugin_file ) );
		$plugin_data = get_plugin_data( WP_CONTENT_DIR . '/themes/vip/' . $plugin_file );

		$is_active = WPCOM_VIP_Plugins_UI()->is_plugin_active( $plugin );

		$class = $is_active ? 'active' : 'inactive';
		if ( $is_active )
			$class .= ' active-' . $is_active;

		$actions = array();
		$actions = WPCOM_VIP_Plugins_UI()->add_activate_or_deactive_action_link( $actions, $plugin );

		$plugin_name = $plugin_data['Name'];
		$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';

		echo '<tr class="' . esc_attr( $class ) . '">';

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			switch ( $column_name ) {
				case 'name':
					echo "<td class='plugin-title'$style><strong>$plugin_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>
						<div class='plugin-description'>$description</div>
						<div class='$class second plugin-version-author-uri'>";

					$plugin_meta = array();

					$plugin_meta[] = '<a href="' . esc_url( '//vip.wordpress.com/plugins/' . $plugin . '/' ) . '" title="' . esc_attr__( 'Visit our VIP Plugins Directory to learn more' ) . '" target="_blank">' . __( 'Learn More' ) . '</a>';

					if ( ! empty( $plugin_data['Author'] ) ) {
						$author = $plugin_data['Author'];
						if ( ! empty( $plugin_data['AuthorURI'] ) )
							$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . esc_attr__( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
						$plugin_meta[] = sprintf( __( 'By %s' ), $author );
					}

					echo implode( ' | ', $plugin_meta );

					echo '</div></td>';
					break;
			}
		}

		echo "</tr>";
	}
}

/**
 * VIP featured plugins list table class for the VIP admin screen
 */
class WPCOM_VIP_Featured_Plugins_List_Table extends WP_List_Table {

	/**
	 * We are using the backdoor _column_headers, because the columns filter is screen-specific
	 * but this is the second table on that screen and we can't differentiate between both.
	 *
	 * Setting this variable won't run the filter at all
	 */
	public $_column_headers = array( array( 'plugin' => '' ), array(), array() );

	/**
	 * Filter the featured plugin list?
	 * Used to separate new layout
	 *
	 * @var string
	 */
	public $filter = 'inactive';

	/**
	 * Constructor. Sets up the list table.
	 */
	function __construct() {
		parent::__construct( array(
			'plural' => 'Featured Plugins',
		) );
	}

	/**
	 * Fetch the list of VIP featured plugins to display in the list table.
	 */
	public function prepare_items() {

		foreach ( WPCOM_VIP_Plugins_UI()->fpp_plugins as $slug => $plugin ) {

			if ( ! WPCOM_VIP_Plugins_UI()->is_plugin_active( $slug ) && in_array( $slug, WPCOM_VIP_Plugins_UI()->hidden_plugins ) )
				continue;

			$this->items[] = $slug;
		}
	}

	/**
	 * Returns the content for a row in the list table.
	 *
	 * @param array $item Plugin slug
	 * @param string $column_name Name of the table column
	 * @return string
	 */
	public function column_default( $slug, $column_name ) {

		if ( ! isset( $slug ) )
			return;

		if ( ! isset( WPCOM_VIP_Plugins_UI()->fpp_plugins[$slug] ) )
			return;

		// only show inactive
		if ( WPCOM_VIP_Plugins_UI()->is_plugin_active( $slug ) && $this->filter == 'inactive' )
			return;

		// only show active
		if ( ! WPCOM_VIP_Plugins_UI()->is_plugin_active( $slug ) && $this->filter == 'active' )
			return;

		$image_src = plugins_url( 'images/featured-plugins/' . $slug . '-2x.png', __FILE__ );

		$lobby_url = '//vip.wordpress.com/plugins/' . $slug . '/';

		$is_active = WPCOM_VIP_Plugins_UI()->is_plugin_active( $slug );

		ob_start();
?>
		<div class="plugin <?php if ( $is_active ) { ?>active<?php } ?>">
			<img src="<?php echo esc_url( $image_src ); ?>" width="48" height="48" class="fp-icon" />
			<div class="fp-content">
				<h3 class="fp-title"><?php echo WPCOM_VIP_Plugins_UI()->fpp_plugins[$slug]['name']; ?></h3>
				<p class="fp-description"><?php echo WPCOM_VIP_Plugins_UI()->fpp_plugins[$slug]['description']; ?></p>
			</div>
			<div class="interstitial">
				<div class="interstitial-inner">
					<h3 class="fp-title"><?php echo WPCOM_VIP_Plugins_UI()->fpp_plugins[$slug]['name']; ?></h3>
					<?php
					if ( $is_active ) {
						if ( 'option' == $is_active ) {
							echo '<a href="' . esc_url( WPCOM_VIP_Plugins_UI()->get_plugin_deactivation_link( $slug ) ) . '" class="fp-button" title="' . esc_attr__( 'Deactivate this plugin' ) . '">' . __( 'Deactivate Plugin' ) . '</a>';
							echo '<span class="fp-text">'. __( 'Deactivating Plugin') .'</span>';
						} elseif ( 'manual' == $is_active ) {
							echo '<span title="To deactivate this particular plugin, edit your theme\'s functions.php file" class="fp-text">' . __( "Enabled via your theme's code" ) . '</span>';
						}
					} elseif ( ! $this->activation_disabled ) {
						echo '<a href="' . esc_url( WPCOM_VIP_Plugins_UI()->get_plugin_activation_link( $slug ) ) . '" class="fp-button" title="' . esc_attr__( 'Activate this plugin' ) . '" class="edit">' . __( 'Activate Plugin' ) . '</a>';
						echo '<span class="fp-text">'. __( 'Activating Plugin') .'</span>';

					}
					?>
				</div>
				<div class="more-info">
					<a href="<?php echo esc_url( $lobby_url ); ?>" target="_blank" title="Learn More">
						<div class="icon"></div>
					</a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Output custom display for featured plugins
	 *
	 * @return void
	 */
	public function display() {
		$singular = $this->_args['singular'];


		$this->display_tablenav( 'top' );

	?>
	<main id="plugins" role="main">

		<section id="plugins-fp" class="<?php echo implode( ' ', $this->get_table_classes() ); ?>">

			<nav id="menu">
				<input id="search" type="search" value="" placeholder="<?php _e( 'Filter Plugins' ); ?>">
			</nav>

			<section id="active">

				<h3><?php _e( 'Active Plugins' ); ?></h3>

				<?php $this->filter = 'active'; ?>
				<?php $this->display_rows_or_placeholder(); ?>

			</section>

			<section id="showcase">

				<h3><?php _e( 'VIP Featured Plugins' ); ?></h3>

				<?php $this->filter = 'inactive'; ?>
				<?php $this->display_rows_or_placeholder(); ?>

			</section>

		</section>

	</main>

	<?php
		$this->display_tablenav( 'bottom' );
	}
}
