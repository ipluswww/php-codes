<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ChangeLocale;

//====================================amazon functions===============================
function getAmazonCurl($region, $category, $keyword) {
 
	$data = array(
		"Operation" => "ItemLookup",
		"IncludeReviewsSummary" => False,
		"ItemId" => "B00KQPGRRE",
		"ResponseGroup" => "Medium,OfferSummary",
	);
	
	if(!empty($keyword))
	{
		$keywords = $keyword; 
		$data = $data + compact('keywords');
	}
	
	if(!($category == 'all'))
	{
		$data = $data + compact('category');
	}
	
	$xml = aws_signed_request($region, $data);	
	
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
//====================================prosperent functions===============================
function getProsperentCurl() {
	$apikey = "274ab56313562fc993a85d25a957ae8e"; 

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "http://api.prosperent.com/api/search "); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST, true); 
	$data = array( 
			'api_key' => $apikey, 
			'imageSize' => '250x250',
			'limit' => '20', 

			
	); 
	
	if(!empty($keyword))
	{
		$query = $keyword; 
		$data = $data + compact('query');
	}
	
	if(!($category == 'all'))
	{
		$filterCategory = $category; 
		$data = $data + compact('filterCategory');
	}
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
			
}
//====================================amazon functions===============================
//====================================amazon functions===============================
//====================================amazon functions===============================

	public function index()
	{
			$keyword = "a";
			$category = "computer & tablets";
			$curl = array();
			//=============================prosperent============================
			$curl["prosperent"] = getProsperentCurl($category, $keyword);
			//=============================amazon============================
			$curl['amazon'] = getAmazonCurl("com", $category, $keyword);
			//=============================shopzilla============================
			$curl['shopzilla'] = getShopzillaCurl($category, $keyword);
			
			//=============================amazon============================
			//=============================amazon============================
			
			
			$mh = curl_multi_init();

			//add the two handles
			curl_multi_add_handle($mh,$ch);
			curl_multi_add_handle($mh,$ch1);

			$running=null;
			
			//execute the handles
			do {
				curl_multi_exec($mh,$running);
			} while($running > 0);
			
			$output = curl_multi_getcontent($ch);
			$return = json_decode( $output ); 
			$product = $return->data[0];
			
			$output = curl_multi_getcontent($ch1);
			$return = json_decode( $output ); 
			$product_overallrating = 5;
			
					
			curl_multi_remove_handle($mh, $ch);
			curl_multi_remove_handle($mh, $ch1);
			curl_multi_close($mh);
			
			$output = curl_exec($ch); 
			
			curl_close($ch); 

			$return = json_decode( $output ); 
			$product_list = $return->data;
			$totalRecords = $return->totalRecords;
			return view('front.product_landing', compact('category', 'keyword', 'product_list', 'totalRecords'));
		}
	}

	index();
