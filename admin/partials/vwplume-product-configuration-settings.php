<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$vwplume_enabled = (string)$vwplume_embed_enabled === 'yes'
?>

<div id="vwplume_product_configuration" class="panel woocommerce_options_panel <?php echo $vwplume_enabled ? 'vwplume-enabled' : ''; ?>">

    <section class="vwplume_embed_enabled_selection">
        <?php
            woocommerce_wp_checkbox( array(
                'id'          => 'vwplume_embed_enabled',
                'label'       => __( 'Enable VividWorks Planner for this product?', 'vwplume' ),
                'value'       => $vwplume_embed_enabled,
                'description' => __( 'Enabling this option will show the VividWorks Planner on your product page', 'vwplume' ),
                'desc_tip'    => true,
            ) );
        ?>
    </section>

    <section>
        <?php
            woocommerce_wp_checkbox( array(
                'id'          => 'vwplume_cart_enabled',
                'label'       => __( 'Enable add-to-cart for this product?', 'vwplume' ),
                'value'       => $vwplume_cart_enabled,
                'default'     => 'yes',
                'description' => __( 'Enable or disable add-to-cart functionality for this product.', 'vwplume' ),
                'desc_tip'    => true,
            ) );
        ?>
    </section>

    <section>

        <p class="form-field">
            <label for="vwplume_library_id"><?php echo __( 'Library ID', 'vwplume' ) ?></label>
            <input type="text" name="vwplume_library_id" value="<?php echo esc_attr($vwplume_library_id); ?>" />
        </p>

        <p class="form-field">
            <label for="vwplume_product_id"><?php echo __( 'Product ID', 'vwplume' ) ?></label>
            <input type="text" name="vwplume_product_id" value="<?php echo esc_attr($vwplume_product_id); ?>" />
        </p>

    </section>

    <section>
        <?php /*
            woocommerce_wp_select( array(
                'id'      => 'vwplume_placement_type',
                'label'   => __( 'Planner placement', 'vwplume' ),
                'options' => $vwplume_placement_type_options,
                'value'   => $vwplume_placement_type,
            ) );
        */ ?>
        <input type="hidden" name="vwplume_placement_type" value="1" />
            
        <section>
            <?php
                woocommerce_wp_select( array(
                    'id'            => 'vwplume_productpage_position',
                    'label'         => __( 'Planner placement on product page', 'vwplume' ),
                    'options'       => $vwplume_productpage_position_options,
                    'value'         => $vwplume_productpage_position,
                    'description'   => __( "In some themes the product summary hooks may not work. If this happens, and you can't see the planner, choose either 'After the Product' or 'Before the Product'. In this case we most likely cannot replace the product page content.", 'vwplume' ),
                    'desc_tip'      => true,
                ) );
            ?>
        </section>

        <?php if ($wccp_inuse && count($vwplume_composite_product_options) > 1) { ?>
        <section>
            <?php
                woocommerce_wp_select( array(
                    'id'            => 'vwplume_composite_product',
                    'label'         => __( 'Use composite product in cart', 'vwplume' ),
                    'options'       => $vwplume_composite_product_options,
                    'value'         => $vwplume_composite_product,
                    'description'   => __( "Group the configured components in a Composite Product (official WooCommerce extension) when adding to cart. This makes it possible to add multiple configurations in an clear and understandable way. Just make sure to add Composite Products extension in WooCoommerce and create 'composite' type products to be used here.", 'vwplume' ),
                    'desc_tip'      => true,
                ) );
            ?>
        </section>
        <?php } ?>
    </section>

    <?php wp_nonce_field( 'vwplume_product_options', 'vwplume_nonce' ); ?>

</div>