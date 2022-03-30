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
  <p><strong>Total Hours Saved:</strong> <span id="total-hours-saved">0</span></p>
  <p><strong>Total Annual Savings:</strong> <span id="total-annual-savings">$0</span></p>
</div>

<script>
    const totalAnnualSavings = localStorage.getItem('total_annual_savings');
    const totalHoursSaved = localStorage.getItem('total_hours_saved');
    if (totalAnnualSavings && totalHoursSaved) {
      document.getElementById('total-hours-saved').innerHTML = parseFloat(totalHoursSaved).toFixed(0);
      document.getElementById('total-annual-savings').innerHTML = totalAnnualSavings;
    }

</script>