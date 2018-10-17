<?php

namespace Enginr\Http;

use Enginr\Socket;
use Enginr\Exception\ResponseException;
use Enginr\Console\Console;

class Response {
    /**
     * The HTTP end of line characters
     * 
     * @var string An EOL characters
     */
    const CRLF = "\r\n";

    /**
     * The HTTP version
     * 
     * @var string An HTTP version
     */
    const VERSION = 'http/1.1';

    /**
     * The HTTP status code matches
     * 
     * @var array An associative array of HTTP status => label
     */
    const STATUS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
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
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
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
        599 => 'Network Connect Timeout Error'
    ];

    /**
     * The MIME type extension matches
     * 
     * @var array An associative array of ext => MIME
     */
    const MIMESEXT = [
        '.aac'   => 'audio/aac',
        '.abw'   => 'application/x-abiword',
        '.arc'   => 'application/octet-stream',
        '.avi'   => 'video/x-msvideo',
        '.azw'   => 'application/vnd.amazon.ebook',
        '.bin'   => 'application/octet-stream',
        '.bz'    => 'application/x-bzip',
        '.bz2'   => 'application/x-bzip2',
        '.csh'   => 'application/x-csh',
        '.css'   => 'text/css',
        '.csv'   => 'text/csv',
        '.doc'   => 'application/msword',
        '.docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.eot'   => 'application/vnd.ms-fontobject',
        '.epub'  => 'application/epub+zip',
        '.gif'   => 'image/gif',
        '.htm'   => 'text/html',
        '.html'  => 'text/html',
        '.ico'   => 'image/x-icon',
        '.ics'   => 'text/calendar',
        '.jar'   => 'application/java-archive',
        '.jpeg'  => 'image/jpeg',
        '.jpg'   => 'image/jpeg',
        '.js'    => 'application/javascript',
        '.json'  => 'application/json',
        '.mid'   => 'audio/midi',
        '.midi'  => 'audio/midi',
        '.mpeg'  => 'video/mpeg',
        '.mpkg'  => 'application/vnd.apple.installer+xml',
        '.odp'   => 'application/vnd.oasis.opendocument.presentation',
        '.ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
        '.odt'   => 'application/vnd.oasis.opendocument.text',
        '.oga'   => 'audio/ogg',
        '.ogv'   => 'video/ogg',
        '.ogx'   => 'application/ogg',
        '.otf'   => 'font/otf',
        '.png'   => 'image/png',
        '.pdf'   => 'application/pdf',
        '.ppt'   => 'application/vnd.ms-powerpoint',
        '.pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        '.rar'   => 'application/x-rar-compressed',
        '.rtf'   => 'application/rtf',
        '.sh'    => 'application/x-sh',
        '.svg'   => 'image/svg+xml',
        '.swf'   => 'application/x-shockwave-flash',
        '.tar'   => 'application/x-tar',
        '.tif'   => 'image/tiff',
        '.tiff'  => 'image/tiff',
        '.ts'    => 'application/typescript',
        '.ttf'   => 'font/ttf',
        '.vsd'   => 'application/vnd.visio',
        '.wav'   => 'audio/x-wav',
        '.weba'  => 'audio/webm',
        '.webm'  => 'video/webm',
        '.webp'  => 'image/webp',
        '.woff'  => 'font/woff',
        '.woff2' => 'font/woff2',
        '.xhtml' => 'application/xhtml+xml',
        '.xls'   => 'application/vnd.ms-excel',
        '.xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.xml'   => 'application/xml',
        '.xul'   => 'application/vnd.mozilla.xul+xml',
        '.zip'   => 'application/zip',
        '.3gp'   => 'video/3gpp',
        '.3g2'   => 'video/3gpp2',
        '.7z'    => 'application/x-7z-compressed'
    ];

    /**
     * The client socket resource
     * 
     * @var resource A socket resource
     */
    private $_client;

    /**
     * The HTTP start line
     * 
     * @var string An HTTP start line
     */
    private $_startline;

    /**
     * The HTTP headers
     * 
     * @var array An HTTP headers
     */
    private $_headers;

    /**
     * The response constructor
     * 
     * @param resource $client A socket resource
     * 
     * @throws ResponseException If $client is not a type of resource
     * 
     * @return void
     */
    public function __construct($client) {
        if (!is_resource($client))
            throw new ResponseException('1st parameter must be a type of resource.');

        $this->_client = $client;
        $this->setStatus(200);
        $this->setHeaders([
            'Connection'   => 'keep-alive',
            'Date'         => date('D d M Y H:i:s e'),
            'Content-Type' => 'text/plain',
            'X-Powered-By' => 'Enginr'
        ]);
    }

    /**
     * Parse the headers array into a string
     * 
     * @param void
     * 
     * @return string The headers to string
     */
    private function _headersToString(): string {
        $headers = '';

        foreach ($this->_headers as $name => $values) {
            $headers .= $name . ': ';

            if (is_array($values)) {
                foreach ($values as $i => $value) {
                    $headers .= $value;

                    if ($i < count($values) - 1)
                        $headers .= ','; 
                }
            } else $headers .= $values;

            $headers .= self::CRLF;
        }

        return $headers;
    }

    /**
     * Initialize the HTTP headers propertie
     * If HTTP headers already exists, merge it with the headers specified
     * 
     * @param array $headers An associative array of headers
     * 
     * @throws ResponseException If $headers is not an associative array
     * 
     * @return void
     */
    public function setHeaders(array $headers): void {
        foreach ($headers as $name => $value) {
            if (is_int($name))
                throw new ResponseException('1st parameter must be an associative array.');
        }

        if (count($this->_headers))
            array_merge($this->_headers, $headers);
        else
            $this->_headers = $headers;
    }

    /**
     * Set the first line of HTTP response
     * 
     * @param int $status An HTTP status code
     * 
     * @return void
     */
    public function setStatus(int $status): void {
        $this->_startline = self::VERSION . ' ' . $status . ' ' . self::STATUS[$status];
    }

    /**
     * Build the HTTP response
     * 
     * @param mixed $body An HTTP body
     * 
     * @return string The HTTP response
     */
    private function _buildHttpResponse($body): string {
        return $this->_startline . self::CRLF .
               $this->_headersToString() . self::CRLF . self::CRLF .
               (string)$body;
    }

    /**
     * Write a message to the client
     * Then, close the client socket
     * 
     * @param mixed $body A data to send
     * 
     * @return void
     */
    public function send($body): void {
        Socket::write($this->_client, $this->_buildHttpResponse($body));
        $this->end();
    }

    /**
     * Close the client socket
     * 
     * @param void
     * 
     * @return void
     */
    public function end(): void {
        Socket::close($this->_client);
    }
}