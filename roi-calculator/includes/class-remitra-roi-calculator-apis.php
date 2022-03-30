<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://esipick.com/
 * @since      1.0.0
 *
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/includes
 */

class Remitra_Roi_Calculator_Api
{
    public $filters = [];

    public function registerApiRoutes()
    {
        add_action('rest_api_init', function () {

            register_rest_route(
                'rest', '/roi/calculator/provider/',
                array(
                    'methods' => 'POST',
                    'callback' => [$this, 'calculateRoiProvider'],
                )
            );

            register_rest_route(
                'rest', '/roi/calculator/supplier/',
                array(
                    'methods' => 'POST',
                    'callback' => [$this, 'calculateRoiSupplier'],
                )
            );
        });
    }


    public function calculateRoiProvider($request)
    {
        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200];
        //user provided values
        $annual_invoice_volume = $request['annual_invoice_volume'];
        $annual_payment_volume = $request['annual_payment_volume'];
        //pre defined values
        $cost_per_manual_invoice = 5;
        $cost_per_check_payment = 5;
        $share_of_check_payments = 0.8; //80%
        $average_time_saved_per_invoice_min = 5;
        $average_time_saved_per_payment_min = 3;
        $ap_staff_pay_rate_hourly = 20;
        $cost_per_remitra_invoice = 1;

        //calculations
        $current_invoice_cost = $annual_invoice_volume * $cost_per_manual_invoice;
        $check_payments = ($annual_payment_volume * $share_of_check_payments) * $cost_per_check_payment;
        $current_costs = $current_invoice_cost + $check_payments;

        $remitra_e_invoicing = $annual_invoice_volume * $cost_per_remitra_invoice;
        $remitra_e_payment = $annual_payment_volume * 0;
        $total_remitra_costs = $remitra_e_invoicing + $remitra_e_payment;

        $total_minutes_saved = ($average_time_saved_per_invoice_min * $annual_invoice_volume) + ($annual_payment_volume * $average_time_saved_per_payment_min);
        $total_hours_saved = $total_minutes_saved/60;
        $total_time_savings =  $total_hours_saved * $ap_staff_pay_rate_hourly;       
        
        $hard_cost_savings = $current_costs - $total_remitra_costs;
        $total_annual_savings = $total_time_savings + $hard_cost_savings;

        $_SESSION['total_hours_saved'] = round($total_hours_saved);
        $_SESSION['total_annual_savings'] = '$'.round($total_annual_savings);
        
        $response['data']['total_hours_saved'] = round($total_hours_saved);
        $response['data']['total_annual_savings'] = '$'.round($total_annual_savings);

        return $response;
    }

    public function calculateRoiSupplier($request)
    {
        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200];

        //user provided values
        $annual_sales = $request['annual_sales'];
        $average_dso = $request['average_sales_dso'];
        $annual_invoice_volume = $request['annual_invoice_volume'];

        //predefined values values
        $cost_of_capital = 0.05; //5%
        $average_time_saved_per_invoice_min = 3;
        $ar_staff_pay_rate_hourly = 20;
        $cost_per_remitra_invoice = 0.63;
        $average_dso_improvement_days = 10;

        //calculations
        $average_outstanding_value_day = $annual_sales / 365;
        $average_total_dso_value = $average_outstanding_value_day * $average_dso;

        $current_cost_of_dso = $average_total_dso_value * $cost_of_capital;

        $total_hours_saved = ($average_time_saved_per_invoice_min * $annual_invoice_volume) / 60;


        $cost_of_dso = ($average_outstanding_value_day * ($average_dso - $average_dso_improvement_days)) * $cost_of_capital;
        $invoice_cost = $annual_invoice_volume * $cost_per_remitra_invoice;


        $dso_savings = $current_cost_of_dso - $cost_of_dso;
        $time_savings =  $total_hours_saved * $ar_staff_pay_rate_hourly;
        $remitra_costs =  $invoice_cost * -1;

        $total_savings =  $dso_savings + $time_savings + $remitra_costs;

        $_SESSION['total_hours_saved'] = round($total_hours_saved);
        $_SESSION['total_annual_savings'] = '$'.round($total_savings);
        
        $response['data']['total_hours_saved'] = round($total_hours_saved);
        $response['data']['total_annual_savings'] = '$'.round($total_savings);

        return $response;
    }


}
