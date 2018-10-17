<?php

namespace Enginr\Http;

class Http {
    /**
     * The HTTP version
     * 
     * @var string An HTTP version
     */
    const VERSION = 'http/1.1';

    /**
     * The HTTP end of line characters
     * 
     * @var string An EOL characters
     */
    const CRLF = "\r\n";

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
}