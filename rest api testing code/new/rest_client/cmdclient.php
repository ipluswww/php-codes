<?php
require_once("oauth/OAuth.php");

$consumer_key = null;
$consumer_secret = null;

$http_method = null;
$endpoint = null;
$postdata = null;

$user_sign_method = null;
$action = null;

// Read user input for consumer key/secret, service endpoint, http method, signature method, post data, etc.
read_input_params($argc, $argv, $http_method, $endpoint, $consumer_key, $consumer_secret, $user_sign_method, $postdata, $action);

// Establish an OAuth Consumer based on read credentials
$consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);

// Setup OAuth request
$oauth_request = OAuthRequest::from_consumer_and_token($consumer, NULL, $http_method, $endpoint, NULL);

//Sign the constructed OAuth request using HMAC-SHA1  
$sign_method = new OAuthSignatureMethod_HMAC_SHA1();
$oauth_request->sign_request($sign_method, $consumer, NULL);
$oauth_header = $oauth_request->to_header();

// Break-up service endpoint into various URL components to be used for sending request to server
$parts = parse_url($endpoint);

$scheme = $parts['scheme'];
$host = $parts['host'];
$port = @$parts['port'];
$port or $port = ($scheme == 'https') ? '443' : '80';
$path = @$parts['path'];

// Generate signed OAuth request for the BCWS server
$http_request = generate_request($http_method, $scheme, $host, $port, $path, $oauth_header, $postdata, "1.0");

print("\r\n");

// Send (or dump) signed OAuth request to the BCWS server
if ($action == "dump") {
    print_r($http_request);
} else if ($action == "send") {
    $fp = fsockopen ($host, $port, $errno, $errstr); 
    if($fp){
        fwrite($fp, $http_request);
        read_response($fp);
        fclose($fp);
    } else {
        print "Fatal error\n";
    }
}

/* ********************************************** FUNCTIONS ****************************************** */

function generate_request($http_method, $scheme, $host, $port, $path, $oauth_header, $postdata = NULL, $http_version = "1.0"){

  $http_req = strtoupper($http_method). " $path ". strtoupper($scheme). "/$http_version\r\n";
  $http_req .= "HOST: $host:$port\r\n";

  if($http_version == "1.0") {
    $http_req .= "Connection: close\r\n";
  } else if($http_version == "1.1") {
    $http_req .= "Connection: Keep-Alive\r\n";
  }

  $http_req .= "$oauth_header\r\n";

  if(($http_method == 'PUT' || $http_method == 'POST')){
    if(!is_null($postdata)){
      $http_req .= "Content-Type: text/xml\r\n";
      $http_req .= "Content-Length: ". strlen($postdata). "\r\n";
      $http_req .= "\r\n";
      $http_req .= $postdata;
    } else{
      print("Error: post data not set for PUT/POST method.\r\n");
      print_usage();
      exit(1);
    }
  } 

  $http_req .= "\r\n";
  return $http_req;
}

function read_response(&$fp){

  $chunked = false;
  $content_length = 0;
  read_response_headers($fp, $content_length, $chunked);

  //print("chunked: ". ($chunked ? "true" : "false"). "\r\n");
  //print("content length: ". $content_length. "\r\n");

  if(!$chunked){
    read_regular_body($fp, $content_length);
  } else{
    read_chunked_body($fp, $content_length);
  }
}

function read_response_headers(&$fp, &$content_length, &$chunked){

  $HEADER_CONTENT_LENGTH = "Content-Length: ";
  $HEADER_TRANSFER_ENCODING = "Transfer-Encoding: chunked";
  
  $content_length = 0;
  $chunked = false;

  while (!feof($fp)) {

    $header_line = fgets($fp);
    print($header_line);

    $trimmed_header_line = trim($header_line, "\r\n ");

    if($content_length == 0 && stripos($trimmed_header_line, $HEADER_CONTENT_LENGTH) === 0){
      $content_length = substr($trimmed_header_line, strlen($HEADER_CONTENT_LENGTH));
    }

    if(!$chunked && strcasecmp($trimmed_header_line, $HEADER_TRANSFER_ENCODING) == 0){
      $chunked = true;
    }

    if(strlen($trimmed_header_line) == 0){
      break;
    }
  }
}

function read_regular_body(&$fp, $content_length){

  print("\r\nRegular Response\r\n");

  $curr_len = 0;
  while (!feof($fp)) {
    if($content_length > 0){
      $buffer = fread($fp, $content_length - $curr_len);
      print($buffer);
      $curr_len += strlen($buffer);
      if($curr_len >= $content_length){
        break;
      }
    } else {
      $buffer = fgets($fp);
      print($buffer);
    }
  }
}

function read_chunked_body(&$fp, $content_length){

  print("\r\nChunked Respone\r\n");

  $last_chunk = false;

  while (!feof($fp)) {

    $buffer = fgets($fp);
    $trimmed_buffer = trim($buffer, "\r\n ");

    if(is_numeric("0x". $trimmed_buffer)) {
      if($trimmed_buffer == '0') {
        $last_chunk = true;
      }
    } else {
      if($trimmed_buffer == '' && $last_chunk == true) {
        break;
      } else {
        print($trimmed_buffer);
      }
    }
  }
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