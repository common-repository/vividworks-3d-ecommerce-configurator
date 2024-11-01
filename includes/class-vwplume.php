<?php

/**
 * VividWorks Plume plugin core
 *
 * @link       https://www.vividworks.com
 * @since      0.1.0
 *
 * @package    Vwplume
 * @subpackage Vwplume/includes
 */

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Features;

/**
 * VividWorks Plume plugin core
 *
 * @since      0.1.0
 * @package    Vwplume
 * @subpackage Vwplume/includes
 * @author     VividWorks Oy <info@vividworks.com>
 */
class Vwplume {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      Vwplume_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function __construct() {
        if ( defined( 'VWPLUME_VERSION' ) ) {
            $this->version = VWPLUME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'vwplume';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Vwplume_Loader. Orchestrates the hooks of the plugin.
     * - Vwplume_i18n. Defines internationalization functionality.
     * - Vwplume_Admin. Defines all hooks for the admin area.
     * - Vwplume_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vwplume-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vwplume-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vwplume-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vwplume-public.php';

        $this->loader = new Vwplume_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Vwplume_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Vwplume_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Vwplume_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        // $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_product_options_page' );

        // Add product mapping tab under "Products"
        $this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'vwplume_add_product_configuration_tab' );
        $this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'vwplume_add_product_configuration_fields' );
        $this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'woocommerce_process_product_meta_fields_save', 10 );

        // Add plugin settings to WooCommerce
        $this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'vwplume_add_settings' );

        // Assembly data in Orders
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'vwplume_order_add_meta_boxes', 10, 2 );
        $this->loader->add_action( 'admin_post_vwplume_download_assembly', $plugin_admin, 'vwplume_download_assembly' );
        $this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_admin, 'vwplume_hide_order_item_meta' );
        $this->loader->add_action( 'woocommerce_after_order_itemmeta', $plugin_admin, 'vwplume_after_order_itemmeta', 10, 3 );
        
        // Extend Composite Product settings
        $this->loader->add_action( 'woocommerce_composite_component_admin_config_html', $plugin_admin, 'vwplume_wccp_component_config_extra', -1, 3 );
        $this->loader->add_action( 'admin_post_vwplume_duplicate_component', $plugin_admin, 'vwplume_duplicate_component' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Vwplume_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        
        // The standard WooCommerce hooks
        $this->loader->add_action( 'woocommerce_before_single_product_summary', $plugin_public, 'plume_replace_product_summary', 0);
        $this->loader->add_action( 'woocommerce_before_single_product_summary', $plugin_public, 'plume_before_product_summary', 0);
        $this->loader->add_action( 'woocommerce_after_single_product_summary', $plugin_public, 'plume_after_product_summary', 0);

        $this->loader->add_action( 'woocommerce_before_single_product', $plugin_public, 'plume_before_product', 0);
        $this->loader->add_action( 'woocommerce_after_single_product', $plugin_public, 'plume_after_product', 0);

        // Modify cart editing possibilities in case of composited items
        $this->loader->add_action( 'woocommerce_cart_item_remove_link', $plugin_public, 'cart_item_remove_link', 11, 2);
        $this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_public, 'composite_cart_item_quantity', 11, 2 );
        // For future use, if we need to add data to cart_item_data
        // $this->loader->add_filter( 'woocommerce_composited_cart_item_data', $plugin_public, 'add_composited_cart_item_data', 10, 2 );

        // Order meta data
        $this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'add_metadata_to_order', 11, 4 );

        // Custom cart & checkout meta data
        $this->loader->add_filter('woocommerce_get_item_data', $plugin_public, 'get_item_data', 11, 2);
        $this->loader->add_filter('woocommerce_composite_container_cart_item', $plugin_public, 'get_composite_item_data', 11, 2);
        $this->loader->add_filter('woocommerce_composite_container_cart_item_data', $plugin_public, 'get_composite_parent_item_data', 11, 3);
        $this->loader->add_filter('woocommerce_checkout_blocks_api_composite_data', $plugin_public, 'modify_checkout_blocks_api_composite_data', 11, 2);

        // Custom hook to add viewer anywhere
        $this->loader->add_action( 'vividworks_product_configurator', $plugin_public, 'plume_render_embedded_configurator', 0);

        // AJAX handlers
        $this->loader->add_action( 'wc_ajax_vwplume_addtocart', $plugin_public, 'vwplume_addtocart' );
        $this->loader->add_action( 'wc_ajax_vwplume_getprices', $plugin_public, 'vwplume_getprices' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.1.0
     * @return    Vwplume_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
