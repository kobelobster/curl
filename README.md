SimpleCurl
==========

A simple object-oriented wrapper of the PHP cURL extension.

### Example usage

##### Perform a simple get request

```php
require 'SimpleCurl.php';
$curl = new SimpleCurl();
$curl->get('http://tzfrs.de');
```

##### Perform a get request with parameters

```php
$curl = new SimpleCurl();
$curl->get('http://tzfrs.de/', array(
    's' => 'searchterm',
));
```

##### Setting some more options.

```php
$curl = new SimpleCurl();
$curl->setUserAgent($userAgent)
    ->setReferrer($referer')
    ->get('http://tzfrs.de');
if ($curl->hasError()) {
    print $curl->errorNo .': '. $curl->error;
}
else {
    print $curl->response;
}
```