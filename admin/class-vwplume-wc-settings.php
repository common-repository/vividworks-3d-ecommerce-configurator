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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vwplume_WC_Settings' ) )
{
    class Vwplume_WC_Settings extends WC_Settings_Page {

        /**
         * Constructor
         * @since  0.1.0
         */
        public function __construct() {
                
            $this->id    = 'vwplume';
            $this->label = __( 'VividWorks Configurator', 'vwplume' );

            // Define all hooks instead of inheriting from parent                    
            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
            add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
            
        }

        /**
         * Get sections.
         *
         * @return array
         */
        public function get_sections() {
            $sections = array(
                '' => __( 'Settings', 'vwplume' ),
                //'productMap' => __( 'Product Mapping', 'vwplume' ),
                //'log' => __( 'Log', 'vwplume' )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
        }


        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings() {

            global $current_section;
            $prefix = 'vwplume_';

            switch ($current_section) {
                //case 'log':
                //    $settings = array(
                //            array()
                //    );
                //    break;
                default:
                    include 'partials/vwplume-wc-settings-main.php';
            }   

            return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );                   
        }

        /**
         * Output the settings
         */
        public function output() {                  
            $settings = $this->get_settings();

            WC_Admin_Settings::output_fields( $settings );
        }

        /**
         * Save settings
         *
         * @since 0.1.0
         */
        public function save() {                    
            $settings = $this->get_settings();

            WC_Admin_Settings::save_fields( $settings );
        }

    }
}

return new Vwplume_WC_Settings();