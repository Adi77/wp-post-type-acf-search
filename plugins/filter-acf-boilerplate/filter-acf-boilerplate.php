<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              adrianfelder.ch
 * @since             1.0.0
 * @package           Filter_Acf_Boilerplate
 *
 * @wordpress-plugin
 * Plugin Name:       filter-acf-boilerplate
 * Plugin URI:        adrianfelder.ch
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Adrian Felder
 * Author URI:        adrianfelder.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       filter-acf-boilerplate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FILTER_ACF_BOILERPLATE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-filter-acf-boilerplate-activator.php
 */
function activate_filter_acf_boilerplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-filter-acf-boilerplate-activator.php';
	Filter_Acf_Boilerplate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-filter-acf-boilerplate-deactivator.php
 */
function deactivate_filter_acf_boilerplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-filter-acf-boilerplate-deactivator.php';
	Filter_Acf_Boilerplate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_filter_acf_boilerplate' );
register_deactivation_hook( __FILE__, 'deactivate_filter_acf_boilerplate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-filter-acf-boilerplate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_filter_acf_boilerplate() {

	$plugin = new Filter_Acf_Boilerplate();
	$plugin->run();

}
run_filter_acf_boilerplate();
