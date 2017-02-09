<?php
require_once("oauth/OAuth.php");

// Read user input for consumer key/secret, service endpoint, http method, post data, etc.
$http_method = @$_REQUEST['http_method'];
$endpoint = @$_REQUEST['endpoint'];
$consumer_key = @$_REQUEST['key'];
$consumer_secret = @$_REQUEST['secret'];
$action = @$_REQUEST['action'];
$postdata = null;

if($http_method == "PUT" || $http_method == "POST"){
  $postdata = trim(@$_REQUEST['postdata'], "\r\n ");
}

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

// Send (or dump) signed OAuth request to the BCWS server
if ($action == "sign_dump") {
    Header("Content-Type: text/plain");
    print_r($http_request);
    exit(0);
} else if ($action == "sign_send") {
    $fp = fsockopen ($host, $port, $errno, $errstr); 
    if($fp){
        fwrite($fp, $http_request);
        read_response($fp);
        fclose($fp);
    } else {
        print "Fatal error\n";
    }
    exit(0);
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
    }
  } 

  $http_req .= "\r\n";
  return $http_req;
}

function read_response(&$fp){

  $chunked = false;
  $content_length = 0;

  read_response_headers($fp, $content_length, $chunked);

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

    header($header_line);
  }
}

function read_regular_body(&$fp, $content_length){

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
        print($buffer);
        break;
      }
    }
    print($buffer);
  }
}

?>

<html>
<head>

  <title>2-Legged OAuth Test Client</title>

  <script type="text/javascript">

    function showhide_postdata(ddlHttpMethod){
      var method = ddlHttpMethod.options[ddlHttpMethod.options.selectedIndex].value;
      if(method == "POST" || method == "PUT"){
        document.getElementById("divPostData").style.display = "block";
      } else {
        document.getElementById("divPostData").style.display = "none";
      }
    }    

  </script>

</head>

<body>

<h1>2-Legged OAuth Test Client</h1>
<form method="POST" name="oauth_client">
<h3>Enter The Endpoint to Test</h3>
endpoint: <input type="text" name="endpoint" value="<?php echo $endpoint; ?>" size="100"/><br />

<h3>Select HTTP Method for the request</h3>
  HTTP Method: 
  <select name="http_method" onchange="showhide_postdata(this);">
    <option value="GET" selected>GET</option>
    <option value="POST">POST</option>
    <option value="PUT">PUT</option>
  </select>
<br />

<div name="divPostData" id="divPostData" style="display:none;">
  <h3>Enter Put/Post data for the request</h3>
  <TextArea name="postdata" width="600px" height="200px"></TextArea>
</div>

<h3>Enter Your Consumer Key / Secret</h3>
consumer key: <input type="text" name="key" value="<?php echo $key; ?>" /><br />
consumer secret: <input type="text" name="secret" value="<?php echo $secret;?>" /><br />

Dump Signed Request <input type="submit" name="action" value="sign_dump" /><br />
Send Signed Request <input type="submit" name="action" value="sign_send" /><br />

</body>
</html>
