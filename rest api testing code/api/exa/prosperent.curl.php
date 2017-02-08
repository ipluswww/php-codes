<?php
echo "step1";

$ch = curl_init(); 
echo "step2";
curl_setopt($ch, CURLOPT_URL, "http://api.prosperent.com/api/merchant"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POST, true); 
echo "step5";
$data = array( 
    'limit' => '10000', 
); 



curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
echo "start";
$output = curl_exec($ch);
$info = curl_getinfo($ch); 
curl_close($ch); 
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br> info=";
var_dump($info);
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br> return=";
        $return = json_decode( $output ); 
//var_dump($return);
var_dump($return->data[0]);
foreach ( $return->data as $trend ) 
{ 

    $mer = $trend->merchant; 
    $logo = $trend->logoUrl; 
     

}  
?>