<?php
/*
Plugin Name: Curtain Options
Description: Custom curtain options to WooCommerce products.
Version: 3.0.2
Author: Mufaqar
*/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
include_once plugin_dir_path(__FILE__) . 'includes/custom-options.php';
include_once plugin_dir_path(__FILE__) . 'includes/cart-checkout.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

add_action('woocommerce_product_options_general_product_data', 'custom_curtain_options_add_custom_field');
function custom_curtain_options_add_custom_field() {
    woocommerce_wp_checkbox(
        array(
            'id'            => 'enable_custom_curtain_options',
            'label'         => __('Enable Custom Rollover Tarp Options', 'woocommerce'),
            'description'   => __('Enable this option to display custom Tarp options on the product page.', 'woocommerce'),
            'desc_tip'      => true,
        )
    );
}

// Save the custom checkbox field value
add_action('woocommerce_process_product_meta', 'custom_curtain_options_save_custom_field');
function custom_curtain_options_save_custom_field($post_id) {
    $enable_custom_curtain_options = isset($_POST['enable_custom_curtain_options']) ? 'yes' : 'no';
    update_post_meta($post_id, 'enable_custom_curtain_options', $enable_custom_curtain_options);
}



function update_price_before_adding_to_cart( $cart_item_data, $product_id ) {
    if( isset( $_POST['custom_price'] ) ) {
        $custom_price = floatval( $_POST['custom_price'] );
        $cart_item_data['custom_price'] = $custom_price;
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'update_price_before_adding_to_cart', 10, 2 );

// Set the custom price when the item is added to the cart
function set_custom_price_in_cart( $cart_object ) {
    foreach( $cart_object->get_cart() as $cart_item ) {
        if( isset( $cart_item['custom_price'] ) ) {
            $cart_item['data']->set_price( $cart_item['custom_price'] );
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'set_custom_price_in_cart' );


/*Shipping Applied*/

add_action('woocommerce_cart_calculate_fees', 'add_custom_shipping_fees_based_on_selected_method');
function add_custom_shipping_fees_based_on_selected_method() {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Retrieve the selected shipping method
    $chosen_methods = WC()->session->get('shipping_method');
    $chosen_shipping = isset($chosen_methods[0]) ? $chosen_methods[0] : '';

    // Check if the chosen shipping method is UPS Ground
    if ($chosen_shipping === 'flexible_shipping_ups:9:03') { // Match the value from the HTML

        // Add $10 flat shipping supplies fee
        $supplies_fee = 10;
        WC()->cart->add_fee(__('Shipping Supplies Fee', 'woocommerce'), $supplies_fee, false); // No tax on this fee

        // Calculate 4% of the cart subtotal
        $cart_subtotal = WC()->cart->get_subtotal();
        $extra_fee = $cart_subtotal * 0.04;

        // Add the 4% custom fee
        WC()->cart->add_fee(__('Extra Shipping Fee (4% of product price)', 'woocommerce'), $extra_fee, false);
    }
}
