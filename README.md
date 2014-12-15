Curl
==========
A simple object-oriented wrapper of the PHP cURL extension.

## Install

Install via [composer](https://getcomposer.org):

```javascript
{
    "require": {
        "tzfrs/curl": "dev-master"
    }
}
```

Run `composer install`.

## Example usage

#### Perform a simple get request

```php
require 'Curl.php';
$curl = new Curl;
$curl->get('http://tzfrs.de');
```

#### Perform a get request with parameters

```php
$curl = new Curl;
$curl->get('http://tzfrs.de/', array(
    's' => 'searchterm',
));
```

#### Setting some more options.

```php
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
```