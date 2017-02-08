<?php 
define ("AWS_ACCESS_KEY_ID", "AKIAJ23QQRRPSRVHFTVA");
define ("AWS_SECRET_ACCESS_KEY","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");

$base_url = "https://mws.amazonservices.fr/Products/2011-10-01";
$method = "GET";
$host = "mws.amazonservices.fr";
$uri = "/Products/2011-10-01";

function amazon_xml($searchTerm) {

$params = array(
'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
'Action' => "GetLowestOfferListingsForSKU",
'SignatureMethod' => "HmacSHA256",
'SignatureVersion' => "2",
'Timestamp'=> date("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
'Version'=> "2011-10-01",
'Query' => $searchTerm,
'ItemCondition'=> "New",
'ExcludeMe' => "false");


// Sort the URL parameters
$url_parts = array();
foreach(array_keys($params) as $key)
$url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
sort($url_parts);

// Construct the string to sign
$url_string = str_replace("%7E", "~", implode("&", $url_parts));
$string_to_sign = "POST\nmws.amazonservices.fr\n/Products/2011-10-01\n" . $url_string;

// Sign the request
$signature = hash_hmac('sha256', $string_to_sign, AWS_SECRET_ACCESS_KEY, TRUE);

// Base64 encode the signature and make it URL safe
$signature = rawurlencode(base64_encode($signature));

$url = "https://mws.amazonservices.fr/Products/2011-10-01" . '?' . $url_string . "&Signature=" . $signature;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$response = curl_exec($ch);
$info	= curl_getinfo($ch);
var_dump($info);
echo "<br><br>";
var_dump($response);
$parsed_xml = simplexml_load_string($response);

return ($parsed_xml);
}

amazon_xml("c");
?>