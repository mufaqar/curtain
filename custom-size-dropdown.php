<?php
/*
Plugin Name: Curtain Options
Description: Custom curtain options to WooCommerce products.
Version: 2.1.5
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
// function set_custom_price_in_cart( $cart_object ) {
//     foreach( $cart_object->get_cart() as $cart_item ) {
//         if( isset( $cart_item['custom_price'] ) ) {
//             $cart_item['data']->set_price( $cart_item['custom_price'] );
//         }
//     }
// }
// add_action( 'woocommerce_before_calculate_totals', 'set_custom_price_in_cart' );


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


// add_action('woocommerce_before_calculate_totals', 'custom_update_product_dimensions_and_weight', 10, 1);

// function custom_update_product_dimensions_and_weight($cart) {
//     if (is_admin() && !defined('DOING_AJAX')) return;

//     // Loop through cart items
//     foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
//         $product = $cart_item['data'];

//         // Replace this with your custom logic or values
//         $custom_weight = 5;
//         $custom_length = 10;
//         $custom_width  = 10;
//         $custom_height = 10;

//         // Update product data dynamically
//         $product->set_weight($custom_weight);
//         $product->set_length($custom_length);
//         $product->set_width($custom_width);
//         $product->set_height($custom_height);
//     }
// }

// add_filter('woocommerce_cart_item_name', 'show_custom_cart_values', 10, 3);
// function show_custom_cart_values($name, $cart_item, $cart_item_key) {
//     if (isset($cart_item['custom_weight'])) {
//         $name .= "<br><small>Weight: {$cart_item['custom_weight']}</small>";
//         $name .= "<br><small>Length: {$cart_item['custom_length']}</small>";
//         $name .= "<br><small>Width: {$cart_item['custom_width']}</small>";
//         $name .= "<br><small>Height: 0.05</small>";
//     }
//     return $name;
// }


// Hook to pass custom dimensions and weight to the product before totals are calculated
add_action('woocommerce_before_calculate_totals', 'custom_update_product_dimensions_and_weight_ups', 20, 1);

function custom_update_product_dimensions_and_weight_ups($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

        // Only apply for 'rollover_tarps' products
        $product_id = $cart_item['product_id'];
        if (!is_custom_plugin_product($product_id)) {
            continue;
        }

       // var_dump($cart);

        // Use your plugin's logic or POST/session/cookies to pass these values
        $custom_weight = isset($cart_item['cal_weight']) ? floatval($cart_item['cal_weight']) : $cart_item['data']->get_weight();
        $custom_length = isset($cart_item['cal_length']) ? floatval($cart_item['cal_length']) : $cart_item['data']->get_length();
        $custom_width  = isset($cart_item['cal_width'])  ? floatval($cart_item['cal_width'])  : $cart_item['data']->get_width();
     

        // Apply to the product object
        $cart_item['data']->set_price( $cart_item['custom_price'] );
        $cart_item['data']->set_weight($custom_weight);
        $cart_item['data']->set_length($custom_length);
        $cart_item['data']->set_width($custom_width);
        $cart_item['data']->set_height(0.05);
    }
}






// add_action('wp_footer', 'mufaqar_print_all_cart_data_for_debug');
// function mufaqar_print_all_cart_data_for_debug() {
//     if (is_admin() || !is_user_logged_in()) return;

//     if (defined('WP_DEBUG') && WP_DEBUG) {
//         echo '<pre style="background:#f0f0f0; padding:20px; border:1px solid #ccc;">';
//         echo "🛒 WooCommerce Cart Contents:\n\n";
//         foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
//             echo "Cart Item Key: $cart_item_key\n";
//             print_r($cart_item);
//             echo "\n----------------------------\n\n";
//         }
//         echo '</pre>';
//     }
// }
