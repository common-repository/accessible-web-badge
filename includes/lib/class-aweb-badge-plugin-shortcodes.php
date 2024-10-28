<?php
/**
 * Shortcodes file.
 *
 * @package Aweb Badge Plugin/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin API class.
 */
class Aweb_Badge_Plugin_Shortcodes {

	/**
	 * Constructor function
	 */
	public function __construct() {
		add_shortcode( 'accessible_web_target_snippet', array( $this, 'target_snippet_shortcode' ) );
	}

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param array  $atts An array of shortcode attributes.
	 * @param string $content A string of inner html.
	 * @return string
	 */
	public function target_snippet_shortcode( $atts, $content = null ) {
		if ( get_option( 'aweb_api_key', null ) === null ) {
			// short circuit.
			return;
		}

		$a = shortcode_atts(
			array(),
			$atts
		);

		if ( $content ) {
			return sprintf( '<!-- Begin Accessible Web Text Only Target Snippet --><a href="#" data-awam-target>%s</a><!-- End Accessible Web Text Only Target Snippet -->', $content );
		}
		return '<!-- Begin Accessible Web A11Y Center Button Target Snippet -->
<div data-awam-target style="display:none;"></div> 
<!-- End Accessible Web A11Y Center Button Target Snippet -->';
	}

}
