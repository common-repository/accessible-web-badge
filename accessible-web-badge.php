<?php
/**
 * Plugin Name: Accessible Web A11Y Center
 * Version: 1.2.2
 * Plugin URI: https://www.accessibleweb.com/
 * Description: A helper plugin to install the Accessible Web A11Y Center.
 * Author: Accessible Web
 * Requires at least: 5.0
 * Tested up to: 6.6
 *
 * @package WordPress
 * @author Accessible Web
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-aweb-badge-plugin.php';
require_once 'includes/class-aweb-badge-plugin-settings.php';

// Load plugin libraries.
require_once 'includes/lib/class-aweb-badge-plugin-admin-api.php';
require_once 'includes/lib/class-aweb-badge-plugin-shortcodes.php';

/**
 * Returns the main instance of Aweb_Badge_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Aweb_Badge_Plugin
 */
function aweb_badge_plugin() {
	$instance = Aweb_Badge_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Aweb_Badge_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

aweb_badge_plugin();
