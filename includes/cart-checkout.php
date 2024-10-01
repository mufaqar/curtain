<?php


// Save custom options in cart for both livestock_curtains and rollover_tarps
function custom_curtain_options_save_custom_options($cart_item_data, $product_id, $variation_id) {
    $product_type = get_post_meta($product_id, '_product_type', true); // Assuming product type is stored in post meta

    if ($product_type === 'livestock_curtains' || $product_type === 'rollover_tarps') {

        if (isset($_POST['cal_price'])) {
            $cart_item_data['calculated_price'] = sanitize_text_field($_POST['cal_price']);
        }
        // Common fields for both product types
        if (isset($_POST['roll_material'])) {
            $cart_item_data['roll_material'] = sanitize_text_field($_POST['roll_material']);
        }
        if (isset($_POST['curtain_material'])) {
            $cart_item_data['curtain_material'] = sanitize_text_field($_POST['curtain_material']);
        }

        // Size fields
        if (isset($_POST['roll_size'])) {
            $cart_item_data['roll_size'] = sanitize_text_field($_POST['roll_size']);
            if ($_POST['roll_size'] == 'size_custom') {
                $cart_item_data['custom_width_feet'] = sanitize_text_field($_POST['custom_width_feet']);
                $cart_item_data['custom_width_inches'] = sanitize_text_field($_POST['custom_width_inches']);
                $cart_item_data['custom_height_feet'] = sanitize_text_field($_POST['custom_height_feet']);
                $cart_item_data['custom_height_inches'] = sanitize_text_field($_POST['custom_height_inches']);
            }
        }

        // Tarp Color
        if (isset($_POST['tarp_color'])) {
            $cart_item_data['tarp_color'] = sanitize_text_field($_POST['tarp_color']);
        }

        // Electric system
        if (isset($_POST['electric_system'])) {
            $cart_item_data['electric_system'] = sanitize_text_field($_POST['electric_system']);
        }

        // Curtain Hems and Add-ons
        if (isset($_POST['curtain_hem'])) {
            $cart_item_data['curtain_hem'] = sanitize_text_field($_POST['curtain_hem']);
        }
        if (isset($_POST['second_hem'])) {
            $cart_item_data['second_hem'] = sanitize_text_field($_POST['second_hem']);
        }
        if (isset($_POST['pipe_pocket'])) {
            $cart_item_data['pipe_pocket'] = sanitize_text_field($_POST['pipe_pocket']);
        }
        if (isset($_POST['webbing_reinforcement'])) {
            $cart_item_data['webbing_reinforcement'] = sanitize_text_field($_POST['webbing_reinforcement']);
        }  
        
        
        if( isset( $_POST['cal_price'] ) ) {
            $custom_price = floatval( $_POST['cal_price'] );
            $cart_item_data['custom_price'] = $custom_price;
        }
        return $cart_item_data;
    }
    
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'custom_curtain_options_save_custom_options', 10, 3);


// checkout 

// Display custom options in cart and checkout
function custom_curtain_options_display_custom_options($item_data, $cart_item) {
    
    if (isset($cart_item['roll_material'])) {
        $item_data[] = array(
            'key' => __('Roll Material', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['roll_material']),
        );
    }
    if (isset($cart_item['curtain_material'])) {
        $item_data[] = array(
            'key' => __('Curtain Material', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['curtain_material']),
        );
    }
    if (isset($cart_item['roll_size'])) {
        $item_data[] = array(
            'key' => __('Roll Size', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['roll_size']),
        );
        if ($cart_item['roll_size'] == 'size_custom') {
            $item_data[] = array(
                'key' => __('Custom Width (Feet)', 'custom-curtain-options'),
                'value' => wc_clean($cart_item['custom_width_feet'] . ' ft ' . $cart_item['custom_width_inches'] . ' in'),
            );
            $item_data[] = array(
                'key' => __('Custom Height (Feet)', 'custom-curtain-options'),
                'value' => wc_clean($cart_item['custom_height_feet'] . ' ft ' . $cart_item['custom_height_inches'] . ' in'),
            );
        }
    }
    if (isset($cart_item['tarp_color'])) {
        $item_data[] = array(
            'key' => __('Tarp Color', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['tarp_color']),
        );
    }
    if (isset($cart_item['electric_system'])) {
        $item_data[] = array(
            'key' => __('Electric Tarp System', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['electric_system']),
        );
    }
    if (isset($cart_item['curtain_hem'])) {
        $item_data[] = array(
            'key' => __('Curtain Hem', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['curtain_hem']),
        );
    }
    if (isset($cart_item['second_hem'])) {
        $item_data[] = array(
            'key' => __('Second Hem', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['second_hem']),
        );
    }
    if (isset($cart_item['pipe_pocket'])) {
        $item_data[] = array(
            'key' => __('Pipe Pocket', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['pipe_pocket']),
        );
    }
    if (isset($cart_item['webbing_reinforcement'])) {
        $item_data[] = array(
            'key' => __('Webbing Reinforcement', 'custom-curtain-options'),
            'value' => wc_clean($cart_item['webbing_reinforcement']),
        );
    }
    if (isset($cart_item['calculated_price'])) {
        $item_data[] = array(
            'name' => 'Calucated Price',
            'value' => wc_price($cart_item['calculated_price'])
        );
    }

    return $item_data;
}
add_filter('woocommerce_get_item_data', 'custom_curtain_options_display_custom_options', 10, 2);


// Save custom options to order
function custom_curtain_options_save_custom_options_to_order($item, $cart_item_key, $values, $order) {

    if (isset($values['calculated_price'])) {
        $item->add_meta_data('Custom Curtain Price', wc_price($values['calculated_price']));
    }
    if (isset($values['roll_material'])) {
        $item->add_meta_data(__('Roll Material', 'custom-curtain-options'), $values['roll_material']);
    }
    if (isset($values['curtain_material'])) {
        $item->add_meta_data(__('Curtain Material', 'custom-curtain-options'), $values['curtain_material']);
    }
    if (isset($values['roll_size'])) {
        $item->add_meta_data(__('Roll Size', 'custom-curtain-options'), $values['roll_size']);
        if ($values['roll_size'] == 'size_custom') {
            $item->add_meta_data(__('Custom Width (Feet)', 'custom-curtain-options'), $values['custom_width_feet'] . ' ft ' . $values['custom_width_inches'] . ' in');
            $item->add_meta_data(__('Custom Height (Feet)', 'custom-curtain-options'), $values['custom_height_feet'] . ' ft ' . $values['custom_height_inches'] . ' in');
        }
    }
    if (isset($values['tarp_color'])) {
        $item->add_meta_data(__('Tarp Color', 'custom-curtain-options'), $values['tarp_color']);
    }
    if (isset($values['electric_system'])) {
        $item->add_meta_data(__('Electric Tarp System', 'custom-curtain-options'), $values['electric_system']);
    }
    if (isset($values['curtain_hem'])) {
        $item->add_meta_data(__('Curtain Hem', 'custom-curtain-options'), $values['curtain_hem']);
    }
    if (isset($values['second_hem'])) {
        $item->add_meta_data(__('Second Hem', 'custom-curtain-options'), $values['second_hem']);
    }
    if (isset($values['pipe_pocket'])) {
        $item->add_meta_data(__('Pipe Pocket', 'custom-curtain-options'), $values['pipe_pocket']);
    }
    if (isset($values['webbing_reinforcement'])) {
        $item->add_meta_data(__('Webbing Reinforcement', 'custom-curtain-options'), $values['webbing_reinforcement']);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'custom_curtain_options_save_custom_options_to_order', 10, 4);




// function update_price_before_adding_to_cart( $cart_item_data, $product_id ) {
//     if( isset( $_POST['custom_price'] ) ) {
//         $custom_price = floatval( $_POST['custom_price'] );
//         $cart_item_data['custom_price'] = $custom_price;
//     }
//     return $cart_item_data;
// }
//add_filter( 'woocommerce_add_cart_item_data', 'update_price_before_adding_to_cart', 10, 2 );

