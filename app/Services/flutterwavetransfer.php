<?php
// Initialize cURL
$curl = curl_init();
// Generate a unique reference
$reference = uniqid('transfer-', true);
$reference = str_replace('.', '-', $reference); // Replace any dots with dashes
// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.flutterwave.com/v3/transfers",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        "account_number" => "1234567890",
        "account_bank" => "044",
        "amount" => 1000,
        "currency" => "NGN",
        "narration" => "Transfer from Bloom Ads",
        "reference" => $reference, // Use the unique reference here
        "callback_url" => "https://your-callback-url.com"
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer FLWSECK_TEST-75d7f0c4ee13b04ed37ffd835cfcd9ce-X",
        "Content-Type: application/json"
    ],
    CURLOPT_SSL_VERIFYPEER => false, // Disable SSL peer verification
    CURLOPT_SSL_VERIFYHOST => false, // Disable SSL host verification
]);

// Execute the request
$response = curl_exec($curl);

// Handle errors
$err = curl_error($curl);

// Close the cURL session
curl_close($curl);

// Output the result
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}