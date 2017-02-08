<?php

$email    = 'review@a2z-reviews.com';
$password = 'review22';

// initial login page which redirects to correct sign in page, sets some cookies
$URL = 'https://affiliate-program.amazon.com/gp/associates/join/landing/main.html';

$ch  = curl_init();

curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'amazoncookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'amazoncookie.txt');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR,  fopen('php://stdout', 'w'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

$page = curl_exec($ch);

//var_dump($page);exit;

// try to find the actual login form
if (!preg_match('/<form name="sign_in".*?<\/form>/is', $page, $form)) {
    die('Failed to find log in form!');
}

$form = $form[0];

// find the action of the login form
if (!preg_match('/action=(?:\'|")?([^\s\'">]+)/i', $form, $action)) {
    die('Failed to find login form url');
}

$URL2 = $action[1]; // this is our new post url

// find all hidden fields which we need to send with our login, this includes security tokens
$count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

$postFields = array();

// turn the hidden fields into an array
for ($i = 0; $i < $count; ++$i) {
    $postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
}

// add our login values
$postFields['username'] = $email;
$postFields['password'] = $password;

$post = '';

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
foreach($postFields as $key => $value) {
    $post .= $key . '=' . urlencode($value) . '&';
}

$post = substr($post, 0, -1);

// set additional curl options using our previous options
curl_setopt($ch, CURLOPT_URL, $URL2);
curl_setopt($ch, CURLOPT_REFERER, $URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$page = curl_exec($ch); // make request

var_dump($page); // should be logged in