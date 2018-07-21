<?php
namespace Plutter\Http;


class Location {
    private $request;
    private $method = 'GET';
    private $version = 1.1;
    private $uri = '';
    private $scheme = '';
    private $host = '';
    private $port = 80;
    private $path = '';
    private $query = '';
    private $fragment = '';
    public function __construct(Request $request){
        $this->request = $request;
        $this->method = $this->processMethod();
        $this->version = $this->processVersion();
        $this->uri = $this->processRequestUri();
        $this->processUri();
    }
    public function clone(){
        $clone = clone $this;
        return $clone;
    }
    public function get($key){
        return $this->__getter($key);
    }
    public function __getter($key){
        if(!in_array($key, [
            "scheme",
            "version",
            "protocol",
            "method",
            "uri",
            "host",
            "port",
            "path",
            "query",
            "fragment",
        ]))
            throw new HttpException("Can't get key \"$key\"");
        return $this->$key;
    }
    public function __setter($key, $value){
        $this->__getter($key);
        $this->$key = $value;
    }
    public function set($key, $value){
        $clone = $this->clone;
        $clone->__setter($key, $value);
        return $clone;
    }
    private function processMethod(){
        $method = $_SERVER['REQUEST_METHOD'];
        $method = $method != ""?$method:'GET';
        $method = $this->validateMethod($method);
        return $method;
    }
    private function read($key, $values, $default = ''){
        if(isset($values[$key]))
            return $values[$key];
        return $default;
    }
    private function processRequestUri(){
        return $_SERVER["REQUEST_URI"];
        $iisUrlRewritten = $this->read('IIS_WasUrlRewritten', $_SERVER);
        $unencodedUrl    = $this->read('UNENCODED_URL', $_SERVER, '');
        if ('1' == $iisUrlRewritten && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }
        $requestUri = $this->read('REQUEST_URI', $_SERVER);
        $httpXRewriteUrl = $this->read('HTTP_X_REWRITE_URL', $_SERVER);
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }
        $httpXOriginalUrl = $this->read('HTTP_X_ORIGINAL_URL', $_SERVER);
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }
        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }
        $origPathInfo = $this->read('ORIG_PATH_INFO', $_SERVER);
        if (empty($origPathInfo)) {
            return '/';
        }
        return $origPathInfo;
    }
    private function processHostPort(){
        $host = '';
        $port = 80;
        if ($this->read('host', $this->request->getData("header")->getAll(), false)) {
            $host = $this->read('host', $this->request->getData("header")->getAll());
            if (preg_match('|\:(\d+)$|', $host, $matches)) {
                $host = substr($host, 0, -1 * (strlen($matches[1]) + 1));
                $port = (int) $matches[1];
            }
        } else if($this->read('SERVER_NAME', $_SERVER, false)) {
            $host = $_SERVER['SERVER_NAME'];
            if (isset($_SERVER['SERVER_PORT'])) {
                $port = (int) $_SERVER['SERVER_PORT'];
            }
            if (isset($_SERVER['SERVER_ADDR']) && preg_match('/^\[[0-9a-fA-F\:]+\]$/', $host)) {
                $host = '[' . $_SERVER['SERVER_ADDR'] . ']';
                if ($port . ']' === substr($host, strrpos($host, ':') + 1)) {
                    $port = null;
                }
            }
        }
        return [$host, $port];
    }
    private function processUri(){
        $_SERVER = $_SERVER;
        $scheme = 'http';
        $https  = self::read('HTTPS', $_SERVER);
        if (($https && 'off' !== $https)
            || self::getHeader('x-forwarded-proto', $headers, false) === 'https'
        ) {
            $scheme = 'https';
        }
        if (! empty($host)) {
            $uri = $uri->withHost($host);
            if (! empty($port)) {
                $uri = $uri->withPort($port);
            }
        }
        list($host, $port) = $this->processHostPort();
        $path = $this->uri;
        $path = $this->stripQueryString($path);
        $query = '';
        if (isset($_SERVER['QUERY_STRING'])) {
            $query = ltrim($_SERVER['QUERY_STRING'], '?');
        }
        $fragment = '';
        if (strpos($path, '#') !== false) {
            list($path, $fragment) = explode('#', $path, 2);
        }
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }
    public function stripQueryString($path){
        if (($qpos = strpos($path, '?')) !== false) {
            return substr($path, 0, $qpos);
        }
        return $path;
    }
    private function validateMethod($method){
        if (null === $method) {
            return 'GET';
        }
        if (! is_string($method)) {
            throw new HttpException(sprintf(
                'Unsupported HTTP method; must be a string, received %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }
        if (! preg_match('/^[!#$%&\'*+.^_`\|~0-9a-z-]+$/i', $method)) {
            throw new HttpException(sprintf(
                'Unsupported HTTP method "%s" provided',
                $method
            ));
        }
        return $method;
    }
    private function processVersion(){
        $_SERVER = $_SERVER;
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            if (! preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $_SERVER['SERVER_PROTOCOL'], $matches)) {
                throw new UnexpectedValueException(sprintf(
                    'Unrecognized protocol version (%s)',
                    $_SERVER['SERVER_PROTOCOL']
                ));
            }
            return (float) $matches['version'];
        }
        return 1.1;
    }
}