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
    /**
     * @var array
     */
    private $postParams = array();

    /**
     * @param $config
     *
     * param example:
     *
     * private $config = array(
     *   'app_key' => '77c863ytgelytp',
     *   'app_secret' => 'CHHUveJYMda6FzfH',
     *   'access_token' => 'AQW8quBxpTEJxPsKpb0MzKyrAooElYTsJQ4aH3w8c-OUZmd9kkUWsnJbEZrWYrupRinIvFUSUZhTWmqVb30IXC6bRV1Vx0kJzDK0K2ssWF1c-9MmAn8tG4O7FAA2sZGUdsVl1b6EpjRibboaO4C1E8mdmhTCtKDS2C0jVrI1stOiCdxmEMg'
     * );
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
        $extra_headers = array('Content-type' => 'application/json');
        $result = json_decode($this->service->request('/people/~/shares?format=json', 'POST', json_encode($this->postParams), $extra_headers), true);
        return $result;
    }

    /**
     * @return $this
     */
    public function setDefaultPostParams()
    {
        $post = array(
            'comment' => 'Message ' . rand(),
            'content' => array(
                'title' => 'Title ' . rand(),
                'description' => '',
                'submitted-url' => 'http://www.linkedin.com?q=' . rand()
            ),
            'visibility' => array('code' => 'anyone')
        );

        $this->postParams = $post;

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setTitle($string)
    {
        $this->postParams['content']['title'] = $string;

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function setMessage($string)
    {
        $this->postParams['content']['description'] = $string;

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

        $this->postParams['content']['submitted-url'] = $url;

        return $this;
    }

    /**
     * @param $url
     * @return $this
     * @throws \Vegas\Social\Exception
     */
    public function setPhoto($url)
    {
        if (!is_string($url)) {
            throw new \Vegas\Social\Exception("setPhoto - argument is not a string");
        }

        if (!PublishHelper::validateLink($url)) {
            throw new \Vegas\Social\Exception("setPhoto - url is not valid" . $url);
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
     * @throws \Vegas\Social\Exception
     */
    public function setPostParams($postParams)
    {
        //check required params
        if (!isset($postParams['content']) || !isset($postParams['content']['title']) || !isset($postParams['content']['submitted-url'])) {
            throw new \Vegas\Social\Exception("setPostParams - required params content/title or content/submitted-url are not set");
        } else $this->setLink($postParams['content']['submitted-url']);

        $this->postParams = $postParams;

        return $this;
    }
}