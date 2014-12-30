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

namespace Vegas\Social\Facebook;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;
use Vegas\Social\CurlFile;

/**
 * Class Publish
 * @package Vegas\Social\Facebook
 */
class Publish extends Service implements PublishInterface
{
    use PublishHelper;

    /**
     * @var array
     */
    private $postParams = [];

    /**
     * @var string, for example 'feed' or 'photos'
     */
    private $publishArea;

    /**
     * @var
     */
    private $targetUser;

    /**
     * @param array $config
     * @throws \Vegas\Social\Exception
     *
     * param example:
     *
     * $config = [
     *  'app_key' => 'APP ID',
     *  'app_secret' => 'APP SECRET',
     *  'access_token' => 'USER TOKEN'
     * ];
     *
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    /**
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    public function setDefaultPostParams()
    {
        $userToken = $this->fbSession->getToken();
        $userName = $this->getUserData()->getName();

        if ($userName) {
            $this->postParams = [
                'access_token' => $userToken,
                'name' => $userName,
                'link' => '',
                'caption' => '',
                'message' => '',
            ];

            $this->publishArea = 'feed';
            $this->targetUser = 'me';

            return $this;
        }
        throw new \Vegas\Social\Exception\InvalidPostParamsException('postParams');
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setTitle($string)
    {
        $this->postParams['caption'] = $string;
        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setMessage($string)
    {
        $this->postParams['message'] = $string;
        return $this;
    }

    /**
     * @param string $string
     * @throws \Vegas\Social\Exception\InvalidLinkException
     */
    public function setLink($string)
    {
        if ($this->validateLink($string)) {
            $this->postParams['link'] = $string;
            return $this;
        }
        throw new \Vegas\Social\Exception\InvalidLinkException($string);
    }

    /**
     * @param CurlFile|string $photo
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setPhoto($photo)
    {
        $this->publishArea = 'photos';
        $message = $this->postParams['message'];
        $this->postParams = [
            'message' => $message
        ];

        if (is_object($photo) && $photo instanceof CurlFile) {
            $this->postParams['source'] = $photo->getResource();
        } else if (is_string($photo) && $this->validateLink($photo)) {
            $this->postParams['url'] = $photo;
        } else {
            throw new \Vegas\Social\Exception\InvalidArgumentException('setPhoto');
        }

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
     * @param array $array
     * @return $this
     * @throws \Vegas\Social\Exception\InvalidLinkException
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    public function setPostParams($array)
    {
        if (!isset($array['url'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('url');
        if (!$this->validateLink($array['url'])) throw new \Vegas\Social\Exception\InvalidLinkException($array['url']);
        if (!isset($array['message'])) throw new \Vegas\Social\Exception\InvalidPostParamsException('message');

        $this->postParams = $array;
        return $this;
    }

    /**
     * @return string
     * @throws \Vegas\Social\Exception
     */
    public function post()
    {
        $this->checkPostParams();

        try {
            return $this->request('POST', '/' . $this->targetUser . '/' . $this->publishArea, $this->postParams)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception\UnexpectedResponseException($e);
        }
    }

    /**
     * @param string $postId
     * @return mixed
     * @throws \Vegas\Social\Exception
     */
    public function deletePost($postId)
    {
        try {
            $this->request('DELETE', '/' . $postId);
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception\UnexpectedResponseException($e);
        }

        return $postId;
    }

    /**
     * @throws \Vegas\Social\Exception\InvalidPostParamsException
     */
    private function checkPostParams()
    {
        $requiredParams = [];
        if ($this->publishArea == 'feed') $requiredParams = ['link', 'caption', 'message'];

        foreach ($requiredParams as $param) {
            if ($this->postParams[$param] == '') throw new \Vegas\Social\Exception\InvalidPostParamsException($param);
        }
    }
}
