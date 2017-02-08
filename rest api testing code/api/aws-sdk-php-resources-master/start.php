<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/Resource.php";
require_once __DIR__ . "/src/Aws.php";

// The sample code below demonstrates how Resource APIs work


$aws = new Aws\Resource\Aws($config);

// Get references to resource objects



$bucket = $aws->s3->bucket('my-bucket');

$object = $bucket->object('image/bird.jpg');

// Access resource attributes


echo $object['LastModified'];

// Call resource methods to take action


$object->delete();

$bucket->delete();


?>