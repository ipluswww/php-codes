<?php
$filterCategory = "computers & accessories"; 
$api_key = "da544137d351b5f9308d321496f17c73"; 

$urls = array(
   "https://embedkit.com/api/v1/embed/",
);

$mh = curl_multi_init();

foreach ($urls as $i => $url) {
    $conn[$i] = curl_init($url);
    curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
    curl_multi_add_handle($mh, $conn[$i]);
	
	curl_setopt($conn[$i], CURLOPT_POST, true); 
	$data = array( 
			'api_key' => $api_key,  
			'url' => "https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DdQw4w9WgXcQ",
			);  
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