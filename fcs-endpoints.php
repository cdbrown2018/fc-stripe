<?php
add_action('rest_api_init', function () {
    register_rest_route('fc-stripe/v1', 'process_donation', array('methods' => 'POST', 'callback' => 'fcs_process_donation'));
    register_rest_route('fc-stripe/v1', 'cancel_subscription', array('methods' => 'POST', 'callback' => 'fcs_process_cancel_subscription'));
});

function fcs_process_donation($request)
{
    $isRecurring = !!rest_sanitize_boolean($request['isrecurring']);
    $amount = (float)$request['amount'];

    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);

    require 'vendor/autoload.php';
    $testMode = get_option('fcs_test_mode');
    $testKey = get_option('fcs_test_key');
    $prodKey = get_option('fcs_prod_key');

    \Stripe\Stripe::setApiKey($testMode ? $testKey : $prodKey);
    $customer = null;

    if ($isRecurring || $jsonObj->firstname != '' || $jsonObj->lastName != null || $jsonObj->email != null || $jsonObj->telephone != null || $jsonObj->city != null) {
        $customer = \Stripe\Customer::create(array(
            'name' => $jsonObj->firstname . ' ' . $jsonObj->lastname,
            'email' => $jsonObj->email ?? null,
            'phone' => $jsonObj->telephone,
            'address' => array('city' => $jsonObj->city)
        ));
    }

    $subscriptionId = null;
    if ($isRecurring == true) {

        $plan_name = strval(time());

        $price = \Stripe\Price::create(array(
            'unit_amount' => $amount,
            'currency' => 'usd',
            'recurring' => ['interval' => 'month'],
            'product_data' => array(
                'name' => 'Donation ' . $plan_name,
                'statement_descriptor' => 'NBFY Donation'
            )
        ));

        $nextCharge = strtotime('+30 days', time());
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => $price->id]
            ],
            'trial_end' => $nextCharge
        ]);
        $subscriptionId = $subscription->id;
    }

    header('Content-Type: application/json');

    try {

        // retrieve JSON from POST body

        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'customer' => $customer != null ? $customer->id : null,
            'amount' => $amount,
            'currency' => 'usd',
            'statement_descriptor' => 'NBFY Donations',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'setup_future_usage' => $isRecurring ? 'off_session' : 'on_session'
        ]);

        $output = [
            'clientSecret' => $paymentIntent->client_secret,
            'subscriptionId' => $subscriptionId
        ];

        echo json_encode($output);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getTraceAsString()]);
    }
}

function fcs_process_cancel_subscription($request)
{
    $subscriptionId = $request['subscriptionId'];
    if ($subscriptionId == null || $subscriptionId == '') return;

    require 'vendor/autoload.php';
    $testMode = get_option('fcs_test_mode');
    $testKey = get_option('fcs_test_key');
    $prodKey = get_option('fcs_prod_key');

    try {
    $stripe = new \Stripe\StripeClient($testMode ? $testKey : $prodKey);

    $stripe->subscriptions->cancel($subscriptionId);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getTraceAsString()]);
    }
}
