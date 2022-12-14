<?php

/**
 *
 *
 * @link              https://dayzsolutions.com
 * @since             1.0.0
 * @package           E-Zearch
 *
 * @wordpress-plugin
 * Plugin Name:       E-Zearch
 * Plugin URI:        https://dayzsolutions.com
 * Description:       Fastest searcher in the field.
 * Version:           1.0.0
 * Author:            DayZ Solutions
 * Author URI:        https://dayzsolutions.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ezearch
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
define( 'EZEARCH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zearch-activator.php
 */
function activate_zearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zearch-activator.php';
	Zearch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zearch-deactivator.php
 */
function deactivate_zearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zearch-deactivator.php';
	Zearch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zearch' );
register_deactivation_hook( __FILE__, 'deactivate_zearch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

// ELastic Search SDK
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-zearch.php';

// $client = Elastic\Elasticsearch\ClientBuilder::create()->build();


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zearch() {

	$plugin = new EZearch();
	$plugin->run();

}
run_zearch();
