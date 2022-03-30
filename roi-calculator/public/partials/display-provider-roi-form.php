<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://esipick.com/
 * @since      1.0.0
 *
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/public/partials
 */
?>

<div class="range-container">
  <h3>Share your Health System details to get started.</h3>
<div class="custom-slider-widget">
    <label for="annual_invoice_volume">How many <strong>invoices</strong> do you process annually?</label>
    <div class="slider-wrapper">
      <input type="range" min="0" max="15000" value="7500" id="annual_invoice_volume" step="1">
      <div class="custom-track" aria-hidden="true">
        <span class="custom-fill" id="annual_invoice_volume_fill"></span>
        <span class="custom-thumb" id="annual_invoice_volume_thumb"></span>
      </div>
      <output for="annual_invoice_volume"  class="bubble"></output>
      <input type="text" for="annual_invoice_volume" class="range-track-field" id="annual_invoice_volume_output" aria-hidden="true" value="7500" />
    </div>
  </div>

  <div class="custom-slider-widget">
    <label for="annual_payment_volume">How many <strong>Payments</strong> do you process annually?</label>
    <div class="slider-wrapper">
      <input type="range" min="0" max="100000" value="50000" id="annual_payment_volume" step="1">
      <div class="custom-track" aria-hidden="true">
        <span class="custom-fill" id="annual_payment_volume_fill"></span>
        <span class="custom-thumb" id="annual_payment_volume_thumb"></span>
      </div>
      <output for="annual_payment_volume"  class="bubble"></output>
      $<input type="text" for="annual_payment_volume" id="annual_payment_volume_output"  class="range-track-field" aria-hidden="true" value="50000" />
    </div>
  </div>
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<script src="//offers.premierinc.com/js/forms2/js/forms2.min.js"></script>
<form id="mktoForm_5011"></form>

<script>


MktoForms2.loadForm("//offers.premierinc.com", "381-NBB-525", 5011, function(form) {


    jQuery(document).ready(function($){
        $(document).on('click', '.mktoButton', function(e){
            e.preventDefault();
            const paymentVolume = jQuery('#annual_payment_volume_output').val();
            const invoiceVolume = jQuery('#annual_invoice_volume_output').val();
            $.ajax({
                method: 'POST',
                data: { annual_payment_volume: paymentVolume, annual_invoice_volume: invoiceVolume },
                url: '<?=site_url()?>/wp-json/rest/roi/calculator/provider/',
                success: function(result) {
                  localStorage.setItem('total_annual_savings', result['data']['total_annual_savings']);
                  localStorage.setItem('total_hours_saved', result['data']['total_hours_saved']);
                    form.vals({ "Remitra_Annual_Invoice_Volume__c": invoiceVolume, "Remitra_Annual_Payment_Volume__c": paymentVolume, "Remitra_Annual_Hours_Saved__c": result['data']['total_hours_saved'], "Remitra_Annual_Costs_Saved__c": result['data']['total_annual_savings']});
                    form.submit();
                }   
            });    
        });
    });


    //Add an onSuccess handler
    form.onSuccess(function(values, followUpUrl) {
        // Take the lead to a different page on successful submit, ignoring the form's configured followUpUrl
        location.href = "<?=site_url()?>/roi-thank-you/";
        // Return false to prevent the submission handler continuing with its own processing
        return false;
    });
});

// range slider output
function initSlider(min, max, startValue, elementSelector) {
    const slider = document.querySelector('.slider-wrapper'),
      sliderInput = jQuery(elementSelector),
      sliderOutput = jQuery(elementSelector+'_output'),
      sliderThumb = jQuery(elementSelector+'_thumb'),
      sliderFill = jQuery(elementSelector+'_fill');
      const bubble = sliderInput.closest('.slider-wrapper').find(".bubble");

      sliderInput.attr('min', min);
      sliderInput.attr('max', max);
      sliderInput.val(startValue);
      
  const onSliderChange = function(event) {
    let value = event.target.value;
    sliderOutput.val(value);
    sliderThumb.css('left', (value/max * 100) + '%');
    sliderFill.css('width', (value/max * 100) + '%');
    setBubble(sliderInput, bubble);
  }
  
  sliderInput.on('input', onSliderChange);
  sliderInput.on('change', onSliderChange);

  setBubble(sliderInput, bubble);
}

initSlider(0, 15000, 7500, '#annual_invoice_volume');
initSlider(0, 100000, 50000, '#annual_payment_volume');

function setBubble(range, bubble) {
  const val = range.val();
  const max = range.attr('max') ? parseInt(range.attr('max')) : 100;
  bubble.text(val);

  // Sorta magic numbers based on size of the native UI thumb
  bubble.css('left', ((val/max * 70))+'%');
}


jQuery(document).ready(function($){
  var x_timer; 
  $("#annual_invoice_volume_output").keyup(function (e){
    clearTimeout(x_timer);
    const value = parseInt($(this).val());
    x_timer = setTimeout(function(){
      const maxVal = parseInt($('#annual_invoice_volume').attr('max'));
      if (maxVal > value) {
        $('#annual_invoice_volume').val(value);
        $('#annual_invoice_volume').trigger('change');
      }
    }, 800);
  });

  $("#annual_payment_volume_output").keyup(function (e){
      clearTimeout(x_timer);
      const value = parseInt($(this).val());
      x_timer = setTimeout(function(){
        const maxVal = parseInt($('#annual_payment_volume').attr('max'));
      if (maxVal > value) {
        $('#annual_payment_volume').val(value);
        $('#annual_payment_volume').trigger('change');
      }
      }, 800);
  });

});
</script>