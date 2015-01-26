<?php
// $base_request_uri = "http://127.0.0.1:8080/wordpress/wc-api/v2/products/count";
$base_request_uri = "http://distribuidoragc.com/wp/wc-api/v2/products/count";

$http_method = 'GET';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// function normalize_parameters( $parameters ) {

// 	$normalized_parameters = array();

// 	foreach ( $parameters as $key => $value ) {

// 		// percent symbols (%) must be double-encoded
// 		$key   = str_replace( '%', '%25', rawurlencode( rawurldecode( $key ) ) );
// 		$value = str_replace( '%', '%25', rawurlencode( rawurldecode( $value ) ) );

// 		$normalized_parameters[ $key ] = $value;
// 	}

// 	return $normalized_parameters;
// }

// $client_signature = 'cs_1b1ea3479727527c49d2f24bdb7fea16';
$client_signature = 'cs_b354b09e5789972dbe806c8d58af3991';

$oauth_params = array(
	'data' => 'null',
	// 'oauth_consumer_key' => 'ck_f8d760447be1d24b74acba76d6386d45',
	'oauth_consumer_key' => 'ck_33d834258d4ffb4a175d1a100ad10633',
	//// 'oauth_signature' => 'cs_1b1ea3479727527c49d2f24bdb7fea16',
	'oauth_nonce' => generateRandomString(6),
	'oauth_signature_method' => 'HMAC-SHA1',
	'oauth_timestamp' => time(),
	'oauth_version' => '1.0',
	);

// normalize parameter key/values
// $params = normalize_parameters( $oauth_params );
$params = $oauth_params;

// sort parameters
if ( ! uksort( $params, 'strcmp' ) ) {
	throw new Exception( __( 'Invalid Signature - failed to sort parameters', 'woocommerce' ), 401 );
}

// form query string
$query_params = array();
foreach ( $params as $param_key => $param_value ) {

	$query_params[] = $param_key . '%3D' . $param_value; // join with equals sign
}
$query_string = implode( '%26', $query_params ); // join with ampersand

$encoded_uri = rawurlencode($base_request_uri);

$string_to_sign = $http_method . '&' . $encoded_uri . '&' . $query_string;

$hash_algorithm = strtolower( str_replace( 'HMAC-', '', $params['oauth_signature_method'] ) );

$signature = rawurlencode( base64_encode( hash_hmac( $hash_algorithm, $string_to_sign, $client_signature, true ) ) );

// echo "JODETE:  ".$signature."<br/><br/><br/>";

// $signature = substr($signature, 0, -2);

$oauth_params['oauth_signature'] = $signature;

$query_string = "";
foreach ( $oauth_params as $param_key => $param_value ) {
	$separator = "";
	if(!empty($query_string)) $separator = "&";
	$query_string .= $separator . $param_key . '=' . $param_value; // join with equals sign
}

$curl_url = $base_request_uri."?".$query_string;

// $ch = curl_init();
$ch = curl_init($curl_url);

// curl_setopt($ch, CURLOPT_URL, $base_request_uri);
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);

curl_exec($ch);
echo "<br/>";
echo "<br/>";
echo "<br/>";
// echo($signature);
curl_close($ch);