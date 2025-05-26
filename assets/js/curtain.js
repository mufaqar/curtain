jQuery(document).ready(function ($) {
  function convertFeetToInches(feet) {
    return feet * 12;
  }

  function updateSizeOptions() {
    var materialType = $('#curtain_material').val();
    var curtainSizeSelect = $('#curtain_size');
    // Clear current options
    curtainSizeSelect.empty();

    // Add new size options based on the selected material
    if (prices[materialType] && prices[materialType].lin_pr) {
      var sizeOptions = prices[materialType].lin_pr;
      $.each(sizeOptions, function (key, value) {
        console.log('Adding size option:', value.label, 'with key:', key);
        curtainSizeSelect.append(new Option(value.label, key));
      });
    }
    updatePriceAndConvertSize();
  }

  function updatePriceAndConvertSize() {
    var basePrice = 0;
    var materialType = $('#curtain_material').val();
    var sizeValue = $('#curtain_size').val();
    var sqWeightValue = prices[materialType] ? prices[materialType].wt || 0 : 0;

    // Calculate total feet from feet and inches inputs
    var custom_height_feet = parseFloat($('#custom_height_feet').val()) || 0;
    var custom_height_inches =
      parseFloat($('#custom_height_inches').val()) || 0;
    var TFH = custom_height_feet + custom_height_inches / 12;
    var custom_width_feet = parseFloat($('#custom_width_feet').val()) || 0;
    var custom_width_inches = parseFloat($('#custom_width_inches').val()) || 0;
    var TFW = custom_width_feet + custom_width_inches / 12;

    // Initialize material price
    var materialPrice = 0;

    if (sizeValue !== 'size_custom') {
      // Calculate material price based on the selected size
      materialPrice = prices[materialType]?.lin_pr[sizeValue]?.price || 0;
      materialWidth = prices[materialType]?.lin_pr[sizeValue]?.width || 0;

      var sq_inch_totalArea = materialWidth * 12 * (TFH * 12);

      var cubic_Area_Tarp = sq_inch_totalArea * 0.03;
      var cubic_Area_Box = 5880;
      var Total_Box = cubic_Area_Tarp / cubic_Area_Box;

      $('#area_display').text(Math.ceil(Total_Box));

      if (TFH > 0) {
        materialPrice = TFH * materialPrice;
      } else {
        materialPrice = 0;
      }

      let TotalWeight = materialWidth * TFH;
      let CalWeight = sqWeightValue * TotalWeight;
      $('#size_display').text(TotalWeight);
      $('#weight_display').text(CalWeight.toFixed(2));

      // Hide custom size fields if a predefined size is selected
      $('.curtain_custom_width').hide();
      $('.curtain_custom_height').show();
    } else {
      materialPrice = prices[materialType]?.lin_pr[sizeValue]?.price || 0;

      var customSizePrice = getCustomSizePrice(TFW, TFH, materialType);

      let TotalWeight = TFW * TFH;
      $('#size_display').text(TotalWeight);

      var sq_inch_totalArea = TFW * 12 * (TFH * 12);

      var cubic_Area_Tarp = sq_inch_totalArea * 0.03;
      var cubic_Area_Box = 5880;
      var Total_Box = cubic_Area_Tarp / cubic_Area_Box;

      $('#area_display').text(Math.ceil(Total_Box));

      let CalWeight = sqWeightValue * TotalWeight;
      $('#weight_display').text(CalWeight.toFixed(2));

      $('.curtain_custom_width').show();
      $('.curtain_custom_height').show();

      materialPrice = customSizePrice;
    }



    // Get other price components
    var lengthPrice = getLengthPrice(TFH);
    var hemPrice = getHemPrice(materialType, TFH); // Default hem price
    var secondHemPrice =
      $('#second_hem').val() !== 'none'
        ? getSecondHemPrice(materialType, TFH)
        : 0;
    var pipePocketPrice =
      $('#pipe_pocket').val() !== 'none'
        ? getPipePocketPrice(materialType, TFH)
        : 0;
    var webbingReinforcementPrice = $('#webbing_reinforcement').is(':checked')
      ? getWebbingReinforcementPrice(materialType, TFH)
      : 0;

    console.log('Length Price:', lengthPrice);
    console.log('Hem Price:', hemPrice);
    console.log('Second Hem Price:', secondHemPrice);
    console.log('Pipe Pocket Price:', pipePocketPrice);
    console.log('Webbing Reinforcement Price:', webbingReinforcementPrice);

    // Calculate total price  hemPrice +
    var totalPrice =
      basePrice +
      materialPrice +
      lengthPrice +
      secondHemPrice +
      pipePocketPrice +
      webbingReinforcementPrice;

    // Ensure totalPrice is a valid number
    totalPrice = isNaN(totalPrice) ? 0 : totalPrice;

    console.log('Total Price:', totalPrice);

    // Update the displayed price
    $('#price_display').text('$' + totalPrice.toFixed(2));
    $('#cal_price').val(totalPrice.toFixed(2));
  }

  function toggleCustomSizeFields() {
    // Trigger the price update and field toggle logic when the size is changed
    updatePriceAndConvertSize();
  }

  // Attach event listeners to form fields
  $('#curtain_material').on('change', updateSizeOptions);
  $(
    '#curtain_size, #curtain_length, #curtain_hem, #second_hem, #pipe_pocket, #webbing_reinforcement, #custom_height_feet, #custom_height_inches , #custom_width_feet , #custom_width_inches'
  ).on('input change', updatePriceAndConvertSize);

  // Initial trigger for material and size options
  updateSizeOptions();
  toggleCustomSizeFields();

  // Functions to retrieve prices
  function getLengthPrice(lengthValue) {
    var lengthPrices = {};
    for (var i = 11; i <= 50; i++) {
      lengthPrices[i.toString()] = i;
    }
    return lengthPrices[lengthValue] || 0;
  }

  function getHemPrice(materialType, TFH) {
    var materialPricePerUnit = prices[materialType]
      ? prices[materialType].him || 0
      : 0;
    return TFH * materialPricePerUnit; // Use TFH for second hem price calculation
  }

  function getSecondHemPrice(materialType, TFH) {
    var materialPricePerUnit = prices[materialType]
      ? prices[materialType].him || 0
      : 0;
    return TFH * materialPricePerUnit; // Use TFH for second hem price calculation
  }

  function getPipePocketPrice(materialType, TFH) {
    var pipePocketQuantity = parseInt($('#pipe_pocket').val()) || 0;
    if (pipePocketQuantity === 0) return 0;
    var materialPricePerUnit = prices[materialType]
      ? prices[materialType].pocket || 0
      : 0;
    return TFH * materialPricePerUnit * pipePocketQuantity; // Use TFH for pipe pocket price calculation
  }

  function getWebbingReinforcementPrice(materialType, TFH) {
    var materialPricePerUnit = prices[materialType]
      ? prices[materialType].web || 0
      : 0;
    return TFH * materialPricePerUnit; // Use TFH for webbing reinforcement price calculation
  }

  function getCustomSizePrice(TFW, TFH, materialType) {
    var squareFeet = TFW * TFH;
    var pricePerSquareFoot = prices[materialType]
      ? prices[materialType].lin_pr.size_custom.price || 0
      : 0;
    return squareFeet * pricePerSquareFoot;
  }
});

// Define the prices object with example data
var prices = {
  '15_oz': {
    lin_pr: {
      size_5: {
        price: 4.05,
        label: '5\' with 1 3" Hem (58") or 4" Hem',
        width: 5,
      },
      size_6: {
        price: 4.86,
        label: '6\' with 1 3" Hem (69") or 4" Hem',
        width: 6,
      },
      size_9: {
        price: 7.28,
        label: '9\' with 1 3" Hem (105") or 4" Hem',
        width: 9,
      },
      size_12: {
        price: 9.71,
        label: '12\' with 1 3" Hem (141") or 4" Hem (140")',
        width: 12,
      },
      size_custom: {
        price: 0.81,
        label: 'Custom Size (price x total sq ft)',
      },
    },
    him: 0.54,
    pocket: 1.92,
    web: 0.55,
    wt: 0.1552086,
  },
  '18_oz': {
    lin_pr: {
      size_5: {
        price: 5.04,
        width: 5,
        label: '5\' with 1 3" Hem (58") or 4" Hem',
      },
      size_6: {
        price: 6.05,
        width: 6,
        label: '6\' with 1 3" Hem (69") or 4" Hem',
      },
      size_9: {
        price: 9.07,
        width: 9,
        label: '9\' with 1 3" Hem (105") or 4" Hem',
      },
      size_12: {
        price: 12.1,
        width: 12,
        label: '12\' with 1 3" Hem (141") or 4" Hem (140")',
      },
      size_custom: {
        price: 1.01,
        label: 'Custom Size (price x total sq ft)',
      },
    },
    him: 0.61,
    pocket: 2.16,
    web: 0.55,
    wt: 0.1785714,
  },
};
