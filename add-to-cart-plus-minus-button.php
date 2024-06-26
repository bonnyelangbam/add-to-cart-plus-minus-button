<?php
/*
 * Plugin Name:       Custom Add To Cart Plus & Minus Button
 * Description:       To add custom plus and minus button for add to cart items.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Bonny Elangbam
 * Author URI:        https://dev.bonnyelangbam.com/
 * WC requires at least: 5.0
 * WC tested up to: 8.6
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       add-to-cart-plus-minus-button
 */

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

if ( ! class_exists( 'atc_Plus_Minus_Button' ) ) {

    /**
     * Main Class.
     */
    class atc_Plus_Minus_Button {

        /**
         * The instance variable of the class.
         *
         * @var $instance.
         */
        protected static $instance = null;

        /**
         * Constructor of this class.
         */
        public function __construct() {
            add_action( 'woocommerce_after_quantity_input_field', array( $this, 'be_display_quantity_plus' ) );
            add_action( 'woocommerce_before_quantity_input_field', array( $this, 'be_display_quantity_minus' ) );
            add_action( 'wp_footer', array( $this, 'be_add_cart_quantity_plus_minus' ) );
        }

        /**
         * Display plus button after Add to Cart button.
         */
        public function be_display_quantity_plus() {
            echo '<button type="button" class="plus">+</button>';
        }

        /**
         * Display minus button before Add to Cart button.
         */
        public function be_display_quantity_minus() {
            echo '<button type="button" class="minus">-</button>';
        }

        /**
         * Enqueue script.
         */
        public function be_add_cart_quantity_plus_minus() {

            if ( ! is_product() && ! is_cart() ) {
                return;
            }

            wc_enqueue_js(
                "$(document).on( 'click', 'button.plus, button.minus', function() {

                    var qty = $( this ).parent( '.quantity' ).find( '.qty' );
                    var val = parseFloat(qty.val());
                    var max = parseFloat(qty.attr( 'max' ));
                    var min = parseFloat(qty.attr( 'min' ));
                    var step = parseFloat(qty.attr( 'step' ));

                    if ( $( this ).is( '.plus' ) ) {
                        if ( max && ( max <= val ) ) {
                        qty.val( max ).change();
                        } else {
                        qty.val( val + step ).change();
                        }
                    } else {
                        if ( min && ( min >= val ) ) {
                        qty.val( min ).change();
                        } else if ( val > 1 ) {
                        qty.val( val - step ).change();
                        }
                    }

                });"
            );
        }

        /**
         * Instance of this class.
         *
         * @return object.
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }
}

atc_Plus_Minus_Button::get_instance();

// Removing Default Up & Down Aroww
add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    $plugin_version = '1.0.0'; // Change this to your plugin's version number.
    wp_register_style('add-to-cart-plus-minus-button', plugins_url('plus-minus.css', __FILE__), array(), $plugin_version);
    wp_enqueue_style('add-to-cart-plus-minus-button');
}


add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
