<?php

/**
 * The main public facing functionality. 
 *
 * @link       https://www.vividworks.com
 * @since      0.1.0
 *
 * @package    Vwplume
 * @subpackage Vwplume/public
 */

/**
 * The main public facing functionality
 *
 * @package    Vwplume
 * @subpackage Vwplume/public
 * @author     VividWorks Oy <info@vividworks.com>
 */
class Vwplume_Public {

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Vwplume_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Vwplume_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vwplume-public.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        add_filter( 'script_loader_tag', array($this, 'handle_script_tags'), 10, 3 );

        wp_enqueue_script( $this->plugin_name . '_config', plugin_dir_url( __FILE__ ) . 'js/vwplume-config.js', array( 'jquery' ), $this->version, false );

        $this->add_js_configuration_data();

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vwplume-public.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name . '_embed', plugin_dir_url( __FILE__ ) . 'js/vwplume-embed.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Append a generic Plume related configuration data to the public JavaScript
     *
     * @since    1.0.0
     */
    private function add_js_configuration_data() {
        global $post;

        // Product info
        $product_id = '';
        $vwplume_library_id = '';
        $vwplume_product_id = '';
        $product_cart_enabled = 'yes';
        if (class_exists( 'woocommerce' ) && isset($post) && !empty($post) && isset($post->ID) ) {
            $product = wc_get_product($post);
            if (isset($product) && !empty($product)) {
                $product_id = $post->ID;
                $vwplume_library_id = metadata_exists('post', $post->ID, 'vwplume_library_id') ? get_post_meta($post->ID, 'vwplume_library_id', true) : '';
                $vwplume_product_id = metadata_exists('post', $post->ID, 'vwplume_product_id') ? get_post_meta($post->ID, 'vwplume_product_id', true) : '';
                $product_cart_enabled = metadata_exists('post', $post->ID, 'vwplume_cart_enabled') ? get_post_meta($post->ID, 'vwplume_cart_enabled', true) : 'yes';
            }
        }
        
        // Custom add-to-cart
        $cart_custom_js = get_option('vwplume_cart_custom_js');

        // Add-to-cart, or not
        $cart_enabled = get_option('vwplume_cart_enabled', true);

        if ($cart_enabled == 'yes') {
            $cart_enabled = !empty($product_id)
                ? $product_cart_enabled == 'yes'
                : true;
        }
        else {
            $cart_enabled = false;
        }

        // Locale
        $locale = get_locale();

        // Unfortunate "hack" to fix Finnish language. This is because
        // WordPress for unkown reasons outputs 'fi' for Finnish even
        // if for other languages it uses the full ISO code ('fi_FI').
        if ($locale == 'fi') {
            $locale = 'fi-FI';
        }

        $wpml_lang = apply_filters( 'wpml_current_language', NULL );

        $vw_params = json_encode(array(
            'library_id'        => esc_js($vwplume_library_id),
            'product_id'        => esc_js($vwplume_product_id),
            'wc_product_id'     => esc_js($product_id),
            'wpml_lang'         => esc_js($wpml_lang),
            'locale'            => esc_js($locale),
            'cart_custom_js'    => !empty($cart_custom_js),
            'cart_enabled'      => $cart_enabled
        ));

        $code = 'if (typeof window[\'VWPlume\'] == \'undefined\') { window[\'VWPlume\'] = {}; }; VWPlume.custom_cart_event = function(){' . $cart_custom_js . '};' . 'VWPlume.params = ' . $vw_params;

        wp_add_inline_script( $this->plugin_name . '_config', $code);
    }
    
    /**
     * Append a generic Plume related configuration script in the header
     *
     * @since    0.3.1
     */
    public function header_script() {
    }

    /**
     * Optional extra handling for script tag creation.
     *
     * @since    0.1.0
     */
    public function handle_script_tags( $tag, $handle, $src ) {
        if (!str_contains($handle, $this->plugin_name)) {
            return $tag;
        }

        // Handle embedding script
        if ( $this->plugin_name . '_embed' === $handle ) {
            $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
        }
        return $tag;
    }

    /**
     * Check whether or not the embed should show up on the Product Page
     *
     * @since    0.1.0
     */
    private function show_on_product_page() {
        global $post;
        $vwplume_enabled = metadata_exists('post', $post->ID, 'vwplume_embed_enabled');
        $vwplume_library_id = metadata_exists('post', $post->ID, 'vwplume_library_id') ? get_post_meta($post->ID, 'vwplume_library_id', true) : '';
        $vwplume_product_id = metadata_exists('post', $post->ID, 'vwplume_product_id') ? get_post_meta($post->ID, 'vwplume_product_id', true) : '';
        
        // Only show if we have product data and the shop owner wants to show the planner
        return $vwplume_enabled && !empty($vwplume_library_id) && !empty($vwplume_product_id);
    }

    /**
     * Check from Post / Product meta data where on the page the
     * embed should be located
     *
     * @since    0.1.0
     */
    private function get_productpage_position() {
        global $post;
        return metadata_exists('post', $post->ID, 'vwplume_productpage_position') ? (int) get_post_meta($post->ID, 'vwplume_productpage_position', true) : 0;
    }

    /**
     * Action to place the VividWorks embed replacing the normal
     * Product Summary
     *
     * @since    0.1.0
     */
    public function plume_replace_product_summary() {
        if ($this->show_on_product_page() && $this->get_productpage_position() == 0) {
            /**
              * WooCommerce 6.2.0 originals
              *
              * woocommerce_show_product_sale_flash - 10
              * woocommerce_show_product_images - 20
              */
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
            remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
            
            /**
             * WooCommerce 6.2.0 originals
             *
             * woocommerce_template_single_title - 5
             * woocommerce_template_single_rating - 10
             * woocommerce_template_single_price - 10
             * woocommerce_template_single_excerpt - 20
             * woocommerce_template_single_add_to_cart - 30
             * woocommerce_template_single_meta - 40
             * woocommerce_template_single_sharing - 50
             * WC_Structured_Data::generate_product_data() - 60
             */
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

            // add_action('woocommerce_single_product_summary', array($this, 'add_embed'), 15);
            $this->add_embed("productpage-replace-summary");
        }
    }

    /**
     * Action to place the VividWorks embed before / above
     * the WooCommerce Product Summary
     *
     * @since    0.1.0
     */
    public function plume_before_product_summary() {
        if ($this->show_on_product_page() && $this->get_productpage_position() == 1) {
            $this->add_embed("productpage-before-summary");
        }
    }

    /**
     * Action to place the VividWorks embed after / below
     * the WooCommerce Product Summary
     *
     * @since    0.1.0
     */
    public function plume_after_product_summary() {
        if ($this->show_on_product_page() && $this->get_productpage_position() == 2) {
            $this->add_embed("productpage-after-summary");
        }
    }
    /**
     * Action to place the VividWorks embed before / above
     * the WooCommerce Product Summary
     *
     * @since    0.2.0
     */
    public function plume_before_product() {
        if ($this->show_on_product_page() && $this->get_productpage_position() == 3) {
            $this->add_embed("productpage-before");
        }
    }

    /**
     * Action to place the VividWorks embed after / below
     * the WooCommerce Product Summary
     *
     * @since    0.2.0
     */
    public function plume_after_product() {
        if ($this->show_on_product_page() && $this->get_productpage_position() == 4) {
            $this->add_embed("productpage-after");
        }
    }

    /**
     * Action to render the embedded configurator anywhere,
     * this is called via the hook/action "vividworks_product_configurator".
     * 
     * Use <?php do_action( 'vividworks_product_configurator' ); ?> to
     * invoke anywhere.
     *
     * @since    0.2.0
     */
    public function plume_render_embedded_configurator() {
        global $post;
        $prod = wc_get_product($post);
        if (!empty($prod)) {
            $this->add_embed("custom");
        }
    }

    /**
     * Function to create the actual embedding
     *
     * @since    0.1.0
     * @param    string    $hook    Hook name. Used for CSS. Can be "replace", "before" or "after".
     */
    public function add_embed($hook) {
        global $post;

        $product_id = $post->ID;
        $vwplume_library_id = metadata_exists('post', $post->ID, 'vwplume_library_id') ? get_post_meta($post->ID, 'vwplume_library_id', true) : '';
        $vwplume_product_id = metadata_exists('post', $post->ID, 'vwplume_product_id') ? get_post_meta($post->ID, 'vwplume_product_id', true) : '';

        // Only show if we have product data and the shop owner wants to show the planner
        if (!empty($vwplume_library_id) && !empty($vwplume_product_id)) {

            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/vwplume-embed.php';

        }
    }

    /**
     * Function to find a WC product for a SKU gotten from VividWorks platform
     *
     * @since    0.1.0
     * @deprecated 0.2.1    slow
     * @param    array          $products    An array of WC products
     * @param    string         $sku         SKU to search for
     * @return   Post | null                 A WC Product object, or null
     */
    private function find_product_for_sku($products, $sku) {
        foreach( $products as $product) {
            $product_sku = $product->get_sku();
            $product_type = $product->get_type();
            $sku_match = $sku == $product->get_sku();

            if ($product_type == "variable" && !$sku_match) {
                $variations = $product->get_available_variations($return = "objects");
                $found = $this->find_product_for_sku($variations, $sku);
                if (empty($found)) {
                    continue;
                }
                return $found;
            }
            else if ($sku_match ) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Function to find a WC products for an array of SKUs 
     * gotten from VividWorks platform
     *
     * @since    0.2.1
     * @param    string         $skus        SKU to search for
     * @return   array                       Array of WP Posts
     */
    private function get_products_for_skus($skus) {
        $meta_query = array(
            'relation' => 'OR'
        );
        if(is_array($skus) && $skus) {
            foreach($skus as $sku) {
                $meta_query[] = array(
                    'key' => '_sku',
                    'value' => $sku,
                    'compare' => '='
                );
            }
        }
    
        $args = array(
            'posts_per_page'  => -1,
            'post_type'       => array( 'product', 'product_variation' ),
            'post_status'     => array( 'private', 'publish' ),
            'meta_query'      => $meta_query
        );
        // $posts = get_posts( $args );
        $q = new WP_Query( $args );
        return $q->get_posts();
    }

    /**
     * Check whether we are adding a composite product to cart
     *
     * @since    1.1.0
     * @return   boolean
     */
    private function is_adding_composite($product_id) {
        if (function_exists( 'WC_CP' )) {
            $product = wc_get_product($product_id);
            if (is_object($product) && is_a($product, 'WC_Product')) {
                $vwplume_composite_product = (int)(metadata_exists('post', $product->get_id(), 'vwplume_composite_product') ? get_post_meta($product->get_id(), 'vwplume_composite_product', true) : '');
                return $vwplume_composite_product;
            }
        }
        return 0;
    }

    /**
     * Ajax function end-point to add products into cart. Expects to 
     * have a VividWorks BOM object in the $_POST, which is then parsed
     * for SKUs, which are used to find applicable WC products.
     * 
     * Invoked via AJAX.
     *
     * @since    0.1.0
     */
    public function vwplume_addtocart() {
        ob_start();

        // Added checks for empty data
        if ( empty( $_POST ) || empty($_POST["bom"]) || empty($_POST["bom"]["totals"])) {
            return;
        }

        // The BOM JSON model. Sanitize the whole array using `sanitize_text_field`
        $plume_bom = $this->array_map_recursive('sanitize_text_field', $_POST["bom"]);
        $product_id = sanitize_text_field($_POST["product_id"]);
        $assembly_map = !empty($_POST["assembly"])
            ? $this->array_map_recursive('sanitize_text_field', $_POST["assembly"])
            : null;
        $readOnlyLink = sanitize_text_field($_POST["readOnlyLink"]);
        $shareLink = sanitize_text_field($_POST["shareLink"]);

        if (empty($plume_bom)) {
            return;
        }

        $cartitems = array();
        $items_added = array();
        $error     = false;

        // Match SKUs
        $skus_amounts = array();
        $products = array();
        $matches = array();

        // Get the SKU codes and amounts 
        if (!empty($plume_bom["totals"]) && is_array($plume_bom["totals"])) {
            foreach( $plume_bom["totals"] as $total_row ) {
                $sku = $total_row["code"];
                $amount = $total_row["amount"];

                $skus_amounts[$sku] = absint($amount);
            }
        }
        else {
            wp_send_json_error(__("Nothing to add.","vwplume"), 400);
            return;
        }

        $product_posts = $this->get_products_for_skus(array_keys($skus_amounts));

        foreach( $plume_bom["totals"] as $total_row ) {
            $sku = $total_row["code"];
            $amount = absint($total_row["amount"]);
            $properties = !empty($total_row["properties"]) && is_array($total_row["properties"])
                ? $total_row["properties"]
                : array();
            
            foreach( $product_posts as $post ) {
                if (!empty($post) && isset( $post, $post->ID )) {
                    $prod = wc_get_product($post);
                    if ($sku == $prod->get_sku()) {
                        $matches[] = array(
                            'product' => $prod,
                            'amount'  => $skus_amounts[$prod->get_sku()],
                            'properties'  => $properties,
                            'readOnlyLink' => $readOnlyLink,
                            'shareLink' => $shareLink
                        );
                    }
                }
            }
        }

        $products_to_add = $this->validate_products($matches, $error);

        if (!empty($products_to_add) && !$error) {
            $composite = $this->is_adding_composite($product_id);
            if ( is_numeric( $composite ) && $composite > 0 ) {
                $composite = wc_get_product( $composite );
            }
            $adding_composite = is_object( $composite ) && $composite->is_type( 'composite' );
            
            if ( $adding_composite ) {
                $this->add_composite_to_cart($composite, $products_to_add, $assembly_map, $cartitems, $items_added, $error);
            }
            else {
                $this->add_products_to_cart($products_to_add, $cartitems, $items_added, $error);
            }
        }

        // DONE
        if (!$error) {
            //wp_safe_redirect( wc_get_cart_url() );
            
            if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
                wc_add_to_cart_message( $items_added, true );
            }
            WC_AJAX::get_refreshed_fragments();
            wp_send_json(
                array(
                    'message' =>  __("Your cart has been updated. Would you like to go to the cart?","vwplume"),
                ),
                200
            );
        }
        else {
            if (!empty($cartitems)) {
                foreach($cartitems as $cartitem_key => $quantity) {
                    $cartitem = WC()->cart->get_cart_item($cartitem_key);
                    $cart_quantity = absint($cartitem['quantity']);
                    if ($cart_quantity > $quantity) {
                        WC()->cart->set_quantity($cartitem_key, $cart_quantity-$quantity);
                    }
                    else {
                        WC()->cart->remove_cart_item($cartitem_key);
                    }
                }
            }

            wp_send_json_error(__("Some of the products could not be added to the cart.","vwplume"), 400);
        }
        
    }

    /**
     * Go through the SKU matched products, validate then
     * and gather an array of data to be added to the cart.
     * 
     * @since    1.1.0
     */
    private function validate_products($matches, $error) {
        $products_to_add = array();

        foreach($matches as $match) {
            $product           = $match['product'];
            $product_id        = !empty($product->ID)
                ? absint($product->ID)
                : absint($product->get_id());
            
            // Exit early if we get no ID
            if (empty($product_id)) {
                $error = true;
                break;
            }
                
            $quantity          = empty( $match['amount'] ) ? 1 : wc_stock_amount( $match['amount'] );
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
            $product_status    = get_post_status( $product_id );
            $variation_id      = 0;
            $variation         = array();

            if ( 'variation' === $product->get_type() ) {
                // If this was a variation, switch the IDs
                // and get variation data
                $variation_id = $product_id;
                $product_id   = $product->get_parent_id();
                $variation    = $product->get_variation_attributes();
            }
            
            if ($passed_validation) {
                $product_to_add = array(
                    'product_id'    => $product_id,
                    'quantity'      => $quantity,
                    'variation_id'  => $variation_id,
                    'variation'     => $variation,
                    'properties'    => array()
                );

                // Process additional properties / item data,
                // and add "vwplume_meta_" prefix to the data
                if (isset($match['properties']) && !empty($match['properties']) && is_array($match['properties'])) {
                    $properties = array();
                    
                    foreach($match['properties'] as $key => $value) {
                        $properties["vwplume_meta_".$key] = $value;
                    }

                    $product_to_add["properties"] = $properties;
                }

                // If present in the BOM / SalesData, add readOnlyLink and shareLink, for all items
                if (isset($match['readOnlyLink']) && !empty($match['readOnlyLink'])) {
                    $product_to_add["properties"]['vwplume_readOnlyLink'] = $match['readOnlyLink'];
                }
                if (isset($match['shareLink']) && !empty($match['shareLink'])) {
                    $product_to_add["properties"]['vwplume_shareLink'] = $match['shareLink'];
                }

                $products_to_add[] = $product_to_add;
            }
            else {
                $error = true;
                break;
            }
        }

        return $products_to_add;
    }

    /**
     * Add a Composite Product to the cart
     * 
     * @since    1.1.0
     */
    private function add_composite_to_cart($composite, $products_to_add, $assembly_map, $cartitems, $items_added, $error) {
        $composite_configuration = array();
        $components = $composite->get_components();
        $component_map = array();
        
        foreach ( $components as $component_id => $component ) {
            $component_map[$component_id] = $component->get_options();
        }

        foreach($products_to_add as $product_to_add) {
            if (empty($component_map)) {
                $error = true;
                return;
            }

            // Find out where we can add this product to
            $filter_by_product = function($options) use ($product_to_add) {
                return in_array($product_to_add['product_id'], $options);
            };
            $compatible_components = array_filter($component_map, $filter_by_product);

            // Add to first compatible component
            if (!empty($compatible_components)) {
                $component_id = array_keys($compatible_components)[0];
                $composite_configuration[ $component_id ] = array();
                $composite_configuration[ $component_id ][ 'product_id' ] = $product_to_add['product_id'];
                $composite_configuration[ $component_id ][ 'quantity' ]   = $product_to_add['quantity'];
                $composite_configuration[ $component_id ][ 'variation_id' ] = $product_to_add['variation_id'];
                $composite_configuration[ $component_id ][ 'attributes' ] = $product_to_add['variation'];
                $composite_configuration[ $component_id ][ 'properties' ] = $product_to_add['properties'];
            }

            // We only add once per component
            unset($component_map[$component_id]);
        }

        $composite_id = $composite->get_id();

        // Set this to recognize Plume made configurations
        $cart_item_data = array(
            'vwplume_composition' => 'yes'
        );
        if (!empty($assembly_map)) {
            $cart_item_data['vwplume_assembly'] = $assembly_map;
        }
        
        // If present in the BOM / SalesData, add readOnlyLink and shareLink, for all items
        if (isset($products_to_add[0]['vwplume_readOnlyLink']) && !empty($products_to_add[0]["properties"]['vwplume_readOnlyLink'])) {
            $cart_item_data['vwplume_readOnlyLink'] = $products_to_add[0]["properties"]['vwplume_readOnlyLink'];
        }
        if (isset($products_to_add[0]['vwplume_shareLink']) && !empty($products_to_add[0]["properties"]['vwplume_shareLink'])) {
            $cart_item_data['vwplume_shareLink'] = $products_to_add[0]["properties"]['vwplume_shareLink'];
        }
        
        $added = WC_CP()->cart->add_composite_to_cart($composite_id, 1, $composite_configuration, $cart_item_data);
        
        if (!is_a($added, 'WP_Error')) {
            $cartitems[$added ] = 1;
            $items_added[$composite_id] = 1;
            do_action( 'woocommerce_ajax_added_to_cart', $composite_id );
        }
        else {
            $error = true;
        }
    }

    /**
     * Add assembly information to the order line item.
     * 
     * @since    1.1.0
     */
    public function add_assembly_map_to_order( $order_item, $cart_item_key, $cart_item ) {
        if ( function_exists( 'WC_CP' ) && isset( WC()->cart->cart_contents[ $cart_item_key ][ 'composite_data' ] ) && !empty( WC()->cart->cart_contents[ $cart_item_key ][ 'composite_children' ] ) ) {
            $order_item->add_meta_data( '_vwplume_composition', $cart_item[ 'vwplume_composition' ], true );
            $order_item->add_meta_data( '_vwplume_assembly', $cart_item[ 'vwplume_assembly' ], true );
        }
	}

    /**
     * Add read-only link and share link to order items
     * 
     * @since    1.2.0
     */
    public function add_plan_links_to_order( $item, $cart_item_key, $values, $order ) {
        foreach($values as $key => $value) {
            if (in_array($key, array('vwplume_readOnlyLink', 'vwplume_shareLink'))) {
                $item->add_meta_data(
                    $key,
                    $value,
                    true
                );
            }
        }
	}

    /**
     * Add VWPlume related meta data to order lines.
     * 
     * @since    1.2.0
     */
    public function add_metadata_to_order( $item, $cart_item_key, $values, $order) {
        // Add meta fields such as those coming from our
        // integration data property sets
        $prefix = "vwplume_meta_";

        foreach($values as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $name = substr($key, strlen($prefix));
                $item->add_meta_data(
                    $name,
                    $value,
                    true
                );
            }
        }

        // Add assebly info of the composited cart items
        $this->add_assembly_map_to_order($item, $cart_item_key, $values);

        // Add plan links
        $this->add_plan_links_to_order($item, $cart_item_key, $values, $order);
    }

    /**
     * Add VWPlume related meta data to cart-items
     * 
     * @since    1.2.0
     */
    public function get_item_data($item_data, $cart_item_data) {
        $prefix = "vwplume_meta_";
        
        $isCompositeParent = isset($cart_item_data['vwplume_composition']) && !!$cart_item_data['vwplume_composition'];
        $isCompositeItem = isset($cart_item_data['composite_parent']) && !!$cart_item_data['composite_parent'];
        
        foreach($cart_item_data as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $name = substr($key, strlen($prefix));
                $item_data[] = array(
                    'key'   => __( $name, 'vwplume' ),
                    'value' => wc_clean( $value ),
                );
                
            }
            else if ($isCompositeParent && in_array($key, array('vwplume_shareLink'))) {
                $item_data[] = array(
                    'key'   => 'Shareable link',
                    'value' => wc_clean( $value ),
                );
            }
            else if ($isCompositeItem && in_array($key, array('vwplume_readOnlyLink', 'vwplume_shareLink'))) {
                // Do not show
            }
            else if (!$isCompositeParent && !$isCompositeItem && in_array($key, array('vwplume_shareLink'))) {
                $item_data[] = array(
                    'key'   => 'Shareable link',
                    'value' => wc_clean( $value ),
                );
            }
        }
        return $item_data;
    }

    /**
     * Show share link in the cart / checkout for composite parent items
     * @since    1.2.2
     */
    public function get_composite_parent_item_data($data, $cart_item, $args) {
        foreach($cart_item as $key => $value) {
            if ($key == 'vwplume_shareLink') {
                $data[] = array(
                    'key'   => 'Shareable link',
                    'value' => wc_clean( $value ),
                );
            }
        }
        return $data;
    }

    /**
     * Ensure meta data is shown for composite parents if they are ours
     * @since    1.2.2
     */
    public function modify_checkout_blocks_api_composite_data($settings_arr, $cart_item) {
        if (isset($cart_item['vwplume_composition'])) {
            $settings_arr['is_meta_hidden_in_cart'] = false;
        }
        return $settings_arr;
    }

    /**
     * Add VWPlume related meta data to composited cart-items
     * 
     * @since    1.2.1
     */
    public function get_composite_item_data($cart_item, $composite) {
        $composite_data = $cart_item[ 'composite_data' ];

        if (is_array($composite_data)) {
            $component = $composite_data[array_key_first($composite_data)];
            $properties = $component[ 'properties' ];
            if (isset($properties) && is_array($properties) && !empty($properties)) {
                foreach( $properties as $key => $value ) {
                    $cart_item[$key] = $value;
                }
            }
        }

        return $cart_item;
    }

    /**
     * Add products to the cart
     * 
     * @since    1.1.0
     */
    private function add_products_to_cart($products_to_add, $cartitems, $items_added, $error) {
        foreach($products_to_add as $product_to_add) {
            $cartitem_key = WC()->cart->add_to_cart( $product_to_add['product_id'], $product_to_add['quantity'], $product_to_add['variation_id'], $product_to_add['variation'], $product_to_add['properties'] );

            if ( $cartitem_key !== false ) {
                $cartitems[$cartitem_key ] = $product_to_add['quantity'];
                $items_added[$product_to_add['product_id']] = $product_to_add['quantity'];
                do_action( 'woocommerce_ajax_added_to_cart', $product_to_add['product_id'] );
                continue;
            } else {
                $error = true;
                break;
            }
        }
    }

    /**
     * Modify Cart item remove link.
     *
     * If we are working with a composited item, disallow removing single items. This is
     * because they are part of a VividWorks made composition. Any modifications to that
     * composition may break the product business rules and are thus not allowed outside
     * the VividWorks 3D Configurator
     * 
     * @since    1.1.0
     */
    public function cart_item_remove_link( $link, $cart_item_key ) {
        // For items that are components of a composite product, and belong
        // to a Plume composition, hide the remove links
        if ( function_exists( 'WC_CP' ) && isset( WC()->cart->cart_contents[ $cart_item_key ][ 'composite_data' ] ) && !empty( WC()->cart->cart_contents[ $cart_item_key ][ 'composite_parent' ] ) ) {
            $parent_key = WC()->cart->cart_contents[ $cart_item_key ][ 'composite_parent' ];
            if ( isset( WC()->cart->cart_contents[ $parent_key ] ) ) {
                $parent_item = WC()->cart->cart_contents[ $parent_key ];
                if ( !empty( $parent_item['vwplume_composition'] ) ) {
                    return '';
                }
            }
        }

        return $link;
    }

    /**
     * Modify Cart quantity input.
     *
     * If we are working with a composited item, disallow changes in quantity from the cart, per item.
     * User can only modify the quantity of the composite / composition, which then multiplies the
     * child items quantities in proportion.
     * 
     * @since    1.1.0
     */
    public function composite_cart_item_quantity( $quantity, $cart_item_key ) {
        if (function_exists( 'WC_CP' )) {
            $cart_item = WC()->cart->cart_contents[ $cart_item_key ];

            if ( $parent = wc_cp_get_composited_cart_item_container( $cart_item ) ) {
                $quantity = $cart_item[ 'quantity' ];
            }
        }

        return $quantity;
    }

    /**
     * Get country for the user location.
     *
     * @since    0.3.0
     */
    private function get_user_geo_country() {
        $geo      = new WC_Geolocation(); // Get WC_Geolocation instance object
        $user_ip  = $geo->get_ip_address(); // Get user IP
        $user_geo = $geo->geolocate_ip( $user_ip ); // Get geolocated user data.
        $country  = $user_geo['country']; // Get the country code
        return $country;
        // return WC()->countries->countries[ $country ]; // return the country name
    }

    /**
     * function to recursively apply functions to array members
     *
     * @since    0.3.0
     */
    private function array_map_recursive(callable $func, array $arr) 
    {
        if (!is_callable($func)) {
            return $arr;
        }
        $result = [];

        foreach ( $arr as $key => $value ) {
            $updated_key = $func( $key );

            if ( ! is_array( $value ) ) {
                $result[$updated_key] = $func( $value );
            } else {
                $result[$updated_key] = $this->array_map_recursive($func,$arr[$key]);
            }
        }

        return $result;
    }

    /**
     * Ajax function end-point to get prodct prices. Expects to 
     * have a VividWorks BOM object in the $_POST, which is then parsed
     * for SKUs, which are used to find applicable WC products.
     * 
     * Invoked via AJAX.
     *
     * @since    0.1.0
     */
    public function vwplume_getprices() {
        ob_start();

        // Added checks for empty data
        if ( empty( $_POST ) || empty($_POST["bom"]) || empty($_POST["bom"]["totals"])) {
            return;
        }

        // The BOM JSON model. Sanitize the whole array using `sanitize_text_field`
        $plume_bom = $this->array_map_recursive('sanitize_text_field', $_POST["bom"]);

        if (empty($plume_bom)) {
            return;
        }

        $cartitems = array();
        $items_added = array();
        $error     = false;

        // Match SKUs
        $skus_amounts = array();
        $products = array();
        $matches = array();

        // Get the SKU codes and amounts 
        if (!empty($plume_bom["totals"]) && is_array($plume_bom["totals"])) {
            foreach( $plume_bom["totals"] as $total_row ) {
                $sku = $total_row["code"];
                $amount = $total_row["amount"];

                $skus_amounts[$sku] = absint($amount);
            }
        }

        $product_posts = $this->get_products_for_skus(array_keys($skus_amounts));

        foreach( $product_posts as $post ) {
            if (!empty($post) && isset( $post, $post->ID )) {
                $prod = wc_get_product($post);
                $sku = $prod->get_sku();
                $matches[] = array(
                    'product' => $prod,
                    'amount'  => $skus_amounts[$sku],
                    'sku'     => $sku,
                );
            }
        }

        $prices = array();
        $country_geoloc     = $this->get_user_geo_country() || "FI";
        $country_account    = WC()->customer->get_shipping_country() ? WC()->customer->get_shipping_country() : WC()->customer->get_billing_country();
        $state              = WC()->customer->get_shipping_state() ? WC()->customer->get_shipping_state() : WC()->customer->get_billing_state();
        $city               = WC()->customer->get_shipping_city() ? WC()->customer->get_shipping_city() : WC()->customer->get_billing_city();
        $postcode           = WC()->customer->get_shipping_city() ? WC()->customer->get_shipping_city() : WC()->customer->get_billing_city();

        $tax_obj = new WC_Tax();
        $country = !empty($country_account) ? $country_account : $country_geoloc;

        $display_price_decimals = absint(get_option('woocommerce_price_num_decimals'));

        foreach($matches as $match) {
            $product           = $match['product'];
            $product_id        = !empty($product->ID)
                ? absint($product->ID)
                : absint($product->get_id());
            
            // Exit early if we get no ID
            if (empty($product_id)) {
                break;
            }

            $variation_id      = 0;

            $cartitem_key      = null;

            if ( 'variation' === $product->get_type() ) {
                // If this was a variation, switch the IDs
                // and get variation data
                $variation_id = $product_id;
                $product_id   = $product->get_parent_id();
            }

            $currency = get_woocommerce_currency();
            $price = $product->get_price();
            $price_with_tax  = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $price ) );
            $price_notax  = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $price ) );
            $sale_percentage = 0;

            // Get the tax data from customer location and product tax class
            $tax_rates_data = $tax_obj->find_rates( array(
                'country'   => $country,
                'state'     => $state,
                'city'      => $city,
                'postcode'  => $postcode,
                'tax_class' => $product->get_tax_class()
            ) );
            
            // Try and get the tax rate, first from WC_Tax class, then calculating
            $tax_rate = 0;
            if( ! empty($tax_rates_data) ) {
                $tax_rate = reset($tax_rates_data)['rate'];
            }
            else {
                $tax_rate = round(($price_with_tax * 100 - $price_notax * 100) / $price_notax, 2);
            }
            
            if ($product->is_on_sale('')) {
                $reg = $product->get_regular_price();
                $sale = $product->get_sale_price();

                $sale_percentage = round(($reg * 100 - $sale * 100) / $reg, 2);
            }

            $prod = array(
                // VividWorks PriceModel
                'applicationId'         => "",                // Need this to be defined, but value is not important
                'currency'              => $currency,
                'country'               => $country,
                'sku'                   => $match['sku'],
                'value'                 => $price_notax,      // This is the unit price excluding taxes, but INCLUDING discount
                'vat'                   => $tax_rate,
                'discount'              => $sale_percentage,
                // Extra info
                'wc_product_id'         => $product_id,
                'wc_variation_id'       => $variation_id,
                'unit_price_with_tax'   => $price_with_tax,
            );

            // Only add decimal config if the setting is something else than 2
            if ($display_price_decimals != 2) {
                $prod['displayDecimals'] = $display_price_decimals;
            }

            $prices[] = $prod; 
        }

        wp_send_json(
            $prices,
            200
        );
        
    }
}
