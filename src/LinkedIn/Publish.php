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

namespace Vegas\Social\LinkedIn;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

/**
 * Class Publish
 * @package Vegas\Social\LinkedIn
 */
class Publish extends Service implements PublishInterface
{
    use PublishHelper;

    /**
     * @var array
     */
    private $postParams = [];

    /**
     * @param array $config
     *
     * param example:
     *
     * private $config = [
     *   'app_key' => 'APP KEY',
     *   'app_secret' => 'APP SECRET',
     *   'access_token' => 'USER TOKEN'
     * ];
     *
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    /**
     * @return mixed
     */
    public function post()
    {
        $this->checkParams($this->postParams);
        $extraHeaders = ['Content-type' => 'application/json'];
        $result = json_decode($this->service->request('/people/~/shares?format=json', 'POST', json_encode($this->postParams), $extraHeaders), true);
        return $result;
    }

    /**
     * @return $this
     */
    public function setDefaultPostParams()
    {
        $post = [
            'comment' => '',
            'content' => [
                'title' => '',
                'description' => '',
                'submitted-url' => ''
            ],
            'visibility' => ['code' => 'anyone']
        ];

        $this->postParams = $post;

        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setTitle($string)
    {
        $this->postParams['content']['title'] = $string;

        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setMessage($string)
    {
        $this->postParams['content']['description'] = $string;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidLinkException
     */
    public function setLink($url)
    {
        if (!is_string($url)) {
            throw new \Vegas\Social\Exception\InvalidLinkException('');
        }
        if (!$this->validateLink($url)) {
            throw new \Vegas\Social\Exception\InvalidLinkException($url);
        }

        $this->postParams['content']['submitted-url'] = $url;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidArgumentException
     * @throws \Vegas\Social\Exception\InvalidLinkException
     */
    public function setPhoto($url)
    {
        if (!is_string($url)) {
            throw new \Vegas\Social\Exception\InvalidArgumentException('setPhoto');
        }

        if (!$this->validateLink($url)) {
            throw new \Vegas\Social\Exception\InvalidLinkException($url);
        }

        $this->postParams['content']['submitted-image-url'] = $url;

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
     * @param $postParams
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    public function setPostParams($postParams)
    {
        $this->checkParams($postParams);
        $this->postParams = $postParams;

        return $this;
    }

    /**
     * @param $postParams
     * @throws \Vegas\Social\Exception\InvalidLinkException
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    private function checkParams($postParams)
    {
        //check required params
        if (!isset($postParams['content'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('content');
        if (!isset($postParams['content']['title'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('content.title');
        if (!isset($postParams['content']['submitted-url'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('content.submitted-url');

        $this->setLink($postParams['content']['submitted-url']);
    }
}