<?php
/**
 * Plugin Name: List Multiple Products
 * Plugin URI:  https://michaelcampos.com.br/plugins/list-multiple-products
 * Description: Um plugin que permite listar e selecionar múltiplos produtos em uma página com shortcode.
 * Version:     1.0.1
 * Author:      Michael Campos
 * Author URI:  https://michaelcampos.com.br
 * Text Domain: list-multiple-products
 * Domain Path: /languages
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin path
define( 'LMP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include required files
require_once LMP_PLUGIN_PATH . 'includes/class-lmp-settings.php';
require_once LMP_PLUGIN_PATH . 'includes/class-lmp-shortcode.php';
require_once LMP_PLUGIN_PATH . 'admin/class-lmp-admin.php';

// Initialize the plugin
LMP_Admin::init();
LMP_Shortcode::init();
LMP_Settings::init();

// Enqueue scripts and styles
function lmp_enqueue_scripts() {
    wp_enqueue_style( 'lmp-public-styles', plugins_url( 'public/css/public-styles.css', __FILE__ ) );
    wp_enqueue_script( 'lmp-public-scripts', plugins_url( 'public/js/public-scripts.js', __FILE__ ), array( 'jquery' ), null, true );

    wp_localize_script( 'lmp-public-scripts', 'lmp_params', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'locale'   => get_locale(),
        'currency' => get_woocommerce_currency(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'lmp_enqueue_scripts' );

// Handle Ajax add to cart
function lmp_add_to_cart() {
    $product_id = intval( $_POST['product_id'] );
    $quantity = intval( $_POST['quantity'] );

    if ( $product_id && $quantity ) {
        $added = WC()->cart->add_to_cart( $product_id, $quantity );

        if ( $added ) {
            // Obter a imagem do produto para a notificação
            $product = wc_get_product( $product_id );
            $product_image = $product ? $product->get_image() : '';
            wp_send_json_success( array( 'message' => __( 'Product added to cart.', 'list-multiple-products' ), 'image' => $product_image ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to add product to cart.', 'list-multiple-products' ) ) );
        }
    } else {
        wp_send_json_error( array( 'message' => __( 'Invalid product or quantity.', 'list-multiple-products' ) ) );
    }
}
add_action( 'wp_ajax_lmp_add_to_cart', 'lmp_add_to_cart' );
add_action( 'wp_ajax_nopriv_lmp_add_to_cart', 'lmp_add_to_cart' );
?>