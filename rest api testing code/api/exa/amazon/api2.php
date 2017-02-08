<?php

//Enter your IDs
define("Access_Key_ID", "AKIAIN4M6RVQ4ATKLGGA");
define ("AWS_ACCESS_KEY_ID", "AKIAJ23QQRRPSRVHFTVA");
define ("AWS_SECRET_ACCESS_KEY","hYw+oa9NBQVbHe/PiuRfhu8bWnkPAxfxslXSfzjP");
define("Associate_tag", "computer");

//Set up the operation in the request
function ItemSearch($SearchIndex, $Keywords){

//Set the values for some of the parameters
$Operation = "ItemSearch";
$Version = "2013-08-01";
$ResponseGroup = "ItemAttributes,Offers";
//User interface provides values
//for $SearchIndex and $Keywords

//Define the request
$request=
     "http://webservices.amazon.com/onca/xml"
   . "?Service=AWSECommerceService"
   . "&AssociateTag=" . Associate_tag
   . "&AWSAccessKeyId=" . Access_Key_ID
   . "&Operation=" . $Operation
   . "&Version=" . $Version
   . "&SearchIndex=" . $SearchIndex
   . "&Keywords=" . $Keywords
   . "&Signature=" . "2016"
   . "&ResponseGroup=" . $ResponseGroup;

//Catch the response in the $response object
$response = file_get_contents($request);
$parsed_xml = simplexml_load_string($response);
printSearchResults($parsed_xml, $SearchIndex);
}

ItemSearch("0679722769", "i");
?>