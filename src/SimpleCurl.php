<?php
namespace tzfrs\Util;
/**
 * SimpleCurl
 * @author Theo Tzaferis
 *
 * This class is used to handle curl operations.
 */
class SimpleCurl
{
    /**
     * Variable used for the cURL object creation
     * @var resource $curl
     */
    private $curl;
    /**
     * The response from the curl request gets saved into this variable
     * @var mixed $curlResponse
     */
    public $response;
    /**
     * The curl information after a request are getting saved into this variable
     * @var array $curlInfo
     */
    public $info;
    /**
     * The string containing the last error for the current session
     * @var string $curlError
     */
    public $error;

    /**
     * Is set to true, when there was an error and the cURL-Response is false
     * @var bool $hasError
     */
    public $hasError;

    /**
     * The number of the curlError
     * @var int $curlErrorNo
     */
    public $errorNo;

    /**
     * Contains HTTP Response Header if requested
     * @var string $responseHeader
     */
    public $responseHeader;

    /**
     * The constructor of this class sets some basic options to the curl request
     * such as returntransfer to true (default), meaning the result won't get printed out
     * when executing the curl request but getting saved into a variable
     *
     * @param boolean $returnTransfer determines whether the result should
     */
    public function __construct($returnTransfer = true)
    {
        $this->curl = curl_init();
        $this->setOpt(CURLOPT_RETURNTRANSFER, $returnTransfer);
        $this->setOpt(CURLOPT_FOLLOWLOCATION, true);
    }

    /**
     * Performing a getRequest to the given URL
     *
     * @param $url string $url The URL to send the request to
     * @param null|array $data The data that should be appeneded as get parameters to the url
     * @param bool $withUrlEncoding
     * @return array
     */
    public function get($url, $data = null, $withUrlEncoding = false)
    {
        if ($withUrlEncoding) {
            $url = $this->curlEscape($url);
        }
        if ($data !== null) {
            $url .= '?'. http_build_query($data, null, '&');
        }
        return $this->setOpt(CURLOPT_URL, $url)
            ->exec();
    }

    /**
     * Performing a post request to the given url
     * Sets CURLOPT_POST to true and sets the postFields
     *
     * This POST is the normal application/x-www-form-urlencoded kind, most commonly used by HTML forms.
     * @param $url
     * @param null $data
     * @param bool $withUrlEncoding
     * @return array
     */
    public function post($url, $data = null, $withUrlEncoding = false)
    {
        if ($withUrlEncoding) {
            $url = $this->curlEscape($url);
        }
        $this->setOpt(CURLOPT_URL, $url)
            ->setOpt(CURLOPT_POST, true);

        if ($data !== null) {
            $this->setOpt(CURLOPT_POSTFIELDS, $data);
        }
        return $this->exec();
    }

    /**
     * Performs a PUT Request
     *
     * true to HTTP PUT a file. The file to PUT must be set with CURLOPT_INFILE and CURLOPT_INFILESIZE.
     * @param $url
     * @param null $data
     * @param bool $withUrlEncoding
     * @return array
     */
    public function put($url, $data = null, $withUrlEncoding = false)
    {
        if ($withUrlEncoding) {
            $url = $this->curlEscape($url);
        }
        $this->setOpt(CURLOPT_URL, $url)
            ->setOpt(CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data!==null) {
            $this->setOpt(CURLOPT_POSTFIELDS, $data);
        }
        return $this->exec();
    }

    public function putWithFile($url, $inFile, $inFilesize, $returnResult = true, $withUrlEncoding = false)
    {
        if ($withUrlEncoding) {
            $url = $this->curlEscape($url);
        }
        return $this->setOpt(CURLOPT_URL, $url)
            ->setOpt(CURLOPT_PUT, true)
            ->setOpt(CURLOPT_INFILE, $inFile)
            ->setOpt(CURLOPT_INFILESIZE, $inFilesize)
            ->exec($returnResult);
    }

    /**
     * Performs a delete request
     * @param $url
     * @param bool $withUrlEncoding
     * @return array
     */
    public function delete($url, $withUrlEncoding = false)
    {
        if ($withUrlEncoding) {
            $url = $this->curlEscape($url);
        }
        return $this->setOpt(CURLOPT_URL, $url)
            ->setOpt(CURLOPT_CUSTOMREQUEST, "DELETE")
            ->exec();
    }

    /**
     * URL encodes the given string
     * @param $escapestring
     * @return bool|string
     */
    private function curlEscape($escapestring)
    {
        return curl_escape($this->curl, $escapestring);
    }

    /**
     * This can be used, if you don't want to set all options manually and rather set all options in one action
     * @param $optionArray
     */
    public function setOptArray($optionArray)
    {
        curl_setopt_array($this->curl, $optionArray);
    }

    /**
     * Set an option for a cURL transfer
     * @param int $option The cURL option
     * @param string $value the value for the option
     * @throws \Exception
     * @return $this returns itself
     */
    public function setOpt($option, $value)
    {
        if (!isset($option) || !isset($value)) {
            throw new \Exception("At least one option parameter (Option/Value) is missing");
        }
        curl_setopt($this->curl, $option, $value);
        return $this;
    }

    /**
     * @param null $cookie The contents of the "Cookie: " header to be used in the HTTP request.
     * Note that multiple cookies are separated with a semicolon followed by
     * a space (e.g., "fruit=apple; colour=red")
     *
     * @param null $cookiefile  The name of the file containing the cookie data.
     * The cookie file can be in Netscape format, or just plain HTTP-style headers dumped
     * into a file. If the name is an empty string, no cookies are loaded, but cookie handling is still enabled.
     *
     * @param null $cookiejar The name of a file to save all internal cookies to when the handle is closed,
     * e.g. after a call to curl_close.
     *
     * @return $this Returns itself
     */
    public function setCookieOptions($cookie = null, $cookiefile = null, $cookiejar = null)
    {
        if ($cookie != null) {
            $this->setOpt(CURLOPT_COOKIE, $cookie);
        }
        if ($cookiefile != null) {
            $this->setOpt(CURLOPT_COOKIEFILE, $cookiefile);
        }
        if ($cookiejar != null) {
            $this->setOpt(CURLOPT_COOKIEJAR, $cookiejar);
        }
        return $this;
    }

    /**
     * The contents of the "Accept-Encoding: " header. This enables decoding of the response.
     * Supported encodings are "identity", "deflate", and "gzip".
     * If an empty string, "", is set, a header containing all supported encoding types is sent.
     *
     * Added in cURL 7.10.
     *
     * @param string $encoding
     *
     * @return $this Returns itself
     */
    public function setEncoding($encoding)
    {
        $this->setOpt(CURLOPT_ENCODING, $encoding);
        return $this;
    }

    public function setFile($file)
    {
        $this->setOpt(CURLOPT_FILE, $file);
        return $this;
    }

    /**
     * true to follow any "Location: " header that the server sends as part of the HTTP header
     * (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
     *
     * @param bool $followlocation true OR false
     * @return $this
     */
    public function setFollowlocation($followlocation = true)
    {
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $followlocation);
        return $this;
    }

    /**
     * true to include the header in the output.
     * @param bool $header
     *
     * @return $this
     */
    public function setHeader($header = true)
    {
        $this->setOpt(CURLOPT_HEADER, $header);
        return $this;
    }

    /**
     * An array of HTTP header fields to set, in the format array('Content-type: text/plain', 'Content-length: 100')
     * @param array $header
     *
     * @return $this
     */
    public function setHTTPHeader($header)
    {
        $this->setOpt(CURLOPT_HTTPHEADER, $header);
        return $this;
    }

    /**
     * The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
     * @param int $maxredirs
     *
     * @return $this
     */
    public function setMaxredirs($maxredirs)
    {
        $this->setOpt(CURLOPT_MAXREDIRS, $maxredirs)
            ->setFollowlocation();
        return $this;
    }

    /**
     * The contents of the "Referer: " header to be used in a HTTP request.
     * @param string $referer
     *
     * @return $this Return itself
     */
    public function setReferer($referer)
    {
        $this->setOpt(CURLOPT_REFERER, $referer);
        return $this;
    }

    /**
     * 1 to check the existence of a common name in the SSL peer certificate.
     * 2 to check the existence of a common name and also verify that it matches the hostname provided.
     *
     * In production environments the value of this option should be kept at 2 (default value).
     *
     * Support for value 1 removed in cURL 7.28.1
     *
     * @param $sslVerifyhost
     *
     * @return $this Return itself
     */
    public function setSSLVerifyhost($sslVerifyhost)
    {
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, $sslVerifyhost);
        return $this;
    }

    /**
     * false to stop cURL from verifying the peer's certificate.
     * Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or
     * a certificate directory can be specified with the CURLOPT_CAPATH option.
     *
     * true by default as of cURL 7.10. Default bundle installed as of cURL 7.10.
     * @param bool $sslVerifypeer
     *
     * @return $this Return itself
     */
    public function setSSLVerifypeer($sslVerifypeer = true)
    {
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, $sslVerifypeer);
        return $this;
    }

    /**
     * The SSL version (2 or 3) to use. By default PHP will try to determine this itself,
     * although in some cases this must be set manually.
     *
     * @return $this Return itself
     */
    public function setSSLversion()
    {
        $this->setOpt(CURLOPT_SSLVERSION, 4);
        return $this;
    }

    /**
     * The contents of the "User-Agent: " header to be used in a HTTP request.
     * @param string $useragent
     *
     * @return $this Return itself
     */
    public function setUseragent($useragent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $useragent);
        return $this;
    }

    /**
     * Sets CURLOPT_FORBID_REUSE
     * true to force the connection to explicitly close when it has finished processing, and not be pooled for reuse.
     * @param bool $curloptForbidReuse
     * @return $this
     */
    public function setForbidReuse($curloptForbidReuse = true)
    {
        $this->setOpt(CURLOPT_FORBID_REUSE, $curloptForbidReuse);
        return $this;
    }


    /**
     * A username and password formatted as "[username]:[password]" to use for the connection.
     *
     * @param      $userName
     * @param      $password
     * @param bool $unrestrictedAuth true to keep sending the username and password when following locations
     *                               (using CURLOPT_FOLLOWLOCATION), even when the hostname has changed.
     * @return $this
     */
    public function setUserPwd($userName, $password, $unrestrictedAuth = true)
    {
        if ($unrestrictedAuth) {
            $this->setOpt(CURLOPT_UNRESTRICTED_AUTH, $unrestrictedAuth);
        }
        $this->setOpt(CURLOPT_USERPWD, $userName.":".$password);
        return $this;
    }

    public function getHeaderFromResponse()
    {
        return substr($this->response, 0, $this->info['header_size']);
    }

    public function getContentFromResponse()
    {
        return substr($this->response, $this->info['header_size']);
    }

    /**
     * Executes the curl request and returns it or not
     *
     * @return array
     */
    protected function exec()
    {
        $this->response             = curl_exec($this->curl);
        $this->info                 = curl_getinfo($this->curl);
        $this->error                = curl_error($this->curl);
        $this->errorNo              = curl_errno($this->curl);
        $this->responseHeader       = $this->getHeaderFromResponse();
        $returnArray                = array();
        $returnArray['response']    = $this->response;
        $returnArray['info']        = $this->info;
        if (!$this->response) {
            $returnArray['error']   =  $this->error;
            $this->hasError         = true;
        }
        return $returnArray;
    }
}