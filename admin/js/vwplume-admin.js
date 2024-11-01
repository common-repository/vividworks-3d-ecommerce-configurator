(function( $ ) {
    'use strict';

    // Enabled checkbox / flag for product configuration options
    $(document).on('change', '#vwplume_embed_enabled', function(e) {
        var plume_enabled_checkbox = $(this),
            plume_enabled_parent_panel = plume_enabled_checkbox.parents('.woocommerce_options_panel').eq(0);

        plume_enabled_parent_panel.toggleClass('vwplume-enabled');
    });

    $(document).on('click', '.vwplume_duplicate_component_btn', function(e) {
        e.preventDefault();
        var component_id = $(this).data('vwComponent');
        var data = $(this).data('vwComponentdata');
        var product_id = $(this).data('vwProduct');
        var action = $(this).data('vwAction');
        var url = $(this).data('vwUrl');

        var $components_panel = $( '#bto_product_data' );
        var block_params = {
            message:    null,
            overlayCSS: {
                background: '#fff',
                opacity:    0.6
            }
        };

        $components_panel.block( block_params );

        var $components_panel     = $( '#bto_product_data' ),
            $components_container = $( '.config_group', $components_panel ),
            $component_metaboxes  = $( '.bto_groups', $components_container ),
            $components           = $( '.bto_group', $component_metaboxes ),
            count                 = $components.length;

        // var name_inputs = $('input.group_title');
        // var names = name_inputs.map((idx,input) => { return input.value });
        
        $.ajax({
            type: 'post',
            url: url,
            data: {
                'id': ++count,
                'data': data,
                'product_id': product_id,
                'action': action,
            },
            'security': wc_composite_admin_params.add_component_nonce
        })
        .done(function(response){
            $component_metaboxes.append( response );

            var $added    = $( '.bto_group', $component_metaboxes ).last(),
                added_id  = 'component_' + count;
            $added.data( 'component_metabox_id', added_id );
            $components_panel.triggerHandler( 'wc-cp-components-changed' );

            $components_panel.unblock();
        })
        .fail(function(response){
            $components_panel.unblock();
        });
    });
})( jQuery );
