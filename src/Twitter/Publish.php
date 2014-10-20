<?php
/**
 * This file is part of Vegas package
 *
 * @author Tomasz Borodziuk <tomasz.borodziuk@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Social\Twitter;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

/**
 * Class Publish
 * @package Vegas\Social\Twitter
 */
class Publish extends Service implements PublishInterface
{
    /**
     * @var array
     */
    private $postParams;
    /**
     * @var string
     */
    private $postTitle = '';
    /**
     * @var string
     */
    private $postMessage = '';
    /**
     * @var string
     */
    private $postLink = '';
    /**
     * @var string
     */
    private $tmpFile = '';

    /**
     * @param array $config
     *
     * param example:
     *
     * $config = array(
     *   'consumer_key' => 'XXX9dOKA3Nga2VskRPcAWWIqr',
     *   'consumer_secret' => 'XXXIYIqpGfDnM03EFOBkkD26QT3xMIzwqOTwXFyAYZzP44ZFmK',
     *   'token' => '2831990805-dCAoGfdhYEX60GAG7xsOdIblMdfVerdfF9OoEv8',
     *   'secret' => '5WoQo1JtO8H61cOUvVPQbs6tTXZSTVB2ukGTou7yXkZOx',
     * );
     *
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    /**
     * @return $this
     */
    public function setDefaultPostParams()
    {
        $this->postParams = array(
            'method' => 'POST',
            'url' => $this->url("1.1/statuses/update"),
            'params' => array(
                'status' => ''
            )
        );

        $this->setTitle('');
        $this->setMessage('');

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setTitle($string)
    {
        $this->postTitle = $string;
        $this->updateStatus();

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setMessage($string)
    {
        $this->postMessage = $string;
        $this->updateStatus();

        return $this;
    }

    /**
     * @param $url
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setLink($url)
    {
        if (!is_string($url) || !PublishHelper::validateLink($url)) {
            throw new \Vegas\Social\Exception("setLink - url is not valid");
        }

        $this->postLink = $url;
        $this->updateStatus();

        return $this;
    }

    /**
     * @return $this
     */
    private function updateStatus()
    {
        if ($this->postTitle != '') {
            $this->postParams['params']['status'] = $this->postTitle . "\n\n" . $this->postMessage;
        } else {
            $this->postParams['params']['status'] = $this->postMessage;
        }

        if ($this->postLink != '') {
            $this->postParams['params']['status'] = $this->postParams['params']['status'] . "\n" . $this->postLink;
        }

        return $this;
    }

    /**
     * @param $photo
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setPhoto($photo)
    {
        if (gettype($photo) == 'object' && get_class($photo) == 'CURLFile') {
            $this->postParams['params']['media[]'] = $photo;
        } else if (gettype($photo) == 'string' && PublishHelper::validateLink($photo)) {
            $this->tmpFile = './image_tmp' . time();
            file_put_contents($this->tmpFile, file_get_contents($photo));
            $file_type = image_type_to_mime_type(exif_imagetype($this->tmpFile));
            $curl_file = curl_file_create($this->tmpFile, $file_type, $this->postMessage);
            $this->postParams['params']['media[]'] = $curl_file;
        } else {
            throw new \Vegas\Social\Exception('not valid argument in setPhoto');
        }

        $this->postParams['url'] = $this->url("1.1/statuses/update_with_media");
        $this->postParams['multipart'] = true;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostParams()
    {
        return $this->postParams;
    }

    /**
     * @param $array
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setPostParams($array)
    {
        $this->checkParams($array);

        $this->postParams = $array;
        return $this;
    }

    /**
     * @return mixed
     * @throws \Vegas\Social\Exception
     */
    public function post()
    {
        $this->checkParams($this->postParams);
        $code = $this->user_request($this->postParams);

        if ($code != 200) {
            throw new \Vegas\Social\Exception("Error: " . $code);
        }

        $response = json_decode($this->response['response'], true);

        return $response['id'];
    }

    /**
     * @param $id
     * @throws \Vegas\Social\Exception
     */
    public function deletePost($id)
    {
        try {
            $code = $this->user_request(array(
                'method' => 'POST',
                'url' => $this->url("1.1/statuses/destroy/" . $id),
                'params' => array()
            ));

            if ($code == 200) {
                $response = json_decode($this->response['response'], true);
                return $response['id'];
            }
        } catch (\Exception $ex) {
            throw new \Vegas\Social\Exception(var_export($ex, true));
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->tmpFile != '' && file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        $this->tmpFile = '';
    }

    /**
     * @param $array
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    private function checkParams($array)
    {
        if (!isset($array['method']) || $array['method'] != 'POST') throw new \Vegas\Social\Exception\InvalidPostParamsException('method');
        if (!isset($array['params']['status'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('params.status');
    }
}

?>