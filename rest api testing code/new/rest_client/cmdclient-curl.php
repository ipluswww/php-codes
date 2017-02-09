<?php
require_once("oauth/OAuth.php");

$consumer_key = null;
$consumer_secret = null;

$http_method = null;
$endpoint = null;
$postdata = null;

$user_sign_method = null;
$action = null;

read_input_params($argc, $argv, $http_method, $endpoint, $consumer_key, $consumer_secret, $user_sign_method, $postdata, $action);

// Establish an OAuth Consumer based on read credentials
$consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);

// Setup OAuth request
$request = OAuthRequest::from_consumer_and_token($consumer, NULL, $http_method, $endpoint, NULL);

//Sign the constructed OAuth request using HMAC-SHA1  
$sign_method = new OAuthSignatureMethod_HMAC_SHA1();
$request->sign_request($sign_method, $consumer, NULL);

// Make signed OAuth request to the BCWS server 
echo send_request($request->get_normalized_http_method(), $endpoint, $request->to_header(), $postdata);  


/* ********************************************** FUNCTIONS ****************************************** */

function send_request($http_method, $endpoint, $auth_header=null, $postdata=null) {

  if( ($http_method == 'PUT' || $http_method == 'POST') ){
      if( is_null($postdata) ){
        print("Error: post data not set for PUT/POST method.\r\n");
        print_usage();
        exit(1);
      }
  } 

  $curl = curl_init($endpoint);  
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
  curl_setopt($curl, CURLOPT_FAILONERROR, false);  
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
  //curl_setopt($curl, CURLOPT_VERBOSE, true);  
  
  switch($http_method) {  
    case 'GET':  
      if ($auth_header) {  
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));   
      }  
      break;  
    case 'POST':  
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', $auth_header));   
      curl_setopt($curl, CURLOPT_POST, 1);                                         
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);  
      break;  
    case 'PUT':  
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', $auth_header));   
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);  
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);  
      break;  
    case 'DELETE':  
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));   
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);   
      break;  
  }
  
  $response = curl_exec($curl);  
  if (!$response) {  
    $response = curl_error($curl);  
  }  
  
  curl_close($curl);  
  return $response;  
}  

function read_input_params($argc, $argv, &$http_method, &$endpoint, &$key, &$secret, &$user_sign_method, &$postdata, &$action) {

  $http_method = "GET";
  $action = "send";

  $i = 1;

  if($argc <= 1){
    print_usage();
    exit(1);
  }

  while($i < $argc){

    if(strlen($argv[$i]) > 3 || substr($argv[$i], 0, 1) != "-"){
      print_usage();
      exit(1);
    }

    $option = substr($argv[$i], 1, 1);
    switch($option){
      case 'k':
        $i++;
        $key = $argv[$i++];
        break;
      case 's':
        $i++;
        $secret = $argv[$i++];
        break;
      case 'u':
        $i++;
        $endpoint = $argv[$i++];
        break;
      case 'm':
        $i++;
        $http_method = $argv[$i++];
        if($http_method != 'GET' && $http_method != 'POST' && $http_method != 'PUT'){
          print("Unsupported Http method. Allowed methods are GET, POST and PUT.\r\n");
          print_usage();
          exit(1);
        }
        break;
      case 'p':
        $source = substr($argv[$i], 2, 1);
        $i++;
        if($source == "d"){
          $postdata = $argv[$i];
        } else if($source == "f"){
          $postdata_file = $argv[$i];
          if(file_exists($postdata_file)){
            $postdata = file_get_contents($postdata_file);
          } else{
            print("File $postdata_file doesnot exist. No post data is available.\r\n");
            print_usage();
            exit(1);
          }
        } else {
          print("Invalid post data source.\r\n");
          print_usage();
          exit(1);
        }
        $i++;
        break;
      case 'o':
        $method_id = substr($argv[$i], 2, 1);
        if($method_id == "h"){
          $user_sign_method = "HMAC-SHA1";
        } else if($method_id == "p"){
          $user_sign_method = "PLAINTEXT";
        } else if($method_id == "r"){
          print('RSA-SHA1 method is not yet supported.');
          exit(1);
        } else {
          print("Invalid signature method option.\r\n");
          print_usage();
          exit(1);
        }
        $i++;
        break;
      case 'd':
        $action = "dump";
        $i++;
        break;
      default:
        print_usage();
        exit(1);
    }
  }

  $missing = false;
  if(is_null($key)){
    print ("\r\nError: missing consumer key\r\n");
    $missing = true;
  }

  if(is_null($secret)){
    print ("\r\nError: missing consumer secret\r\n");
    $missing = true;
  }

  if(is_null($endpoint)){
    print ("\r\nError: missing endpoint url\r\n\r\n");
    $missing = true;
  }

  if($missing == true){
    print_usage();
    exit(1);
  }
}

function print_usage(){

  print("\r\nUsage: php cmdclient.php [parameters...]\r\n\r\n");
  print("Mandatory parameters:\r\n\r\n");
  print("  -k    <consumer key>        Consumer Key\r\n");
  print("  -s    <consumer secret>     Consumer Secret\r\n");
  print("  -u    <endpoint url>        Url of REST resource\r\n\r\n");
  print("Optional parameters:\r\n\r\n");
  print("  -m                          Http Method - GET or POST or PUT. Default is GET\r\n");
  print("  -pd                         Post data (for POST/PUT http methods)\r\n");
  print("  -pf                         Name of the file containing post data\r\n");
  print("                              (for POST/PUT http methods)\r\n");
  print("  -op                         Use OAuth PLAINTEXT method for signing request\r\n");
  print("  -oh                         Use OAuth HMAC-SHA1 method for signing request\r\n");
  print("  -or                         Use OAuth RSA-SHA1 method for signing request\r\n");
  print("  -d                          Don't send request, dump it to console\r\n");
}

?>