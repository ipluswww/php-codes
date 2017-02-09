<?php
 
// Region code and Product ASIN
$response = getAmazonPrice("com", "715050648118");
 
function getAmazonPrice($region, $asin) {
 
	$xml = aws_signed_request($region, array(
		"Operation" => "ItemLookup",
		"ItemId" => $asin,
		"IdType" => "UPC" ,
		"SearchIndex" => "All",
		"IncludeReviewsSummary" => False,
		"ResponseGroup" => "Medium,OfferSummary,Accessories,Images",
	));
	/*$xml = aws_signed_request($region, array(
		"Operation" => "ItemLookup",
		"ItemId" => $asin,
		"IncludeReviewsSummary" => False,
		"ResponseGroup" => "Medium,OfferSummary",
	));*/
 
	$item = $xml->Items->Item;
	$title = htmlentities((string) $item->ItemAttributes->Title);
	$url = htmlentities((string) $item->DetailPageURL);
	$image = htmlentities((string) $item->MediumImage->URL);
	$price = htmlentities((string) $item->OfferSummary->LowestNewPrice->Amount);
	$code = htmlentities((string) $item->OfferSummary->LowestNewPrice->CurrencyCode);
	$qty = htmlentities((string) $item->OfferSummary->TotalNew);
 
	if ($qty !== "0") {
		$response = array(
			"code" => $code,
			"price" => number_format((float) ($price / 100), 2, '.', ''),
			"image" => $image,
			"url" => $url,
			"title" => $title
		);
	}
 
	return $response;
}
 
function getPage($url) {
 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$url);
	/*curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	*/
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 15);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$html = curl_exec($curl);
	$info = curl_getinfo($curl);
	curl_close($curl);
	return $html;
}
 
function aws_signed_request($region, $params) {
 
	$public_key = "AKIAIQNXANCW6XFR55BA";
	$private_key = "Z5unWeON8270VwlmTaROz1XpnQLO/j/IqiTsL36K";
	
	$method = "GET";
	$host = "webservices.amazon." . $region;
	$uri = "/onca/xml";
 
	$params["Service"] = "AWSECommerceService";
	$params["AssociateTag"] = "affiliate-20"; // Put your Affiliate Code here
	$params["AWSAccessKeyId"] = $public_key;
	$params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	$params["Version"] = "2015-05-26";
 
	ksort($params);
	$canonicalized_query = array();
	foreach ($params as $param => $value) {
		$param = str_replace("%7E", "~", rawurlencode($param));
		$value = str_replace("%7E", "~", rawurlencode($value));
		$canonicalized_query[] = $param . "=" . $value;
	}
	$canonicalized_query = implode("&", $canonicalized_query);
 
	$string_to_sign = $method . "\n" . $host . "\n" . $uri . "\n" . $canonicalized_query;
	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));
	$signature = str_replace("%7E", "~", rawurlencode($signature));
 
	$request = "http://" . $host . $uri . "?" . $canonicalized_query . "&Signature=" . $signature;
	$response = getPage($request);
	echo "<br>";
	echo "<br>";
	echo "<br> response=";
	
	var_dump($response);
 
	$pxml = @simplexml_load_string($response);
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br> pxml=";
	
	var_dump($pxml);
 
	if ($pxml === False) {
		return False;// no xml
	} else {
		return $pxml;
	}
}
 
?>