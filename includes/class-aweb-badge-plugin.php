<?php
/**
 * Main plugin class file.
 *
 * @package Aweb Badge Plugin/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Aweb_Badge_Plugin {

	/**
	 * The single instance of Aweb_Badge_Plugin.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of Aweb_Badge_Plugin_Admin_API
	 *
	 * @var Aweb_Badge_Plugin_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * The plugin shortcodes instance
	 *
	 * @var     Aweb_Badge_Plugin_Shortcodes
	 * @access  public
	 * @since   1.0.0
	 */
	public $shortcodes = null;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'aweb_badge_plugin';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new Aweb_Badge_Plugin_Admin_API();
		}

		add_action( 'wp_footer', array( $this, 'maybe_inject_badge' ), 10, 1 );

		add_action( 'init', array( $this, 'init_shortcodes' ), 10 );

		// Handle localisation.
	} // End __construct ()

	/**
	 * Inject badge snippet if enabled
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function maybe_inject_badge() {
		$api_key = get_option( 'aweb_api_key', null );
		if ( null === $api_key ) {
			// short circuit.
			return;
		}
		$this->render_badge_snippet( $api_key );
	} // End maybe_inject_badge ()

	/**
	 * Inject badge snippet if enabled
	 *
	 * @param string $api_key Customer API Key.
	 *
	 * @access  public
	 * @return  string
	 * @since   1.0.0
	 */
	public function render_badge_snippet( $api_key ) {
		if ( ! $api_key ) {
			return;
		}
		?>
		<!-- Begin Accessible Web A11Y Center Button Snippet --> 
		<script async defer 
			id="aweb-script"
			type="text/javascript"
			src="https://ramp.accessibleweb.com/badge/<?php echo esc_attr( $api_key ); ?>/script.js"></script> 
		<!-- End Accessible Web A11Y Center Button Snippet --> 
		<?php
	}

	/**
	 * Initialise shortcodes
	 *
	 * @return void
	 */
	public function init_shortcodes() {
		$this->shortcodes = new Aweb_Badge_Plugin_Shortcodes();
	}

	/**
	 * Main Aweb_Badge_Plugin Instance
	 *
	 * Ensures only one instance of Aweb_Badge_Plugin is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object Aweb_Badge_Plugin instance
	 * @see Aweb_Badge_Plugin()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

}
