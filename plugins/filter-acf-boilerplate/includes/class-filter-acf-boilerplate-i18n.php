<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       adrianfelder.ch
 * @since      1.0.0
 *
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Filter_Acf_Boilerplate
 * @subpackage Filter_Acf_Boilerplate/includes
 * @author     Adrian Felder <adrianfelder@gmx.ch>
 */
class Filter_Acf_Boilerplate_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'filter-acf-boilerplate',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
