<?php
/*
Plugin Name: Curtain Options
Description: Custom curtain options to WooCommerce products.
Version: 2.0.2
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

add_action('woocommerce_cart_calculate_fees', 'add_custom_shipping_fee', 10, 1);

function add_custom_shipping_fee($cart) {
    // Ensure we're not affecting the backend or admin areas
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Loop through the cart items to check for the specific product
    $extra_fee_percentage = 4; // 4% shipping fee
    $total_extra_fee = 0;

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];

        // Check if the product is the one for which the fee should be applied
        if (is_custom_plugin_product($product_id)) { // Replace with your actual condition
            $product_price = $cart_item['line_subtotal'];
            $extra_fee = ($extra_fee_percentage / 100) * $product_price;
            $total_extra_fee += $extra_fee;
        }
    }

    // Add the extra fee to the cart
    if ($total_extra_fee > 0) {
        $cart->add_fee(__('Extra Shipping Fee', 'your-plugin-textdomain'), $total_extra_fee);
    }
      // Add $10 extra fee if PayPal is the selected payment method
      if (is_paypal_payment_method_selected()) {
        $paypal_fee = 10;
        $cart->add_fee(__('PayPal Fee', 'your-plugin-textdomain'), $paypal_fee);
    }
}

/**
 * Helper function to determine if a product is part of the custom plugin.
 * Replace with your actual condition logic.
 */
function is_custom_plugin_product($product_id) {
    // Check for the product meta key '_product_type' and value 'rollover_tarps'
    $product_type = get_post_meta($product_id, '_product_type', true);
    
    // Return true if the product type is 'rollover_tarps', otherwise false
    return $product_type === 'rollover_tarps';
}


function is_paypal_payment_method_selected() {
    // Access the chosen payment method
    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    return $chosen_payment_method === 'paypal'; // Change 'paypal' if your payment gateway's slug differs
}


add_filter('woocommerce_cart_shipping_packages', function($packages) {
    foreach ($packages as &$package) {
        $total_weight = 0;

        foreach ($package['contents'] as $cart_item) {
            $default_weight = wc_get_weight($cart_item['data']->get_weight(), 'lbs'); // Get weight in pounds
            $custom_weight = isset($cart_item['custom_weight']) ? floatval($cart_item['custom_weight']) : 0;

            // Use custom weight if provided, otherwise use the default weight
            $total_weight += ($custom_weight > 0) ? $custom_weight : $default_weight;
        }

        // Override the total package weight
        $package['contents_weight'] = $total_weight;
    }

    // Log the package details for debugging
    error_log("Modified Shipping Package: " . print_r($packages, true));

    return $packages;
}, 10, 1);
