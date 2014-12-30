<?php
/**
 * @author Radosław Fąfara <radek@archdevil.pl>
 * @copyright (c) 2014, Amsterdam Standard
 */

namespace Vegas\Social;

/**
 * Class CurlFile
 * Workaround for PHP < 5.5 where class CURLFile doesn't exist in cURL module
 * Use getResource() to retrieve applicable value.
 * @package Vegas\Social
 */
class CurlFile
{
    /**
     * @var \CURLFile|string
     */
    private $resource;

    /**
     * @param string $filename
     * @param string $mimetype
     * @param string $postname
     */
    public function __construct($filename, $mimetype = '', $postname = '')
    {
        if (function_exists('curl_file_create')) {
            $this->resource = new \CURLFile($filename, $mimetype, $postname);
        } else {
            if (empty($postname)) {
                $postname = basename($filename);
            }
            $resource = [
                "@{$filename}",
                "filename={$postname}"
            ];
            $mimetype && array_push($resource, "type={$mimetype}");
            $this->resource = implode(';', $resource);
        }
    }

    /**
     * Get appropriate file value
     * @return \CURLFile|string
     */
    public function getResource()
    {
        return $this->resource;
    }
}
