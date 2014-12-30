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

use Vegas\Social\CurlFile;
use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

/**
 * Class Publish
 * @package Vegas\Social\Twitter
 */
class Publish extends Service implements PublishInterface
{
    use PublishHelper;

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
     * $config = [
     *   'consumer_key' => 'CONSUMER KEY',
     *   'consumer_secret' => 'CONSUMER SECRET',
     *   'token' => 'TOKEN',
     *   'secret' => 'SECRET',
     * ];
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
        $this->postParams = [
            'method' => 'POST',
            'url' => $this->url("1.1/statuses/update"),
            'params' => [
                'status' => ''
            ]
        ];

        $this->setTitle('');
        $this->setMessage('');

        return $this;
    }

    /**
     * @param string $string
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
     * @param string $url
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setLink($url)
    {
        if (!is_string($url) || !$this->validateLink($url)) {
            throw new \Vegas\Social\Exception\InvalidLinkException($url);
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
     * @param CurlFile|string $photo
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidArgumentException
     */
    public function setPhoto($photo)
    {
        if (is_string($photo) && $this->validateLink($photo)) {
            $this->tmpFile = './image_tmp' . time();
            file_put_contents($this->tmpFile, file_get_contents($photo));
            $fileType = image_type_to_mime_type(exif_imagetype($this->tmpFile));
            $photo = new CurlFile($this->tmpFile, $fileType, $this->postMessage);
        }

        if (is_object($photo) && $photo instanceof CurlFile) {
            $this->postParams['params']['media[]'] = $photo->getResource();
        } else {
            throw new \Vegas\Social\Exception\InvalidArgumentException('setPhoto');
        }

        $this->postParams['url'] = $this->url("1.1/statuses/update_with_media");
        $this->postParams['multipart'] = true;

        return $this;
    }

    /**
     * @return array
     */
    public function getPostParams()
    {
        return $this->postParams;
    }

    /**
     * @param $array
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
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
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
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
     * @param string $id
     * @throws \Vegas\Social\Exception\UnexpectedResponseException
     */
    public function deletePost($id)
    {
        try {
            $code = $this->user_request([
                'method' => 'POST',
                'url' => $this->url("1.1/statuses/destroy/" . $id),
                'params' => []
            ]);

            if ($code == 200) {
                $response = json_decode($this->response['response'], true);
                return $response['id'];
            }
        } catch (\Exception $ex) {
            throw new \Vegas\Social\Exception\UnexpectedResponseException($ex);
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
     * @param array $array
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    private function checkParams($array)
    {
        if (!isset($array['method']) || $array['method'] != 'POST') throw new \Vegas\Social\Exception\InvalidPostParamsException('method');
        if (!isset($array['params']['status'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('params.status');
    }
}
