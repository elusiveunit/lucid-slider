<?php
/**
 * Lucid Toolbox activation.
 *
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Tries to install and/or activate Lucid Toolbox if it's not available.
 *
 * @package Lucid
 * @subpackage Slider
 */
class Lucid_Slider_Activate_Toolbox {

	/**
	 * Lucid Toolbox plugin basename (plugin-folder/plugin-file.php).
	 *
	 * @var string
	 */
	protected $toolbox_basename;

	/**
	 * Path to bundled Lucid Toolbox zip file.
	 *
	 * @var string
	 */
	protected $toolbox_zip;

	/**
	 * WordPress plugin directory.
	 *
	 * @var string
	 */
	protected $wp_plugin_dir;

	/**
	 * Activation error message, if failed.
	 *
	 * @var string
	 */
	protected $activation_error = '';

	/**
	 * Constructor. Set paths and register activation hook.
	 *
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {

		// Set paths
		$this->toolbox_basename = 'lucid-toolbox/lucid-toolbox.php';
		$this->wp_plugin_dir    = trailingslashit( dirname( LUCID_SLIDER_PATH ) );
		$this->toolbox_zip      = LUCID_SLIDER_PATH . 'lucid-toolbox/lucid-toolbox.zip';

		// Run on plugin activation
		register_activation_hook( $file, array( $this, 'plugin_activation' ) );
	}

	/**
	 * Plugin activation hook. Install and/or activate Lucid Toolbox if needed.
	 */
	public function plugin_activation() {
		$active = get_option( 'active_plugins' );

		// Check if Lucid Toolbox is activated.
		if ( ! in_array( $this->toolbox_basename, $active ) ) :
			$installed = array_keys( get_plugins() );

			if ( in_array( $this->toolbox_basename, $installed ) ) :
				add_action( 'update_option_active_plugins', array( $this, 'activate_toolbox' ) );
			else :
				$this->_install_toolbox();
			endif;
		endif;
	}

	/**
	 * Install Lucid Toolbox.
	 *
	 * Unzips bundled plugin file to plugin directory and registers the
	 * activation function if successful.
	 */
	protected function _install_toolbox() {
		$zip = new ZipArchive;
		$res = $zip->open( $this->toolbox_zip );

		// Zip opened successfully
		if ( true === $res ) :
			$zip->extractTo( $this->wp_plugin_dir );
			$zip->close();
			add_action( 'update_option_active_plugins', array( $this, 'activate_toolbox' ) );
		else :
			$this->activation_error = $res;
			add_action( 'admin_notices', array( $this, 'toolbox_install_failed' ) );
		endif;
	}

	/**
	 * Activate Lucid Toolbox.
	 */
	public function activate_toolbox() {

		// Make sure the plugin list is updated
		wp_cache_flush();
		$toolbox_basename = $this->wp_plugin_dir . $this->toolbox_basename;
		$activated = activate_plugin( $toolbox_basename );

		// activate_plugin returns WP_Error on failure and null on success
		if ( is_wp_error( $activated ) ) :
			$this->activation_error = $activated->get_error_message();
			add_action( 'admin_notices', array( $this, 'toolbox_install_failed' ) );
		elseif ( is_null( $activated ) ) :
			add_action( 'admin_notices', array( $this, 'toolbox_install_success' ) );
		else :
			$this->activation_error = __( 'Unknown error', 'lucid-slider' );
			add_action( 'admin_notices', array( $this, 'toolbox_install_failed' ) );
		endif;
	}

	/**
	 * Admin notice for install failure.
	 */
	public function toolbox_install_failed() {
		$notice = sprintf( __( 'Lucid Toolbox couldn\'t be activated, error message: %s', 'lucid-slider' ), $this->activation_error );
		printf( '<div class="error"><p>%s</p></div>', $notice );
	}

	/**
	 * Admin notice for install success.
	 */
	public function toolbox_install_success() {
		printf( '<div class="updated"><p>%s</p></div>', __( 'Lucid Toolbox was activated successfully!', 'lucid-slider' ) );
	}
}