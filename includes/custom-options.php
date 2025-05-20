<?php
function custom_curtain_options_add_to_product() {
    global $product;
  // Get the custom field value
        $enable_custom_curtain_options = get_post_meta($product->get_id(), 'enable_custom_curtain_options', true);
    if ($enable_custom_curtain_options === 'yes') {

    if ($product->is_type('simple')) {
        $product_id = $product->get_id();
        $product_type = get_post_meta($product_id, '_product_type', true);
        $curtain_size = get_post_meta($product_id, '_curtain_size', true) ?: array();

        $product_weight = $product->get_weight(); 

        // LiveStock Material Options
        $livestock_materials = array(
            '15_oz' => '15oz',
            '18_oz' => '18oz',
        );
        // RollOver Material Options
        $rollover_materials = array(
            '18_oz' => '18oz',
            '22_oz' => '22oz'
        );

        $base_price = $product->get_price();

        if ($product_type === 'rollover_tarps') {
            $size_label = 'Size of Tarps :';
        } else {
            $size_label = 'Size of Curtain:';
        }      
       
       

        echo '<div class="custom-curtain-options">';
        echo '<div id="curtain_options" class="form-group curtain-options" style="display: block;">';

         // Hidden field to hold the base price
         echo '<input type="hidden" id="base_price" value="' . esc_attr($base_price) . '">';
         echo '<input type="hidden" name="cal_price" id="cal_price" value="">';
         echo '<input type="hidden" id="cal_weight" name="cal_weight" value="' . esc_attr($product_weight) . '">';
         echo '<input type="hidden" id="cal_width" name="cal_width" value="">';
         echo '<input type="hidden" id="cal_length" name="cal_length" value="">';
       

        // Price Display price_display
        echo '<div id="curtain_price_display" style="margin-top: 10px;">
                <strong>Base Price: $ </strong><span id="price_display">0.00</span>
              </div>';
        
                   // weight_display
        echo '<div id="" style="margin-top: 10px;">
        <strong>'. $size_label.' </strong><span id="size_display">0</span> ft&sup2;
    </div>';

               // weight_display weight_display
        echo '<div id="" style="margin-top: 10px;">
                    <strong>Weight: </strong><span id="weight_display">0</span> lb  
                </div>';

                     // area_display
        echo '<div id="" style="margin-top: 10px;margin-bottom:10px">
                <strong>Box: </strong><span id="area_display">0</span> Boxes
            </div>';

            
                        

        // If Product type is rollover_tarps
        if ($product_type === 'rollover_tarps') {

            echo "<p>Highly durable rollover tarps made to your exact size specifications.<br/>  Most orders ship within 5 - 7 business days.</p>";

              // Curtain Fabric
              echo '<div class="form-group curtain-material">
                    <label for="roll_material">Fabric Options:</label>
                    <select id="roll_material" name="roll_material">';
                        foreach ($rollover_materials as $key => $label) {
                            echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
                        }
                          // Add custom size option
                 
             echo '</select></div>';

             // Width/Size Options
             echo '<div class="form-group curtain-size">
                    <label for="roll_size">Linear Ft. Width:</label>
                    <select id="roll_size" name="roll_size">';
                    foreach ($rollover_size_options as $key => $label) {
                    
                        echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
                    }
                    echo '<option value="size_custom">Custom Size (price x total sq ft)</option>';
                  
                    echo '</select></div>';

           

      
            // Custom Width and Height with Feet and Inches
            echo '<div class="curtain-custom-size-fields">';

            // Display roll_custom_width only if the size is "size_custom"
            echo '<div class="form-group roll_custom_width" style="display: none;">
                    <label for="custom_width_feet">Linear Ft. Width:</label>
                    <div class="inline-inputs">
                        <input type="number" id="custom_width_feet" name="custom_width_feet" value="" placeholder="Feet" class="inline-input">
                        <input type="number" id="custom_width_inches" name="custom_width_inches" value="" placeholder="Inches" class="inline-input">
                    </div>
                </div>';

            echo '<div class="form-group roll_custom_height">
                    <label for="custom_height_feet">Enter desired length of tarp:</label>
                    <div class="inline-inputs">
                        <input type="number" id="custom_height_feet" name="custom_height_feet" value="" placeholder="Feet" class="inline-input">
                        <input type="number" id="custom_height_inches" name="custom_height_inches" value="" placeholder="Inches" class="inline-input">
                    </div>
                </div>';

            echo '</div>'; // End of curtain-custom-size-fields

          
           
            // Tarp Color (changed to select dropdown)
            echo '<div class="form-group tarp-color">
                    <label for="tarp_color">Color:</label>
                    <select id="tarp_color" name="tarp_color">';
            foreach ($tarp_colors as $key => $label) {
                echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
            }
            echo '</select>
                  </div>';
                   // Electric System
            echo '<div class="form-group electric-system">
                    <label for="electric_system">Is this tarp for an electric tarp system?</label>
                    <select id="electric_system" name="electric_system">
                        <option value="no"  >No</option>
                        <option value="yes" >Yes</option>
                    </select>
                </div>';

          

                

        }

        // If Product type is livestock_curtains
        if ($product_type === 'livestock_curtains') {
            // Width/Size Options
            echo '<div class="form-group curtain-size">
                    <label for="curtain_size">Linear Ft. Width:</label>
                    <select id="curtain_size" name="curtain_size">';
            foreach ($livestock_size_options as $key => $label) {
                echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
            }
            echo '</select></div>';

            // Custom Width and Height with Feet and Inches
            echo '<div class="curtain-custom-size-fields">';
                echo '<div class="form-group curtain_custom_width">
                        <label for="custom_width_feet">Linear Ft. Width:</label>
                        <div class="inline-inputs">
                            <input type="number" id="custom_width_feet" name="custom_width_feet" value="" placeholder="Feet" class="inline-input">
                            <input type="number" id="custom_width_inches" name="custom_width_inches" value="" placeholder="Inches" class="inline-input">
                        </div>
                    </div>';

            echo '<div class="form-group curtain_custom_height">
                        <label for="custom_height_feet">Linear Ft. Length:</label>
                        <div class="inline-inputs">
                            <input type="number" id="custom_height_feet" name="custom_height_feet" value="" placeholder="Feet" class="inline-input">
                            <input type="number" id="custom_height_inches" name="custom_height_inches" value="" placeholder="Inches" class="inline-input">
                        </div>
                    </div>';

            echo '</div>';

            // Curtain Fabric
            echo '<div class="form-group curtain-material">
                    <label for="curtain_material">Curtain Fabric:</label>
                    <select id="curtain_material" name="curtain_material">';
            foreach ($livestock_materials as $key => $label) {
                echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
            }
            echo '</select></div>';

              // Curtain Hem
              $hem_options = array(
                '3_hem' => '3" Hem',
                '4_hem' => '4" Hem',
            );
            echo '<div class="curtain-hem">
                    <label for="curtain_hem">Choose a hem:</label>
                    <select id="curtain_hem" name="curtain_hem">';
            foreach ($hem_options as $key => $value) {
                $selected = ($curtain_hem == $key) ? 'selected' : '';
                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
            }
            echo '</select></div>';

            // Curtain Optional Add-ons
            echo '<div class="curtain-addons">';
            
            $second_hem_options = array(
                'none' => 'None',
                '3_hem' => '3" Hem',
                '4_hem' => '4" Hem',
            );
            echo '<label>Add a second hem:</label>
                <select id="second_hem" name="second_hem">';
            foreach ($second_hem_options as $key => $value) {
                $selected = ($second_hem == $key) ? 'selected' : '';
                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
            }
            echo '</select> </div>';

            $pipe_pocket_options = array(
                'none' => 'None',
                '1' => '1',
                '2' => '2',
                '3' => '3',
            );
            echo '<label>Add a Pipe Pocket:</label>
                  <select id="pipe_pocket" name="pipe_pocket">';
            foreach ($pipe_pocket_options as $key => $value) {
                $selected = ($pipe_pocket == $key) ? 'selected' : '';
                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
            }
            echo '</select>';

            echo '<div class="form-group curtain-addons">
                    <input type="checkbox" id="webbing_reinforcement" name="webbing_reinforcement" value="yes">
                    <label for="webbing_reinforcement">2″ Webbing Reinforcement:</label>
                  </div>';
       

        }         

        echo '</div>'; // End of curtain_options
        echo '</div>'; // End of custom-curtain-options
    }
}
}
add_action('woocommerce_before_add_to_cart_button', 'custom_curtain_options_add_to_product');