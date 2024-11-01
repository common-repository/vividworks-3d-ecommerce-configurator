<?php

/**
 * The main VividWorks settings
 *
 * @link       https://www.vividworks.com
 * @since      1.0.0
 *
 * @package    Vwplume
 * @subpackage Vwplume/admin/partials
 */

$settings = array(
    //
    // General options
    //
    array(
        'name' => __( 'General Configuration', 'vwplume' ),
        'type' => 'title',
        'id'   => $prefix . 'general_config_settings'
    ),

    array(
        'id'        => $prefix . 'default_product_library',
        'name'      => __( 'Default Product Library', 'vwplume' ), 
        'type'      => 'textarea',
        'desc_tip'  => __( 'Default Product Library ID to use for VividWorks configurator. When editing product specific settings, if the Product Library ID is empty this default value is used.', 'vwplume')
    ),

    array(
        'id'        => $prefix . 'default_product',
        'name'      => __( 'Default Product ID', 'vwplume' ), 
        'type'      => 'textarea',
        'desc_tip'  => __( 'Default Product ID to use for VividWorks configurator. This is useful if most of your configurators are based on the same base product. When editing product specific settings, if the Product ID is empty this default value is used.', 'vwplume')
    ),

    array(
        'id'       => $prefix . 'default_productpage_position',
        'name'      => __( 'Default product page placement', 'vwplume' ), 
        'desc'     => __( "Choose the default positioning of the Planner on the product pages. In some themes the product summary hooks may not work. If this happens, and you can't see the planner, choose either 'After the Product' or 'Before the Product'. In this case we most likely cannot replace the product page content.", 'woocommerce' ),
        'default'  => '0',
        'type'     => 'select',
        'class'    => 'wc-enhanced-select',
        'desc_tip' => true,
        'options'  => array (
            '0' => __( 'Replace Product Summary', 'vwplume' ),
            '1' => __( 'Before Product Summary', 'vwplume' ),
            '2' => __( 'After Product Summary', 'vwplume' ),
            '3' => __( 'Before the Product', 'vwplume' ),
            '4' => __( 'After the Product', 'vwplume' ),
        ),
    ),

    array(
        'id'        => '',
        'name'      => __( 'General Configuration', 'vwplume' ),
        'type'      => 'sectionend',
        'desc'      => '',
        'id'        => $prefix . 'general_config_settings'
    ),

    //
    // Cart / pricing related options
    //
    array(
        'name' => __( 'Shopping cart options', 'vwplume' ),
        'type' => 'title',
        'id'   => $prefix . 'cart_settings'
    ),

    array(
        'id'        => $prefix . 'cart_enabled',
        'name'      => __( 'Enable add-to-cart', 'vwplume' ), 
        'type'      => 'checkbox',
        'default'       => 'yes',
        'desc'      => __( 'Enable or disable add-to-cart functionality.', 'vwplume'),
        'desc_tip'  => true,
    ),

    array(
        'id'        => $prefix . 'cart_custom_js',
        'name'      => __( 'Custom add-to-cart event', 'vwplume' ), 
        'type'      => 'textarea',
        'css'       => 'height: 128px;',
        'desc'      => __( 'Custom JavaScript code to be run upon add-to-cart. Useful for example to update a sidebar cart on the fly.', 'vwplume'),
        'desc_tip'  => true,
    ),

    array(
        'id'        => '',
        'name'      => __( 'Shopping cart options', 'vwplume' ),
        'type'      => 'sectionend',
        'desc'      => '',
        'id'        => $prefix . 'cart_settings'
    ),
);
?>
