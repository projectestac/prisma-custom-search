<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://kiwop.com
 * @since      1.0.0
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/includes
 * @author     Antonio Sánchez (kiwop) <antonio@kiwop.com>
 */
class Prisma_Custom_Search_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'prisma-custom-search',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
