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

/**
 * Class Publish
 * @package Vegas\Social\Facebook
 */
class Publish extends Service implements PublishInterface
{
    /**
     * @var array
     */
    private $postParams = array();

    /**
     * @var string, for example 'feed' or 'photos'
     */
    private $publishArea;

    /**
     * @var
     */
    private $targetUser;

    /**
     * @param $config
     * @throws \Vegas\Social\Exception
     *
     * param example:
     *
     * $config = array(
     *  'app_key' => '704865089606542',
     *  'app_secret' => '786207332b78fb7819d375d480c1c3cd',
     *  'access_token' => 'CAAKBEjFH044BAIIz2eFnS2sVZA0prvVhUA99rPZC8nP32Xtw6T595YuoDXcjgszFSWPNXLzzvtD7sba6FqX373KoOxIbRZAZCP4ZBD8gBZB6jIRvVvLAsqApQLYMxugmQPYlRfxdWBZA3SIYzqBPflIOEvAhll7ybSfOJtK7c7SZCe9zZAui0DmSuLrtC5gjiUqsZD'
     * );
     *
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    /**
     * @throws \Vegas\Social\Exception
     */
    public function setDefaultPostParams()
    {
        $userToken = $this->fbSession->getToken();
        $userName = $this->getUserData()->getName();

        if ($userName) {
            $this->postParams = array(
                'access_token' => $userToken,
                'name' => $userName,
                'link' => 'http://testdomain.com/',
                'caption' => 'Test caption',
                'message' => 'Test message',
            );

            $this->publishArea = 'feed';
            $this->targetUser = 'me';

            return $this;
        }
        throw new \Vegas\Social\Exception('SE4', 'postParams error');
    }

    /**
     * @param $string
     * @return $this
     */
    public function setTitle($string)
    {
        $this->postParams['caption'] = $string;
        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setMessage($string)
    {
        $this->postParams['message'] = $string;
        return $this;
    }

    /**
     * @param $string
     * @throws \Vegas\Social\Exception
     */
    public function setLink($string)
    {
        if (PublishHelper::validateLink($string)) {
            $this->postParams['link'] = $string;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE3', 'not valid link');
    }

    /**
     * @param $photo
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setPhoto($photo)
    {
        $this->publishArea = 'photos';
        $message = $this->postParams['message'];
        $this->postParams = array(
            'message' => $message
        );

        if (gettype($photo) == 'object' && get_class($photo) == 'CURLFile') {
            $this->postParams['source'] = $photo;
        } else if (gettype($photo) == 'string' && PublishHelper::validateLink($photo)) {
            $this->postParams['url'] = $photo;
        } else {
            throw new \Vegas\Social\Exception('SE3', 'not valid argument in setPhoto');
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
     * @param $array
     * @throws \Vegas\Social\Exception
     */
    public function setPostParams($array)
    {
        if (isset($array['url'])
            && (PublishHelper::validateLink($array['url'])
                && isset($array['message']))
        ) {
            $this->postParams = $array;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE9', 'not valid post params');
    }

    /**
     * @return bool
     * @throws \Vegas\Social\Exception
     */
    public function post()
    {
        $post_id = false;

        try {
            $post_id = $this->request('POST', '/' . $this->targetUser . '/' . $this->publishArea, $this->postParams)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    /**
     * @param $post_id
     * @return mixed
     * @throws \Vegas\Social\Exception
     */
    public function deletePost($post_id)
    {
        try {
            $this->request('DELETE', '/' . $post_id);
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE6', 'Could not delete post.');
        }

        return $post_id;
    }
}

?>