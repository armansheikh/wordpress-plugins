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
<h3>Share your supplier details to get started.</h3>
<div class="custom-slider-widget">
    <label for="annual_invoice_volume">How many <strong>invoices</strong> do you send out annually?</label>
    <div class="slider-wrapper">
      <input type="range" min="0" max="15000" value="7500" id="annual_invoice_volume" step="1">
      <div class="custom-track" aria-hidden="true">
        <span class="custom-fill" id="annual_invoice_volume_fill"></span>
        <span class="custom-thumb" id="annual_invoice_volume_thumb"></span>
      </div>
      <output for="annual_invoice_volume"  class="bubble"></output>
      <input for="annual_invoice_volume" id="annual_invoice_volume_output" class="range-track-field" value="7500" aria-hidden="true">
    </div>
  </div>

  <div class="custom-slider-widget">
    <label for="average_sales_dso">What is your <strong>average DSO</strong>?</label>
    <div class="slider-wrapper">
      <input type="range" min="0" max="100" value="50" id="average_sales_dso" step="1">
      <div class="custom-track" aria-hidden="true">
        <span class="custom-fill" id="average_sales_dso_fill"></span>
        <span class="custom-thumb" id="average_sales_dso_thumb"></span>
      </div>
      <output for="average_sales_dso"  class="bubble"></output>
      $<input for="average_sales_dso" id="average_sales_dso_output" class="range-track-field" value="50" aria-hidden="true">
    </div>
  </div>

  <div class="custom-slider-widget">
    <label for="annual_sales">What is your <strong>annual sales</strong>?</label>
    <div class="slider-wrapper">
      <input type="range" min="0" max="90000000" value="45000000" id="annual_sales"  step="1">
      <div class="custom-track" aria-hidden="true">
        <span class="custom-fill" id="annual_sales_fill"></span>
        <span class="custom-thumb" id="annual_sales_thumb"></span>
      </div>
      <output for="annual_sales"  class="bubble"></output>
      $<input for="annual_sales" id="annual_sales_output" class="range-track-field" value="45000000" aria-hidden="true">
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
            const annualInvoiceVolume = jQuery('#annual_invoice_volume_output').val();
            const averageSalesDso = jQuery('#average_sales_dso_output').val();
            const annualSales = jQuery('#annual_sales_output').val();
            $.ajax({
                method: 'POST',
                data: { annual_invoice_volume: annualInvoiceVolume, average_sales_dso: averageSalesDso, annual_sales: annualSales },
                url: '<?=site_url()?>/wp-json/rest/roi/calculator/supplier/',
                success: function(result) {
                  localStorage.setItem('total_annual_savings', result['data']['total_annual_savings']);
                  localStorage.setItem('total_hours_saved', result['data']['total_hours_saved']);
                    form.vals({ "Remitra_Annual_Sales__c": annualSales, "Remitra_Annual_Invoice_Volume__c": annualInvoiceVolume, "Remitra_Avg_Days_Sales_Outstanding_DSO__c": averageSalesDso, "Remitra_Annual_Hours_Saved__c": result['data']['total_hours_saved'], "Remitra_Annual_Costs_Saved__c": result['data']['total_annual_savings']});
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
initSlider(0, 100, 50, '#average_sales_dso');
initSlider(0, 90000000, 45000000, '#annual_sales');

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

$("#average_sales_dso_output").keyup(function (e){
    clearTimeout(x_timer);
    const value = parseInt($(this).val());
    x_timer = setTimeout(function(){
      const maxVal = parseInt($('#average_sales_dso').attr('max'));
      if (maxVal > value) {
        $('#average_sales_dso').val(value);
        $('#average_sales_dso').trigger('change');
      }
    }, 800);
});


$("#annual_sales_output").keyup(function (e){
    clearTimeout(x_timer);
    const value = parseInt($(this).val());
    x_timer = setTimeout(function(){
      const maxVal = parseInt($('#annual_sales').attr('max'));
      if (maxVal > value) {
        $('#annual_sales').val(value);
        $('#annual_sales').trigger('change');
      }
    }, 800);
});

});

</script>