<?php
require __DIR__ . '/../src/SimpleCurl.php';

use tzfrs\Util\SimpleCurl as SimpleCurl;

// Perform a simple get request
$curl = new SimpleCurl;
$curl->get('http://tzfrs.de');

// Perform a get request with parameters
$curl = new SimpleCurl;
$curl->get('http://tzfrs.de/', array(
    's' => 'searchterm',
));

// Setting some more options.
$curl = new SimpleCurl;
$curl->setFollowlocation()
    ->setMaxredirs(15)
    ->get('http://tzfrs.de');
if ($curl->hasError) {
    print $curl->errorNo .': '. $curl->error;
}
else {
    print $curl->response;
}