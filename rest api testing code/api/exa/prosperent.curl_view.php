<?php
$filterCategory = "computers & accessories"; 
$imageSize = "311x100";
$api_key = "274ab56313562fc993a85d25a957ae8e"; 
$access_key = "274ab56313562fc993a85d25a957ae8e"; 

$urls = array(
   "http://api.prosperent.com/api/search",
   "http://api.prosperent.com/api/redirect",
   "http://api.prosperent.com/api/merchant",
   "http://api.prosperent.com/api/brand",
   "http://api.prosperent.com/api/trends",
   "http://api.prosperent.com/api/commissions",
   "http://api.prosperent.com/api/transactions",
   "http://api.prosperent.com/api/payments",
   "http://api.prosperent.com/api/clicks",
);

$mh = curl_multi_init();

foreach ($urls as $i => $url) {
    $conn[$i] = curl_init($url);
    curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
    curl_multi_add_handle($mh, $conn[$i]);
	
	curl_setopt($conn[$i], CURLOPT_POST, true); 
	$data = array( 
			'api_key' => $api_key,  
			'accessKey' => $access_key,  
			'filterCatalogId'   => "a6cd5372c0c6af2867359dbc35300e9e", 
			'limit' => '10', );
	curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $data); 

}

do {
    $status = curl_multi_exec($mh, $active);
    $info = curl_multi_info_read($mh);
    if (false !== $info) {
        var_dump($info);
    }
} while ($status === CURLM_CALL_MULTI_PERFORM || $active);

foreach ($urls as $i => $url) {
    $res[$i] = curl_multi_getcontent($conn[$i]);
	echo $urls[$i];
	echo "<br><br><br>";
    var_dump($res[$i]);
	echo "<br><br><br>";
    curl_close($conn[$i]);
}

//var_dump(curl_multi_info_read($mh));
?>