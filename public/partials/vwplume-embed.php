<?php
/**
 * The public facing VividWorks embed. Requires the embed tag JS to be
 * included elsewhere and also the following variables to be defined:
 * 
 * - $hook
 * - $vwplume_library_id
 * - $vwplume_product_id
 * - $product_id
 *
 * @link       https://www.vividworks.com
 * @since      0.1.0
 *
 * @package    Vwplume
 * @subpackage Vwplume/public/partials
 */
?>

<div class="vw-planner vw-planner-productpage vw-planner-<?php echo esc_attr($hook) ?>">
    <div class="vw3d" 
        data-vw-library="<?php echo esc_attr($vwplume_library_id); ?>" 
        data-vw-product="<?php echo esc_attr($vwplume_product_id); ?>" 
        data-wc-product-id="<?php echo esc_attr($product_id); ?>">
    </div>
</div>