<?php
// instantiate the OAuth object
// OAUTH_CONSUMER_KEY and OAUTH_CONSUMER_SECRET are constants holding your key and secret
// and are always used when instantiating the OAuth object 
$oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);

// make an API request for your temporary credentials
$req_token = $oauth->getRequestToken("https://openapi.etsy.com/v2/oauth/request_token?scope=email_r%20listings_r", 'oob');

print $req_token['login_url']."\n";
?>
