<?php
require 'SimpleCurl.php';
$curl = new SimpleCurl();
$curl->setFollowlocation()
    ->setMaxredirs(15)
    ->get('http://tzfrs.de');
if ($curl->hasError) {
    print $curl->errorNo .': '. $curl->error;
}
else {
    print $curl->response;
}