<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.vividworks.com
 * @since      0.1.0
 *
 * @package    Vwplume
 * @subpackage Vwplume/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vwplume
 * @subpackage Vwplume/admin
 * @author     VividWorks Oy <info@vividworks.com>
 */
class Vwplume_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vwplume-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {

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

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vwplume-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Load dependencies for additional WooCommerce settings
     *
     * @since    0.1.0
     * @access   private
     */
    public function vwplume_add_settings( $settings ) {
        $settings[] = include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vwplume-wc-settings.php';
        return $settings;
    }

    /**
     * Add configuration tab to the WC Product editor
     *
     * @since    0.1.0
     */
    public function vwplume_add_product_configuration_tab($tabs) {
        $tabs['vwplume_product_configuration'] = array(
            'label' => __('VividWorks Product Configuration', 'vwplume'),
            'target' => 'vwplume_product_configuration',
            'priority' => 90
        );
        return $tabs;
    }

    /**
     * Add configuration fields to the WC Product editor
     *
     * @since    0.1.0
     */
    public function vwplume_add_product_configuration_fields() {
        global $post;

        $vwplume_library_id = '';
        $vwplume_product_id = '';
        
        $vwplume_library_id = $this->get_product_meta_field($post, 'vwplume_library_id', '');
        if (empty($vwplume_library_id)) {
            $vwplume_library_id = get_option('vwplume_default_product_library');
        }
        
        
        $vwplume_product_id = $this->get_product_meta_field($post, 'vwplume_product_id', '');
        if (empty($vwplume_product_id)) {
            $vwplume_product_id = get_option('vwplume_default_product');
        }

        $vwplume_placement_type_options = array (
            '1' => __( 'Product Page', 'vwplume' ),
            '0' => __( 'None', 'vwplume' ),
        );
        $vwplume_placement_type = (int) $this->get_product_meta_field($post, 'vwplume_placement_type', 1);
        //metadata_exists('post', $post->ID, 'vwplume_placement_type') ? (int) get_post_meta($post->ID, 'vwplume_placement_type', true) : 1;

        $vwplume_embed_enabled = $this->get_product_meta_field($post, 'vwplume_embed_enabled', 'no');

        $vwplume_cart_enabled = $this->get_product_meta_field($post, 'vwplume_cart_enabled', 'yes');

        $vwplume_productpage_position_options = array (
            '0' => __( 'Replace Product Summary', 'vwplume' ),
            '1' => __( 'Before Product Summary', 'vwplume' ),
            '2' => __( 'After Product Summary', 'vwplume' ),
            '3' => __( 'Before the Product', 'vwplume' ),
            '4' => __( 'After the Product', 'vwplume' ),
        );

        $vwplume_productpage_position_default_setting = get_option('vwplume_default_productpage_position');
        $vwplume_productpage_position = (int) $this->get_product_meta_field($post, 'vwplume_productpage_position', -1);
        if ($vwplume_productpage_position < 0 && !empty($vwplume_productpage_position_default_setting)) {
            $vwplume_productpage_position = (int)$vwplume_productpage_position_default_setting;
        }
        else {
            $vwplume_productpage_position = 0;
        }
        //metadata_exists('post', $post->ID, 'vwplume_productpage_position') ? (int) get_post_meta($post->ID, 'vwplume_productpage_position', true) : 1;

        // If Composite Products plugin exists
        $wccp_inuse = function_exists( 'WC_CP' );
        // $vwplume_composite_product_default_setting = get_option('vwplume_default_productpage_position');
        $vwplume_composite_product = (int) $this->get_product_meta_field($post, 'vwplume_composite_product', -1);
        if ($vwplume_composite_product < 0) {
            $vwplume_composite_product = 0;
        }

        $vwplume_composite_product_options = array();
        if ($wccp_inuse) {
            $vwplume_composite_product_options = $this->get_composite_product_options();
        }
        $vwplume_composite_product_options = array(0 => __( 'Do not use Composite Product', 'vwplume' )) + $vwplume_composite_product_options;

        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vwplume-product-configuration-settings.php';
    }

    /**
     * Get composite products in an array, to be used as options for product configurator settings
     *
     * @since    1.1.0
     */
    private function get_composite_product_options() {
        $options = array();
        $products = wc_get_products( array(
            'limit'  => -1,
            'type' => 'composite'
        ) );
        foreach($products as $product) {
            $options[$product->get_id()] = $product->get_title();
        }

        return $options;
    }

    /**
     * Save product configuration data
     *
     * @since    0.1.0
     */
    public function woocommerce_process_product_meta_fields_save($post_id) {
        if ( array_key_exists( 'vwplume_embed_enabled', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_embed_enabled', 'yes' );
        } else {
            delete_post_meta( $post_id, 'vwplume_embed_enabled' );
        }

        if ( array_key_exists( 'vwplume_cart_enabled', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_cart_enabled', 'yes' );
        } else {
            update_post_meta( $post_id, 'vwplume_cart_enabled', 'no' );
        }

        if ( array_key_exists( 'vwplume_library_id', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_library_id', sanitize_text_field( $_POST['vwplume_library_id'] ) );
        } else {
            delete_post_meta( $post_id, 'vwplume_library_id' );
        }

        if ( array_key_exists( 'vwplume_product_id', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_product_id', sanitize_text_field( $_POST['vwplume_product_id'] ) );
        } else {
            delete_post_meta( $post_id, 'vwplume_product_id' );
        }

        if ( array_key_exists( 'vwplume_placement_type', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_placement_type', sanitize_text_field( $_POST['vwplume_placement_type'] ) );
        } else {
            delete_post_meta( $post_id, 'vwplume_placement_type' );
        }

        if ( array_key_exists( 'vwplume_productpage_position', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_productpage_position', sanitize_text_field( $_POST['vwplume_productpage_position'] ) );
        } else {
            delete_post_meta( $post_id, 'vwplume_productpage_position' );
        }

        if ( array_key_exists( 'vwplume_composite_product', $_POST ) ) {
            update_post_meta( $post_id, 'vwplume_composite_product', sanitize_text_field( $_POST['vwplume_composite_product'] ) );
        } else {
            delete_post_meta( $post_id, 'vwplume_composite_product' );
        }
    }

    /**
     * Get Product meta field data
     *
     * @since    0.1.0
     */
    private function get_product_meta_field($post, $field, $default) {
        return metadata_exists('post', $post->ID, $field) ? get_post_meta($post->ID, $field, true) : $default;
    }

    /**
     * Hide our custom meta fields from the order line items
     * 
     * @since    1.1.0
     */
    public function vwplume_hide_order_item_meta($hidden) {
        $vwplume_meta_fields = array(
            '_vwplume_composition',
            '_vwplume_assembly',
            'vwplume_readOnlyLink',
            'vwplume_shareLink',
        );
        return array_merge( $hidden, $vwplume_meta_fields );
    }

    /**
     * Add admin "meta boxes"
     * 
     * @since    1.1.0
     */
    public function vwplume_order_add_meta_boxes($post_type, $post)
    {
        if (function_exists( 'WC_CP' )) {
            $order = wc_get_order($post);
            if (is_object($order)) {
                $items = $order->get_items();
            
                if ( !empty( $items ) ) {
                    foreach( $items as $item ) {
                        $vwplume_composition = $item->get_meta( '_vwplume_composition' );
                        $vwplume_assembly = $item->get_meta( '_vwplume_assembly' );
                        if (!empty($vwplume_composition) && !empty($vwplume_assembly)) {
                            add_meta_box(
                                'vwplume_order_meta_assembly',
                                __( 'Configuration assembly', 'vwplume' ),
                                array( $this, 'vwplume_order_assembly_data' ),
                                'woocommerce_page_wc-orders',
                                'side',
                                'low',
                            );
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Assembly data neta box for orders.
     * 
     * Only shown if the order actually has assembly data.
     * 
     * @since    1.1.0
     */
    public function vwplume_order_assembly_data($post)
    {
        
        $html = '';
        
        $html .= '<p>';
        $html .= '<strong>'.__( 'This order has configuration assembly data', 'vwplume' ).'</strong>';
        $html .= '</p>';

        $html .= '<p>';
        $html .=  __( 'For integration purposes, the data is in the order item meta field called `_vwplume_assembly` for each Composite Product. You can also download the data, either in JSON or textual format, under each order item.', 'vwplume' );
        $html .= '</p>';

        echo $html;
    }

    /**
     * Modify order items on the admin order page
     * 
     * @since    1.2.0
     */
    public function vwplume_after_order_itemmeta($item_id, $item, $product) {
        // Read-only / share links
        $this->vwplume_render_order_links($item_id, $item, $product);

        // Assembly data
        $this->vwplume_order_item_view_assemblydata($item_id, $item, $product);
    }

    /**
     * Show read-only / share links after order item meta data
     * 
     * @since    1.2.0
     */
    public function vwplume_render_order_links($item_id, $item, $product) {
        if (function_exists( 'WC_CP' )) {
            if (wc_cp_is_composite_container_order_item($item)) {
                $child_items = wc_cp_get_composited_order_items( $item, $item->get_order() );
                $first_child = array_pop($child_items);
                $roLink = $item->get_meta("vwplume_readOnlyLink");
                $shareLink = $item->get_meta("vwplume_shareLink");

                $this->render_readonlylink($item);
                // $this->render_sharelink($item);
            }
            else if (!wc_cp_maybe_is_composited_order_item($item)) {
                // $this->render_readonlylink($item);
                // $this->render_sharelink($item);
            }
        }
        else {
            // $this->render_readonlylink($item);
            // $this->render_sharelink($item);
        }
    }

    /**
     * Render the read-only link in the item header
     * 
     * @since    1.2.0
     */
    private function render_readonlylink($item) {
        $roLink = $item->get_meta("vwplume_readOnlyLink");
        if (!empty($roLink)) {
            ?>
            <div class="wc-order-item-plumelink">
                <strong><?= __('Open read-only configuration')?>:</strong> 
                <a target="_blank" href="<?=$roLink?>"><?=$roLink?></a>
            </div>
            <?php
        }
    }

    /**
     * Render the share link in the item header
     * 
     * @since    1.2.0
     */
    private function render_sharelink($item) {
        $sharelink = $item->get_meta("vwplume_shareLink");
        if (!empty($sharelink)) {
            ?>
            <div class="wc-order-item-plumelink">
                <strong><?= __('Configuration share link')?>:</strong> 
                <a target="_blank" href="<?=$sharelink?>"><?=$sharelink?></a>
            </div>
            <?php
        }
    }

    /**
     * View assembly data download buttons on order lines
     * 
     * @since    1.1.0
     */
    public function vwplume_order_item_view_assemblydata($item_id, $item, $product) {
        global $post;

        $download_url = "admin-post.php?action=vwplume_download_assembly&post=".$item->get_order_id()."&item=".$item_id."&format=";
        $order = $item->get_order();

        $html = '';
        $vwplume_composition = $item->get_meta( '_vwplume_composition' );
        $vwplume_assembly = $item->get_meta( '_vwplume_assembly' );
        if (!empty($vwplume_composition) && !empty($vwplume_assembly)) {
            $html .= '<div class="vwplume_assembly_download_links" style="margin-top:1rem;"><span>'.__( 'Download assembly data:', 'vwplume' ).'</span>';
            $html .= '<a download="'.$this->get_assembly_filename($order,$item,'json').'" href="'.admin_url( $download_url.'json', 'https' ).'" target="_blank" class="button vwplume_button">';
            $html .= '<span class="dashicons dashicons-download"></span>' . __( 'JSON', 'vwplume' );
            $html .= '</a>';
            $html .= '<a download="'.$this->get_assembly_filename($order,$item,'txt').'" href="'.admin_url( $download_url.'txt', 'https' ).'" target="_blank" class="button vwplume_button">';
            $html .= '<span class="dashicons dashicons-download"></span>' . __( 'TXT', 'vwplume' );
            $html .= '</a>';
            $html .= '</div>';
            echo $html;
        }
    }

    /**
     * Get a file name for the assembly files for download
     * 
     * @since    1.1.0
     */
    private function get_assembly_filename($order, $item, $format) {
        $filename = 'order-' . $order->get_order_number();
        $filename .= '_item-' . $item->get_id();
        $filename .= '_assembly';
        $filename .= "." . $format;
        return $filename;
    }

    /**
     * "admin_post_" action hook to download assembly data
     * from an oder item.
     * 
     * @since    1.1.0
     */
    public function vwplume_download_assembly() {
        $post_id = $_GET['post'];
        $item_id = $_GET['item'];
        $format = $_GET['format'];

        $order = wc_get_order($post_id);

        if (is_object($order) && is_a($order, 'WC_Order')) {
            $item = $order->get_item($item_id);

            if (is_object($item) && is_a($item, 'WC_Order_Item')) {
                $vwplume_assembly = $item->get_meta( '_vwplume_assembly' );

                if (!empty($vwplume_assembly)) {
                    $filename = $this->get_assembly_filename($order,$item,$format);

                    header('Content-Disposition: attachment; filename="'.$filename.'";');
                    header("Pragma: no-cache");
                    header("Expires: 0");

                    if ($format == 'json') {
                        header('Content-Type: application/json; charset=utf-8',true,200);
                        echo json_encode($vwplume_assembly, JSON_PRETTY_PRINT);
                    }
                    else {
                        header('Content-Type: text/plain; charset=utf-8',true,200);
                        ob_start();
                        $this->print_assembly_txt($vwplume_assembly);
                        $output = ob_get_contents();
                        ob_end_clean();
                        echo $output;
                    }
                }
            }
        }
        exit();
    }

    /**
     * Converts the assembly data (JSON) to a textual
     * format for human readability
     * 
     * @since    1.1.0
     */
    private function print_assembly_txt($assembly) {
        echo ''."\r\n";
        
        $filter_linears = function($item) {
            return $item['isLinear'];
        };
        $filter_others = function($item) {
            return !$item['isLinear'];
        };
        $linears = array_values(array_filter($assembly, $filter_linears));
        $others = array_values(array_filter($assembly, $filter_others));
        
        foreach( $linears as $item ) {
            $this->print_assembly_item($item, 0, true);
        }
        
        if (!empty($linears) && !empty($others)) {
            echo "\r\n";
            echo "\r\n";
        }

        foreach( $others as $item ) {
            $this->print_assembly_item($item);
        }
    }
    private function print_assembly_item($item, $tablevel = 0, $printIndex = false) {
        if ($tablevel > 0) {
            for($i = 0; $i < $tablevel; $i++) {
                echo "    ";
            }
        }

        // In deeper levels, do not pad
        if ($tablevel == 0) {
            echo $printIndex
                ? str_pad($item['index'], 4)
                : str_pad('-', 4);
        }
        else {
            echo '- ';
        }
        
        // Print SKU if such is defined
        echo !empty($item['sku'])
            ? $item['sku'] . " "
            : "";
        
        // name + feature
        echo '"' . $item["name"] . '" in '.$item["feature"];

        echo "\r\n";

        if (isset($item['accessories']) && is_array($item['accessories']) ) {
            foreach( $item['accessories'] as $accessory ) {
                $this->print_assembly_item($accessory, $tablevel+1);
            }
        }
        
        if (isset($item['materials']) && is_array($item['materials'])) {
            foreach( $item['materials'] as $material ) {
                $this->print_assembly_item($material, $tablevel+1);
            }
        }
    }

    /**
     * Duplication "setting" for Components in a composite product
     * 
     * @since    1.1.0
     */
    public function vwplume_wccp_component_config_extra($id, $data, $product_id) {
        ?><div class="options_group options_group_component"><?php

        $url = admin_url("admin-post.php","https");

        ?>
        <div class="component_vwplume_extras">
            <div class="form-field">
                <label>
                    <?php echo __( 'Duplicate this component', 'vwplume' ); ?>
                </label>
                <a href="#" target="_blank" class="button vwplume_button vwplume_button_inline vwplume_duplicate_component_btn"
                    data-vw-component="<?php echo $id; ?>"
                    data-vw-componentdata="<?php echo htmlspecialchars(json_encode($data)); ?>"
                    data-vw-product="<?php echo $product_id; ?>"
                    data-vw-url="<?php echo $url; ?>"
                    data-vw-action="vwplume_duplicate_component">
                    <span class="dashicons dashicons-admin-page"></span>
                    <?php echo __( 'Duplicate', 'vwplume' ); ?>
                </a>
            </div>
        </div>
        <?php
        ?></div><?php
    }

    /**
     * "admin_post_" hook for the component duplication
     * 
     * @since    1.1.0
     */
    public function vwplume_duplicate_component() {
        $id      = isset( $_POST[ 'id' ] ) ? intval( $_POST[ 'id' ] ) : 0;
        $post_id = isset( $_POST[ 'product_id' ] ) ? intval( $_POST[ 'product_id' ] ) : 0;

        $data = (isset( $_POST[ 'data' ] ) && is_array($_POST[ 'data' ])) 
            ? wc_clean($_POST[ 'data' ])
            : array();

        $component_data = array( 'composite_id' => $post_id );
        $component_data['assigned_ids'] = (isset( $data['assigned_ids'] ) && is_array($data['assigned_ids'] ))
            ? $data['assigned_ids']
            : array();
        $component_data['default_id'] = (isset( $data['default_id'] ))
            ? $data['default_id']
            : "";
        $component_data['description'] = (isset( $data['description'] ))
            ? $data['description']
            : "";
        $component_data['display_prices'] = (isset( $data['display_prices'] ))
            ? $data['display_prices']
            : "";
        $component_data['optional'] = (isset( $data['optional'] ))
            ? $data['optional']
            : "";
        $component_data['priced_individually'] = (isset( $data['priced_individually'] ))
            ? $data['priced_individually']
            : "";
        $component_data['quantity_max'] = (isset( $data['quantity_max'] ))
            ? $data['quantity_max']
            : "";
        $component_data['quantity_min'] = (isset( $data['quantity_min'] ))
            ? $data['quantity_min']
            : "";
        $component_data['shipped_individually'] = (isset( $data['shipped_individually'] ))
            ? $data['shipped_individually']
            : "";
        $component_data['title'] = (isset( $data['title'] ))
            ? $data['title'].' Copy'
            : "Duplicated component";

        // $component_data['assigned_ids'] = array();
        // if (isset( $_POST[ 'data' ] ) && is_array($_POST[ 'data' ])) {
        //     if (isset( $_POST['data']['assigned_ids'] ) && is_array($_POST['data']['assigned_ids'] )) {
        //         foreach($_POST['data']['assigned_ids'] as $aid) {
        //             if (is_numeric($aid)) {
        //                 $component_data['assigned_ids'][] = intval($aid);
        //             }
        //         }
        //     }
            
        //     if ( isset( $_POST['data']['default_id'] ) && is_numeric($_POST['data']['default_id'] ) ) {
        //         $component_data['default_id'] = intval($_POST['data']['default_id']);
        //     }
        //     if ( isset( $_POST['data']['default_id'] ) && is_numeric($_POST['data']['default_id'] ) ) {
        //         $component_data['default_id'] = intval($_POST['data']['default_id']);
        //     }
        // }

        /**
         * Action 'woocommerce_composite_component_admin_html'.
         *
         * @param  int     $id
         * @param  array   $component_data
         * @param  int     $post_id
         * @param  string  $state
         *
         * @hooked {@see component_admin_html} - 10
         */
        do_action( 'woocommerce_composite_component_admin_html', $id, $component_data, $post_id, 'open' );

        die();
    }
}