(function( $ ) {
	'use strict';

	if (typeof window['VWPlume'] == 'undefined') {
		window['VWPlume'] = {};
	}

	var VWPlume = window['VWPlume'];

	var VWPlume_Products = function() {
	}
	VWPlume_Products.prototype.getPrices = function(bom) {
		return new Promise((resolve, reject) => {
			if (bom === undefined 
				|| bom.compositions === undefined
				|| !Array.isArray(bom.compositions)
				|| !bom.compositions.length) {
				reject("Empty configuration");
				return;
			}
	
			const composition = bom.compositions[0];
	
			if (!Array.isArray(composition.modules) && !Array.isArray(composition.totals)) {
				reject("Empty composition");
				return;
			}
	
			$.ajax({
				type: 'post',
				url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'vwplume_getprices' ),
				data: {
					'bom': {
						'totals': composition.totals
					},
				},
			})
			.done(function(response){
				if ( ! response ) {
					resolve([]);
					return;
				}
				resolve(response);
			})
			.fail(function(response){
				reject("Error in price query");
			});
		});
	};
	VWPlume.Products = new VWPlume_Products();

	var VWPlume_Cart = function() {
	}
	VWPlume_Cart.prototype.handlebom = function(bom) {
		if (bom === undefined 
				|| bom.compositions === undefined
				|| !Array.isArray(bom.compositions)
				|| !bom.compositions.length) {
			// console.log("Empty configuration");
			return;
		}

		const composition = bom.compositions[0];
		const readOnlyLink = bom.readOnlyLink || "";
		const shareLink = bom.shareLink || "";

		if (!Array.isArray(composition.modules) && !Array.isArray(composition.totals)) {
			// console.log("Empty composition");
			return;
		}

		const product_id = (typeof window['VWPlume'].params !== 'undefined' && window['VWPlume'].params.wc_product_id !== 'undefined')
			? window['VWPlume'].params.wc_product_id
			: '';
		let cart_data = {
			'bom': {
				'totals': composition.totals
			},
			'assembly': constructAssemblyMap(composition),
			readOnlyLink,
			shareLink
		}

		if (typeof product_id == 'string' && product_id.length > 0) {
			cart_data['product_id'] = product_id;
		}

		var start = performance.now();
		$.ajax({
			type: 'post',
			url: woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'vwplume_addtocart' ),
			data: cart_data,
		})
		.done(function(response){
			var stop = performance.now();
			//console.log('::: Cart add took '+(stop-start)+'ms');
			if ( ! response ) {
				return;
			}

			if (typeof window['VWPlume'].params !== 'undefined' ) {
				if (window['VWPlume'].params.cart_custom_js && typeof window['VWPlume'].custom_cart_event == 'function') {
					window['VWPlume'].custom_cart_event();
					return;
				}
			}

			if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
				window.location = wc_add_to_cart_params.cart_url;
				return;
			}
			
			if (response.message && window.confirm(response.message)) {
				window.location = wc_add_to_cart_params.cart_url;
				return;
			}
			
			// Trigger event so themes can refresh other areas.
			$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
		})
		.fail(function(response){
			if(response.data) {
				alert(response.data);
			}
		});
	}
	VWPlume.Cart = new VWPlume_Cart();

	function constructAssemblyMap(composition) {
		const modules = composition.modules;

		let get_code = (module) => {
		    let codes = module['codes'];
		    if (Array.isArray(codes) && codes.length > 0) {
		        let nonEmpty = codes.find(x => x.code.length > 0);
		        if (nonEmpty != undefined)
		            return nonEmpty.code;
		    }
		    return "";
		};

		let create_module_object = (it) => {
		    let module = {}; 
		    module.name = it.name;
		    module.feature = it.feature;
		    module.index = it.hasOwnProperty('index') ? it.index : -1;
		    module.sku = get_code(it);
		    module.accessories = it.hasOwnProperty('accessories') && Array.isArray(it.accessories) && it.accessories.length > 0
		        ? it.accessories.map(acc => create_module_object(acc))
		        : [];
		    module.materials = it.hasOwnProperty('materials') && Array.isArray(it.materials) && it.materials.length > 0
		        ? it.materials.map(mat => create_module_object(mat))
		        : [];
		    module.isLinear = it.hasOwnProperty('properties') && typeof it.properties == 'object'
		        ? it.properties.hasOwnProperty('IsConnectedLeft') && module.index >= 0
		        : false;
		    return module;
		}

		let modules_map = modules.map((it, idx) => {
		    return create_module_object(it);
		});

		modules_map = modules_map
		    .filter(it => it.isLinear)
		    .sort((a,b) => a.index - b.index)
		    .concat(modules_map.filter(it => !it.isLinear));
		
		return modules_map;
	}

})( jQuery );
