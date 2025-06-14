<?php
/**
 * Plugin Name: PHP API WooCommerce Gateway
 * Description: Custom payment gateway that uses PHP API Client to calculate rate.
 * Version: 1.0
 * Author: Makso
 */

add_action('plugins_loaded', 'php_api_gateway_init', 11);

function php_api_gateway_init() {
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>PHP API Gateway</strong> requires WooCommerce to be installed and active.</p></div>';
        });
        return;
    }

    require_once plugin_dir_path(__FILE__) . 'includes/class-php-api-gateway.php';

    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Gateway_Php_Api';
        error_log('PHP API Gateway registered in woocommerce_payment_gateways');
        return $gateways;
    });
}
