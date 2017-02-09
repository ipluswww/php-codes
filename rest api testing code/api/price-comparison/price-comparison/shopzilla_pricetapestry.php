<?php
$publisherId = "601730"; 
$apikey = "36ea984ef3a983337262e15ca5b1a299"; 
$keyword = "b";

$method = "GET";
$host = "http://catalog.bizrate.com";
$uri = "/services/catalog/v1/api/product";

$params["publisherId"] = $publisherId; 
$params["apiKey"] = $apikey;
$params["keyword"] = $keyword;
$params["format"] = 'json';

$canonicalized_query = array();
foreach ($params as $param => $value) {
	$param = str_replace("%7E", "~", rawurlencode($param));
	$value = str_replace("%7E", "~", rawurlencode($value));
	$canonicalized_query[] = $param . "=" . $value;
}
$canonicalized_query = implode("&", $canonicalized_query);

$request = $host . $uri . "?" . $canonicalized_query;
var_dump($request);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$output = curl_exec($ch); 
$info = curl_getinfo($ch);
echo "<br>";
echo "<br>";
echo "<br>";

curl_close($ch); 

//echo($output); 
$return = json_decode( $output ); 
var_dump($return);
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";

foreach($return->products->product as $product) {
	///echo $product->brand;
	if(!empty($product->brand))
	var_dump($product->brand);
	echo $product->title;
	
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
}
?>