<?php

/**
 * @link              https://www.vividworks.com
 * @since             0.1.0
 * @package           Vwplume
 *
 * @wordpress-plugin
 * Plugin Name:       VividWorks 3D E-Commerce Configurator
 * Description:       Online selling like in-store selling with full 3D visual product configurator
 * Version:           1.2.5
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            VividWorks Oy
 * Author URI:        https://www.vividworks.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vwplume
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
define( 'VWPLUME_VERSION', '1.2.5' );

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
* Check for the existence of WooCommerce and any other requirements
*/
function vwplume_check_requirements() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        return true;
    } else {
        add_action( 'admin_notices', 'vwplume_missing_wc_notice' );
        return false;
    }
}


/**
* Display a message advising WooCommerce is required
*/
function vwplume_missing_wc_notice() { 
    $class = 'notice notice-error';
    $message = __( 'VividWorks 3D E-Commerce Configurator requires WooCommerce to be installed and active.', 'vwplume' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vwplume-activator.php
 */
function activate_vwplume() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-vwplume-activator.php';
    Vwplume_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vwplume-deactivator.php
 */
function deactivate_vwplume() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-vwplume-deactivator.php';
    Vwplume_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vwplume' );
register_deactivation_hook( __FILE__, 'deactivate_vwplume' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vwplume.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_vwplume() {

    $plugin = new Vwplume();
    $plugin->run();

}
run_vwplume();