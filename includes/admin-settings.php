<?php
function custom_curtain_options_product_custom_fields() {
    global $post;
    $product_id = $post->ID;

    $predefined_materials = array(
        '15_oz' => '15oz',
        '18_oz' => '18oz',
        '22_oz' => '22oz'
    );

    $predefined_size_options = array(
        'size_1' => '5\' with 1 3" Hem (58") or 4" Hem',
        'size_2' => '6\' with 1 3" Hem (69") or 4" Hem',
        'size_3' => '9\' with 1 3" Hem (105") or 4" Hem',
        'size_4' => '12\' with 1 3" Hem (141") or 4" Hem (140")',
        'size_5' => 'Tarp Size: 10\'3" x 10',
        'size_6' => 'Tarp Size: 10\'6" x 10',
        'size_7' => 'Tarp Size: 10\'9" x 10',
        'custom' => 'Custom Size (price x total sq ft)',
    );

    $curtain_material = get_post_meta($product_id, '_curtain_material', true) ?: array();
    $curtain_size = get_post_meta($product_id, '_curtain_size', true) ?: array();
    $curtain_custom_width = get_post_meta($product_id, '_curtain_custom_width', true);
    $curtain_custom_height = get_post_meta($product_id, '_curtain_custom_height', true);
    $product_type = get_post_meta($product_id, '_product_type', true);

    echo '<div class="options_group">';

    woocommerce_wp_select(array(
        'id' => '_product_type',
        'label' => __('Product Type', 'custom-curtain-options'),
        'options' => array(
            'livestock_curtains' => __('Livestock Curtains', 'custom-curtain-options'),
            'rollover_tarps' => __('Rollover Tarps', 'custom-curtain-options'),
        ),
        'value' => $product_type,
        'desc_tip' => true,
    ));

  

  
    

    echo '</div>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function toggleFieldsByProductType() {
                var selectedProductType = $('#_product_type').val();
                if (selectedProductType === 'livestock_curtains' || selectedProductType === 'rollover_tarps') {
                    $('#_curtain_hem_field, #_second_hem_field, #_pipe_pocket_field, #_webbing_reinforcement_field, #_additional_details_field, #_electric_system_field, #_curtain_length_field').hide();
                } else {
                    $('#_curtain_length_field').show();
                }
            }

            $('#_product_type').change(function() {
                toggleFieldsByProductType();
            }).change();

            $('#_curtain_size').change(function() {
                if ($('#_curtain_size option[value="custom"]').is(':selected')) {
                    $('.curtain-custom-size-fields').show();
                } else {
                    $('.curtain-custom-size-fields').hide();
                }
            }).change();
        });
    </script>
    <?php
}
add_action('woocommerce_product_options_general_product_data', 'custom_curtain_options_product_custom_fields');

function custom_curtain_options_save_product_custom_fields($post_id) {
    $fields = array(
        '_curtain_custom_width',
        '_curtain_custom_height',
        '_product_type',
        '_curtain_length',
    );

    if (isset($_POST['_curtain_material'])) {
        update_post_meta($post_id, '_curtain_material', array_map('sanitize_text_field', $_POST['_curtain_material']));
    } else {
        delete_post_meta($post_id, '_curtain_material');
    }

    if (isset($_POST['_curtain_size'])) {
        update_post_meta($post_id, '_curtain_size', array_map('sanitize_text_field', $_POST['_curtain_size']));
    } else {
        delete_post_meta($post_id, '_curtain_size');
    }
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        } else {
            delete_post_meta($post_id, $field);
        }
    }
}
add_action('woocommerce_process_product_meta', 'custom_curtain_options_save_product_custom_fields');
