jQuery(document).ready(function ($) {
  var tarpColors = {
    '18_oz': {
      black: 'Black',
      gray: 'Gray',
      green: 'Green',
      orange: 'Orange',
      purple: 'Purple',
      red: 'Red',
      royal_blue: 'Royal Blue',
      tan: 'Tan',
      white: 'White',
      yellow: 'Yellow',
    },
    '22_oz': {
      black: 'Black',
      blue: 'Blue',
      red: 'Red',
      white: 'White',
    },
  };

  var prices = {
    '18_oz': {
      roll_pr: {
        size_96: {
          price: 13.95,
          width: 10.25,
          label: '10\'3" width (96" trailer width)',
          value: 10.3,
        },
        size_99: {
          price: 14.5,
          width: 10.5,
          label: '10\'6" width (99" trailer width)',
          value: 10.6,
        },
        size_102: {
          price: 14.65,
          width: 10.75,
          label: '10\'9" width (102" trailer width)',
          value: 10.9,
        },
        size_custom: {
          price: 1.55, // Example price per sq ft for custom size
          label: 'Custom Size (price x total sq ft)',
        },
      },
      ele: 17.0,
      wt: 0.1552086
    },
    '22_oz': {
      roll_pr: {
        size_96: {
          price: 16.75,
          width: 10.25,
          label: '10\'3" width (96" trailer width)',
          value: 10.3,
        },
        size_99: {
          price: 17.25,
          width: 10.5,
          label: '10\'6" width (99" trailer width)',
          value: 10.6,
        },
        size_102: {
          price: 17.5,
          width: 10.75,
          label: '10\'9" width (102" trailer width)',
          value: 10.9,
        },
        size_custom: {
          price: 1.75,
          label: 'Custom Size (price x total sq ft)',
        },
      },
      ele: 17.0,
      wt: 0.1785714
    },
  };

  function updateTarpColors() {
    var selectedMaterial = $('#roll_material').val();
    var colorOptions = tarpColors[selectedMaterial] || {};
    var $tarpColorSelect = $('#tarp_color');
    // Clear current color options
    $tarpColorSelect.empty();

    // Populate new options based on the selected material
    $.each(colorOptions, function (key, value) {
      $tarpColorSelect.append(new Option(value, key));
    });
  }

  function updateRollSizeOptions() {
    var selectedMaterial = $('#roll_material').val();
    var sizeOptions = prices[selectedMaterial]?.roll_pr || {};
    var $rollSizeSelect = $('#roll_size');

    // Clear current size options
    $rollSizeSelect.empty();

    // Populate new options based on the selected material
    $.each(sizeOptions, function (key, value) {
      $rollSizeSelect.append(new Option(value.label, key));
    });
  }

  function updateCustomFields() {
    var selectedSize = $('#roll_size').val();

    if (selectedSize === 'size_custom') {
      $('.roll_custom_width').show(); // Show custom width if size is custom
    } else {
      $('.roll_custom_width').hide(); // Hide custom width otherwise
    }
  }

  function updatePrice() {
    var selectedMaterial = $('#roll_material').val();
    var selectedSize = $('#roll_size').val();


    
    var selectedPricePerSqFt =  prices[selectedMaterial]?.roll_pr[selectedSize]?.price || 0;
 

    var selectedWidth =   prices[selectedMaterial]?.roll_pr[selectedSize]?.width || 0;     


      selectedHeight = convertHeightToFeet();

      var sq_inch_totalArea =  (selectedWidth *12 ) * (selectedHeight * 12);

      var cubic_Area_Trap = sq_inch_totalArea* .03;

      var cubic_Area_Box = 5880;
  
      var Total_Box = cubic_Area_Trap/cubic_Area_Box;

    

    // Get the total height in feet
    var totalHeightFeet = convertHeightToFeet();
    
    // Calculate the total area (width * height)
    var totalArea =  totalHeightFeet;
    // Calculate the total price based on the area and price per square foot
    var totalPrice = totalHeightFeet * selectedPricePerSqFt;


    var sqWeightValue = prices[selectedMaterial]?.wt || 0;
 
    let WH = selectedWidth * selectedHeight;
    let TotalWeight = sqWeightValue * WH;
    
    $('#total_price_display').text('$' + (totalPrice ).toFixed(2));
    $('#cal_weight').val(TotalWeight.toFixed(2));

 
   

    $('#weight_display').text( TotalWeight);
    $('#area_display').text( Math.ceil(Total_Box));
    $('#size_display').text( selectedWidth * selectedHeight);



    // Calculate custom width if the size is custom
    if (selectedSize === 'size_custom') {
     
      selectedWidth = convertWidthToFeet();
     
      selectedHeight = convertHeightToFeet();

      var sq_inch_totalArea =  (selectedWidth *12 ) * (selectedHeight * 12);
     
      var totalArea =  selectedWidth * selectedHeight ;

      var cubic_Area_Trap = sq_inch_totalArea* .03;

      var cubic_Area_Box = 5880;

      var Total_Box = cubic_Area_Trap/cubic_Area_Box;    


      var totalPrice = totalArea * selectedPricePerSqFt;

    
      var sqWeightValue = prices[selectedMaterial]?.wt || 0;
    

      let TotalWeight = sqWeightValue * totalArea;




      $('#weight_display').text( TotalWeight);
      $('#area_display').text( Math.ceil(Total_Box));
      $('#size_display').text( selectedWidth * selectedHeight);
      $('#cal_weight').val(TotalWeight.toFixed(2));
    
   
    }

    $('#cal_width').val(selectedWidth);
    $('#cal_length').val(selectedHeight);

    console.log(" Weight:", TotalWeight)
    console.log(" Width:", selectedWidth);
    console.log(" Length:", selectedHeight);


    // Check if the electric system is selected and add the price
    var electricSystem = $('#electric_system').val();
    if (electricSystem === 'yes') {
      var electricSystemPrice = prices[selectedMaterial]?.ele || 0;
      totalPrice += electricSystemPrice;
    }

    // Update the price display
    $('#price_display').text('$' + totalPrice.toFixed(2));
    $('#cal_price').val(totalPrice.toFixed(2));
   

  }

  function convertWidthToFeet() {
    var feet_w = parseFloat($('#custom_width_feet').val()) || 0;
    var inches_w = parseFloat($('#custom_width_inches').val()) || 0;
    var totalFeet_Width = feet_w + inches_w / 12;
    return totalFeet_Width;
  }

  function convertHeightToFeet() {
    var feet_h = parseFloat($('#custom_height_feet').val()) || 0;
    var inches_h = parseFloat($('#custom_height_inches').val()) || 0;
    var totalFeet_Length = feet_h + inches_h / 12;
    return totalFeet_Length;
  }

  // Trigger updates when the material, size, height, or electric system changes
  $('#roll_material').on('change', function () {
    updateTarpColors();
    updateRollSizeOptions();
    updatePrice(); // Update price when material changes
  });

  $('#roll_size').on('change', function () {
    updateCustomFields();
    updatePrice(); // Update price when size changes
  });

  $('#custom_width_feet, #custom_width_inches, #custom_height_feet, #custom_height_inches').on(
    'input change',
    function () {
      updatePrice(); // Update price when custom width or height changes
    }
  );

  $('#electric_system').on('change', function () {
    updatePrice(); // Update price when electric system option changes
  });

  // Initialize color, size options, and price on page load
  updateTarpColors();
  updateRollSizeOptions();
  updatePrice();
});
