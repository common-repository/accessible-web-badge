<?php
/**
 * Settings class file.
 *
 * @package Aweb Badge Plugin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Aweb_Badge_Plugin_Settings {

	/**
	 * The single instance of Aweb_Badge_Plugin_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for Accessible Web Badge.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'aweb_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register Accessible Web Badge.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of Accessible Web Badge page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {

		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings() {
		return apply_filters(
			//phpcs:disable
			$this->base . 'menu_settings',
			//phpcs:enable
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'Accessible Web A11Y Center', 'aweb-badge-plugin' ),
				'menu_title'  => __( 'A11Y Center', 'aweb-badge-plugin' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->_token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings( $settings = array() ) {
		return $settings;
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'aweb-badge-plugin' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		ob_start(); ?>

		<p>To get started simply add your api key from your website in Accessible Web RAMP and save! <a href="https://accessibleweb.com/how-to-find-your-api-key/" target="_blank" rel="noreferrer">Learn how to find your api key here.</a></p>

		<?php
		$api_key = get_option( 'aweb_api_key', null );
		if ( null !== $api_key ) {
			?>
			<p>The button will embed your accessibility center into your website. You can <a href="https://ramp.accessibleweb.com/a11ycenter/<?php echo esc_attr( $api_key ); ?>/" target="_blank" rel="noreferrer">review your accessibility center</a> if you'd like to verify its appearance.</p>
			<?php
		}
		?>

		<a href="#" role="button" data-aweb-accordion aria-expanded="false" aria-controls="advanced-installation-options">Show Advanced Installation Options</a>

		<div style="display:none;" id="advanced-installation-options">

		<?php
		$target_snippet            = '<!-- Begin Accessible Web A11Y Center Button Target Snippet -->
<div data-awam-target style="display:none;"></div> 
<!-- End Accessible Web A11Y Center Button Target Snippet -->';
		$text_snippet              = '<!-- Begin Accessible Web A11Y Center Text Only Target Snippet -->
<a href="#" data-awam-target>View Accessibility Policy</a> 
<!-- End Accessible Web A11Y Center Text Only Target Snippet -->';
		$shortcode_snippet_default = '[accessible_web_target_snippet]';
		$shortcode_snippet_text    = '[accessible_web_target_snippet]View our Accessibility Center[/accessible_web_target_snippet]';
		?>

		<h3>Website Target Snippet</h3>
		<p>
			This snippet is <b>optional</b>, and used alongside the previous snippet. It allows you to place the button 
			in a specific location on the page.
		</p>
		<pre><code style="display:inline-block;"><?php echo esc_html( $target_snippet ); ?></code></pre>
		<p>
			To embed this as a simple shortcode use:
		</p>
		<pre><code><?php echo esc_html( $shortcode_snippet_default ); ?></code></pre>
		<h3>Text Only Button Snippet</h3>
		<p>
			This snippet is also <strong>optional</strong>, and can be used in place of the previous snippet. It allows you to embed a text-only link on your website instead of the standard button. This will allow you to show a link to your accessibility center that matches your websites styles.
		</p>
		<pre><code style="display:inline-block;"><?php echo esc_html( $text_snippet ); ?></code></pre>
		<p>
			To embed this as a simple shortcode use:
		</p>
		<pre><code><?php echo esc_html( $shortcode_snippet_text ); ?></code></pre>
		<p>
			You may feel free to edit the content inside of the link however you like, but <strong>be sure to follow accessibility best practices!</strong>
		</p>

		</div> <!-- #advanced-installation-options -->

		<script type="text/javascript">
			var awebSettings = document.getElementById('aweb_badge_plugin_settings');

			function accordionTrigger(event) {
				if (!event.target.hasAttribute('data-aweb-accordion')) return;
				if (!/click|keydown/.test(event.type)) return;
				if (event.type === 'keydown' && event.code !== 'Space') return;
				event.preventDefault();

				var accordion = awebSettings.querySelector(`#${event.target.getAttribute('aria-controls')}`);
				if (!accordion) return;

				var expanded = event.target.getAttribute('aria-expanded') === 'true';
				accordion.style.display = expanded ? 'none' : 'block';
				event.target.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			}

			awebSettings.addEventListener('click', accordionTrigger);
			awebSettings.addEventListener('keydown', accordionTrigger);
		</script>

		<?php
		$description          = ob_get_clean();
		$settings['standard'] = array(
			'title'       => __( 'Settings', 'aweb-badge-plugin' ),
			'description' => $description,
			'fields'      => array(
				array(
					'id'          => 'api_key',
					'label'       => __( 'API Key', 'aweb-badge-plugin' ),
					'description' => __( 'Your api key from Accessible Web RAMP.', 'aweb-badge-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Enter your API key', 'aweb-badge-plugin' ),
				),
			),
		);
		//phpcs:disable
		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );
		//phpcs:enable

		return $settings;
	}

	/**
	 * Register Accessible Web Badge
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			//phpcs:disable
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = sanitize_text_field($_POST['tab']);
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = sanitize_text_field($_GET['tab']);
				}
			}
			//phpcs:enable

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; //phpcs:ignore
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html      = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Accessible Web A11Y Center', 'aweb-badge-plugin' ) . '</h2>' . "\n";

			$tab = '';
		//phpcs:disable
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= sanitize_text_field($_GET['tab']);
		}
		//phpcs:enable

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) { //phpcs:ignore
					if ( 0 === $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) { //phpcs:ignore
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) { //phpcs:ignore
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'aweb-badge-plugin' ) ) . '" />' . "\n";
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";

		echo $html; //phpcs:ignore
	}

	/**
	 * Main Aweb_Badge_Plugin_Settings Instance
	 *
	 * Ensures only one instance of Aweb_Badge_Plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Aweb_Badge_Plugin()
	 * @param object $parent Object instance.
	 * @return object Aweb_Badge_Plugin_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()
}
