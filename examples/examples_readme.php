<?php
require __DIR__ . '/../src/Curl.php';

use tzfrs\Util\Curl as Curl;

// Perform a simple get request
$curl = new Curl;
$curl->get('http://tzfrs.de');

// Perform a get request with parameters
$curl = new Curl;
$curl->get('http://tzfrs.de/', array(
    's' => 'searchterm',
));

// Setting some more options.
$curl = new Curl;
$curl->setFollowlocation()
    ->setMaxredirs(15)
    ->get('http://tzfrs.de');
if ($curl->hasError) {
    print $curl->errorNo .': '. $curl->error;
}
else {
    print $curl->response;
}