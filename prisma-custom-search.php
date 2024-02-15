<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://kiwop.com
 * @since             1.0.0
 * @package           Prisma_Custom_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Kiwop - Prisma Custom Search
 * Plugin URI:        https://kiwop.com
 * Description:       Permet cercar posts per title, descripcio i amb camps ACF assignats a per cada tipus de post
 * Version:           1.0.0
 * Author:            Antonio Sánchez (kiwop)
 * Author URI:        https://kiwop.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prisma-custom-search
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
define( 'PRISMA_CUSTOM_SEARCH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-prisma-custom-search-activator.php
 */
function activate_prisma_custom_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prisma-custom-search-activator.php';
	Prisma_Custom_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-prisma-custom-search-deactivator.php
 */
function deactivate_prisma_custom_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prisma-custom-search-deactivator.php';
	Prisma_Custom_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_prisma_custom_search' );
register_deactivation_hook( __FILE__, 'deactivate_prisma_custom_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-prisma-custom-search.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_prisma_custom_search() {

	$plugin = new Prisma_Custom_Search();
	$plugin->run();

}
run_prisma_custom_search();
