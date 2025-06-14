<?php

use PaymentAPI\Service\PaymentClient;

class WC_Gateway_Php_Api extends WC_Payment_Gateway
{
    private string $api_key;

    public function __construct()
    {
        $this->id = 'php_api_gateway';
        $this->method_title = 'PHP API Gateway';
        $this->method_description = 'Payment gateway using PHP API Client';
        $this->has_fields = true;

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->api_key = $this->get_option('api_key');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => 'Enable',
                'type' => 'checkbox',
                'label' => 'Enable PHP API Gateway',
                'default' => 'yes'
            ],
            'title' => [
                'title' => 'Title',
                'type' => 'text',
                'default' => 'Pay via PHP API'
            ],
            'api_key' => [
                'title' => 'API Key',
                'type' => 'text',
                'default' => ''
            ]
        ];
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        $order->payment_complete();
        $order->add_order_note("Payment confirmed via PHP API Gateway.");

        return [
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        ];
    }

    public function payment_fields()
    {
        if (!$this->api_key) {
            echo '<p style="color:red;">API key is missing. Contact administrator.</p>';
            return;
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        $cart_total = WC()->cart->get_total('edit');
        $amount = (float)WC()->cart->get_total('edit');
        $currency = get_woocommerce_currency();

        echo '<p>Pay securely using Custom API.</p>';

        try {
            $client = new PaymentClient($this->api_key);
            $converted = $client->getRate($currency, $amount);

            echo "<p>Total in converted rate: <strong>" . wc_price($converted) . "</strong></p>";
        } catch (Exception $e) {
            echo '<p style="color:red;">' . esc_html($e->getMessage()) . '</p>';
        }

        echo '<p><strong>Click Place Order to continue.</strong></p>';
    }
}