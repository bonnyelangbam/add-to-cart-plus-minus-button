<?php
/**
 * Plugin Name: WooCommerce Custom Add To Cart Plus & Minus Button
 * Description: To add custom plus and minus button for add to cart items.
 * Version: 1.0.0
 * Author: Bonny Elangbam
 * Author URI: https://dev.bonnyelangbam.com/
 * Text Domain: add-to-cart-plus-minus-button
 * Requires PHP: 7.0
 * Requires at least: 5.8
 *
 * @package WooCommerce Custom Add To Cart Plus & Minus Button
 */

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

if ( ! class_exists( 'BE_Plus_Minus' ) ) {

    /**
     * Main Class.
     */
    class BE_Plus_Minus {

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

BE_Plus_Minus::get_instance();

// Removing Default Up & Down Aroww
add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_register_style('add-to-cart-plus-minus-button', plugins_url('plus-minus.css', __FILE__));
    wp_enqueue_style('add-to-cart-plus-minus-button');
}
