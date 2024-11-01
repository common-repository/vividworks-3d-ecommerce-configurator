=== VividWorks 3D E-Commerce Configurator ===
Contributors: vividworks
Tags: 3d, configuration, visualization, product configuration, cpq, configure price quote, visual product configurator, 3d product configurator
Requires at least: 5.0
Tested up to: 6.6.1
Stable tag: 1.2.5
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Online selling like in-store selling with full 3D visual product configurator

== Description ==

Integrate your VividWorks 3D E-Commerce Configurator account to any [WooCommerce](https://woocommerce.com) shop.

Bring your product or products to life and let your customers experience all the details of your product in full 3D. Enable an in-store physical experience in online where you can almost touch and feel the product for real. Let your customers explore the possibilities of your product options, with safety of ensuring they can only configure the product correctly.

Configure the product rules and add all the parts, materials and accessories with our web-browser based management interface. Update and edit new additions and changes and publish them with ease. The rules system allows to build complex product rules, ensuring all purchase orders can be manufactured, delivered, and installed. Map SKU codes with your e-commerce platform data to ensure showing accurate prices and bill-of-material to the end user. Enable completing the purchase through integration of the WooCommerce shopping cart.

**Note:** To use this plugin you must have a valid subscription with VividWorks and an active _VividWorks 3D E-Commerce Configurator_ account. To learn more [please visit our product page](https://www.vividworks.com/solutions/3d-product-configurator).

== Installation ==

Installing our plugin is very easy, either automatically or by uploading the plugin zip file which you can download from our WordPress plugin page. There are no special technical requirements but we recommend a fairly up-to-date server and WordPress installation, for example PHP 7+ and WordPress version 5 and up.

The only main requirement – for using the plugin, not installing it – is that you have to be our customer. _VividWorks 3D E-Commerce Configurator_ is a SaaS product for which this plugin implements WooCommerce integration. Installing the plugin is free but the plugin without being our customer does not really do anything.

### Installing the plugin

#### Installation through the WordPress plugin directory

1. In your WordPress admin page, go to Plugins > Add New
2. Search for "VividWorks"
3. When you find our plugin (called "VividWorks 3D E-Commerce Configurator") click the "Install Now" button.
4. Activate the plugin in the WordPress Plugin Admin (Plugins > All plugins)

#### Manual installation

1. Navigate to our WordPress plugin page
2. Download the plugin `.zip` file
3. Head over to the Wordpress plugin admin and hit the "Add New" button in the upper section.
    - In the WordPress admin interface, go to Plugins, then "Add New"
4. Now choose "Upload Plugin", always in the upper section of the page, and choose your plugin zip file. Now hit the "Install Now" in the upload widget
5. Congratulations, the VividWorks plugin is now installed! All you have to do now is _activate_ it. As you can note in the last image, WordPress offers a quick way to do this just after the installation, just hit the "Activate Plugin" button. Otherwise you can activate it later in the Wordpress Plugin admin interface
6. Go to the Product settings, find the VividWorks tab, enable the configurator, and insert at least Library ID and Product ID, which can be found in the VividWorks Management site or are given to you by VividWorks

### Configuring the plugin

Configuring our WooCommerce plugin is very simple. As opposed to some other similar offerings we rely completely on our own SaaS service and your WooCommerce product configuration. All our plugin has to do is show our embedded planner interface and provide integration for add-to-cart function.

The configuration can be broken down into two broader steps. **General settings** and **product specific settings**.

#### General settings

In the WooCommerce settings interface (found under `WooCommerce > Settings`) you should now have a tab called *VividWorks Configurator*. In there you will find settings that affect all VividWorks embedded configurators on your site. Currently we have the following settings.

**Note:** In the current release of our plugin we do not have an API connection to our back-end. This means that settings like the Product Library and Product ID will be provided to you by VividWorks.

**Default Product Library:** Product Library in our lingo means a container where you would have similar products in. Defining the Product Library for the embedded configurator is obligatory. What this setting does is set the *default* Product Library to be used for all WooCommerce products, unless otherwise specified in product specific settings. This is handy especially if most or all of your configurators would use the same library of products.

**Default Product ID:** The ID of the product used from our back-end. This setting then sets the *default* Product ID to be used for all WooCommerce products, unless otherwise specified in product specific settings. This is handy especially if most or all of your configurators would use the same product from our back-end (think *variations* of one product).

**Default product page placement:** The default placement on product pages. Choose this to set in which setting the products will default to when enabling the configurator for them. For reference of the options, see the product specific settings later in the document.

**Custom add-to-cart event:** To customize what happens after user has added a product to the cart you can add JavaScript code in this field. Please note that this code, if defined, will overrule any default WooCommerce events such as “redirect to cart”.

#### Product specific settings

In the WooCommerce product section (`Products` in the main menu on the left), when editing individual products you can find `VividWorks Product Configuration` tab. In here you can enable our 3D based planner for the product and tie it to a VividwWorks back-end product.

The main setting here is the `Enable VividWorks Planner for this product?` checkbox. Once you select this, you will see other settings. For the embedded configurator to show up you also need to provide a `Product Library ID` and `Product ID`, values for which we will provide to you. If you set default values for these two in the *General Settings* they will be filled for you automatically.

To control the placement of the configurator on the product detail page use the `Planner placement on the product page`, which has three options:

- **Replace Product Summary** to replace the default product summary and only show our configurator. This is the default option
- **Before Product Summary** to show our configurator *above* the default product page summary
- **After Product Summary** to show our configurator *below* the default product page summary.
- **Before the Product** to show our configurator above the whole product block.
- **After the Product** to show our configurator below the whole product block.

Add-to-cart button can be disabled individually for each product by unticking the `Enable add-to-cart for this product?` checkbox.

If your shop is using the **Composite Products** extension, you can enable product grouping in cart by selecting a composite product with the `Use composite product in cart` dropdown to be used as the grouping container.

### Embedding the planner manually

The configurator can also be embedded on any page, not just product detail pages, by using a certain HTML code. On the product detail pages the positioning of the configurator can be customized by using  a WordPress template tag (called `action` in WordPress lingo).

### Embedding on any page

To include a planner on a normal WordPress page or post, use the following HTML markup:

`
<div class="vw3d" 
    data-vw-library="ba8bb7db-4b31-4afb-a4bd-e647a5a9216f"
    data-vw-product="5b8591c2-ed80-437b-990c-ce102120a6e6">
</div>
`

The above tag would embed the planner for product with ID `5b8591c2-ed80-437b-990c-ce102120a6e6` from a product library with ID `ba8bb7db-4b31-4afb-a4bd-e647a5a9216f`. As mentioned before in this document, these codes / IDs will be provided to you by VividWorks. Also notice that you can wrap the above HTML in any amount of other HTML you will, and style accordingly.

### Custom placement on Product Pages

To place the configurator on any location of the product page, if you have a custom template, you can use the following WordPress `action`:

`
<?php do_action( 'vividworks_product_configurator' ) ?>
`

The action currently assumes it is run on a WooCommerce product page and does not accept custom product or product library IDs.

### Grouping of Shopping Cart items

In some cases it makes sense to group various components of your product under just one item in the shopping cart. This could be the case for example with modular products where the components are all priced individually. To this end we support the official WooCommerce made extension called **Composite Products**.

If the Composite Products is installed and active in your shop you can enable the grouping feature in product specific configurator settings by selecting the desired composite "container" product in the `Use composite product in cart` dropdown. The dropdown menu is filled with names of all "composite" type products in your catalog.

The composite product used for grouping does not have to be visible in he catalog. You can either create an arbitrary hidden composite product to be used for grouping, or use the composite product as the visible product and configure the configurator on it too.

To set up the composite product you need to add "components" to it in the composite product settings. The component structure
should roughly follow you products business rules, especially those that have been implemented for your product in the VividWorks configurator. For example you can define which products can be added to each component, is the component optional or not, and what are the min/max quantitie of the items.

When adding the configured parts from the 3D configuration to the composite product in cart, we simply find the first non-filled component that allows adding the product (the product which matches the SKU of the part) in question. We do not consider any other rules defined in the composite product settings for now. This is because in our opinion setting up the composite products shouold be easy and straight forward, not replicating all the business rules there.

A simple example is a modular sofa. Our recommendation is to add a large enough amount of components to satisfy most customers needs, so mabe 20 or more components. The components should each allow all possible part products, be optional and not have quantity limitations. Naming the components in a generic way is also advisable so for example "Part 1, Part 2, ...". This way the components will be filled with parts from the 3D configuration in order of appearance in the BOM.

**Note:** To make it faster to add a lot of components, we have extended the Composite Products configuration so that you can duplicate a component. To create 20 similar components you can create and configure one and then use the "Duplicate" button found at the top of each components configuration section 11 times. You should consequently name the new components and save the product because otherwise the components configuration sections will not behave as the "natively" created components will.

If the product is simpler - like for example a bicycle - it is easier to make a little bit stricter structure. The seat is most likely just one piece and only accepts seats of various types.

Once the product is in the cart, we have disallowed modifying the component configuration.

#### Assembly list attachment

When a configuration is added to the cart using the Composite Product feature, we will also save an assembly listing of the configuration as a meta data to the order line containing the configuration (that is, the composite product container order line). This data is a PHP array and can be found in a meta data field called `_vwplume_assembly`.

You can either use the meta data field in your integrations or download the assembly data in JSON or textual format via buttons added on the order lines themselves.

### Add-to-cart and pricing

There will be an "Add to cart" button in the embedded configurator UI. This button relies on SKU logic configuration to be done for the products in our back-end, and those same SKUs to be found in the WooCommerce product catatalog.

Note that the SKUs do not have to be defined in the same product for which the embedded configurator was enabled. Internally the add-to-cart will find a WooCommerce product or a product variation that has a SKU we happen to need, and it will add that product (or variation) into the shopping cart. 

For example, a user configures a product with 3 distinct parts and now wants to add it to their shopping cart. WooCommerce will receive a list of SKUs and amounts from the VividWorks back-end, something along the lines of:

`
[
    {
        'code': 'part-1',
        'amount: 2
    },
    {
        'code': 'part-2',
        'amount: 1
    },
    {
        'code': 'part-3',
        'amount: 1,
        'properties': {
            'extra': 'foo'
        }
    }
]
`

Our plugin code will now go through all WooCommerce products and product variants, and find those that match the SKUs in the list. The result is that two instances of a product / variant with SKU `part-1` will be added to the cart, and one for `part-2` and `part-3`.

VividWorks does not handle pricing separately but instead relies on your existing pricing, including sales / discounts. This makes it easier for you to maintain the pricing as it only exists in one system.

== Screenshots ==

1. The embedded configurator front-end
2. Product Settings
3. Material sample
4. Product measurements

== Frequently Asked Questions ==

= Where can I configure my 3D? =
Be in contact with us. First of all you need to be our customer. As of today we VividWorks need to configure the actual 3D products for you. We will open the configuration management interface to all of our _3D E-Commerce Configurator_ customers sometime in the near future, but for now just drop us an email.

= What do I need to put into the ID fields in the plugin settings? =
We will provide you with the correct IDs and other help once we have your products configured.

== Changelog ==

= 1.2.5 =
- Fixed a bug in cart button enabler, which did not work for non product pages

= 1.2.4 =
- Fixed Finnish localizations

= 1.2.3 =
- Fixed "enable add-to-cart" setting that was not working

= 1.2.2 =
- Tested to work with the newest WooCommerce (9.2.3)
- Fixed a minor bug in showing read only links

= 1.2.1 =
- Tested up to WordPress 6.6.1
- Updated version values

= 1.2.0 =
- Added support for cart item meta fields based on VividWorks sales data
- minor fixes

= 1.1.4 =
- Tested up to WordPress 6.4.3
- Fixed an issue of quantity disappearing in the classic cart page template

= 1.1.3 =
- Updated version values

= 1.1.2 =
- Price rounding settings are now transferred to our configurator instance. This makes price display consistent.

= 1.1.1 =
- Remove forced WooCommerce dependency

= 1.1.0 =
- Fixed a minor bug in Tax / VAT fields in price fetch
- Added support for using Composite Products extensions to group configuration parts in cart

= 1.0.1 =
- Fixed add-to-cart and price query features
- Added possibility to disable add-to-cart
- minor bug fixes

= 1.0.0 =
The initial public release of the plugin.

= 0.4.1 =
- Minor bug fixes

= 0.4.0 =
- Added language / locale support

= 0.3.1 =
- Bug fix for add-to-cart on non-product page embeds

= 0.3.0 =
- Possibility to add custom JS code to run on add-to-cart
- Price API to get prices for SKUs

= 0.2.1 =
- Performance optimization for add-to-cart

= 0.2.0 =
- Added hooks to view the Planner after or before the product, using actions "woocommerce_before_single_product" and "woocommerce_after_single_product".
- Added a custom action / hook to render the VividWorks Planner on any point of WooCommerce product page template by running do_action( 'vividworks_product_configurator' )
- Added default setting for product page positioning in the general main settings of the plugin
- Added more spacing around the planner, above and below, when not replacing the product summary

= 0.1.0 =
The initial pre-release of the plugin.
