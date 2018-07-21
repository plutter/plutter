<?php
namespace Plutter\Http;

use Plutter\Http\Emitter\{Emitter, Text};

class Response {
    const MIN_STATUS_CODE_VALUE = 100;
    const MAX_STATUS_CODE_VALUE = 599;
    static $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];
    protected $statusCode = 200;
    protected $reasonPhrase = 'OK';
    protected $emitter;
    protected $body = '';
    protected $headers = [];
    public function __construct(){
        $this->headers = new Data\Header([]);
        $this->emitter = new Text;
    }
    public function clone(){
        $clone = clone $this;
        return $clone;
    }
    private function fetchData(){
        $method = $this->location->get("method");
        switch($method){
            case "GET":
                return new Data\Parsed($_GET);
            break;
            case "POST":
                return new Data\Parsed($_POST);
            break;
            default:
                $data = new Data\Stream;
                $files = $data->getFiles();
                $this->data["file"] = new Data\File($files);
                return $data;
            break;
        }
    }
    public function emit(){
        return $this->emitter->emit($this);
    }
    public function getEmitter($emitter): Emitter {
        return $this->emitter;
    }
    public function getBody($body){
        return $this->body;
    }
    public function getStatusCode($code): int {
        return $this->statusCode;
    }
    public function getReasonPhrase($code): string {
        return $this->reasonPhrase;
    }
    public function setEmitter(Emitter $emitter){
        $this->emitter = $emitter;
    }
    public function setBody($body){
        $this->body = $body;
    }
    public function setStatusCode(int $code): Response {
        if (! is_numeric($code)
            || is_float($code)
            || $code < static::MIN_STATUS_CODE_VALUE
            || $code > static::MAX_STATUS_CODE_VALUE
        ) {
            throw new InvalidArgumentException(sprintf(
                'Invalid status code "%s"; must be an integer between %d and %d, inclusive',
                (is_scalar($code) ? $code : gettype($code)),
                static::MIN_STATUS_CODE_VALUE,
                static::MAX_STATUS_CODE_VALUE
            ));
        }
        $this->statusCode = $code;
        $this->reasonPhrase = isset(self::$phrases[$code])?self::$phrases[$code]:'';
		return $this;
    }
}