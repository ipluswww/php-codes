<?php
$AWS_ACCESS_KEY_ID = "AKIAJNOYC37K3MOJBRVQ";
$AWS_SECRET_ACCESS_KEY = "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";

$base_url = "http://webservices.amazon.com/onca/xml?";
$url_params = array('Operation'=>"ItemSearch",'Service'=>"AWSECommerceService",
 'AWSAccessKeyId'=>$AWS_ACCESS_KEY_ID,'AssociateTag'=>"yourtag-10",
 'Version'=>"2006-09-11",'Availability'=>"Available",'Condition'=>"All",
 'ItemPage'=>"1",'SignatureMethod' => "HmacSHA256",'ResponseGroup'=>"Images,ItemAttributes,EditorialReview",
 'Keywords'=>"Amazon");

// Add the Timestamp
$url_params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

// Sort the URL parameters
$url_parts = array();
foreach(array_keys($url_params) as $key)
    $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($url_params[$key]));
sort($url_parts);

// Construct the string to sign
$url_string = implode("&", $url_parts);

// Construct the string to sign
$string_to_sign = "GET\webservices.amazon.com\n/onca/xml\n".$url_string;


// Sign the request
$signature = hash_hmac("sha256",$string_to_sign,$AWS_SECRET_ACCESS_KEY,TRUE);

// Base64 encode the signature and make it URL safe
$signature = urlencode(base64_encode($signature));

$url = $base_url.$url_string."&Signature=".$signature;
print $url;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$xml_response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "<br/>";
echo "<br/> info = ";
var_dump($info);
echo "<br/>";
echo "<br/> response = ";
echo $xml_response;
?>