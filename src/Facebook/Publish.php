<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 19.09.14
 * Time: 12:09
 */

namespace Vegas\Social\Facebook;



//facebook SDK4

//oauth

class Publish extends Service
{
    private $_post_params = array();

    public function postOnWall($post_params = array() , $targetUser='me')
    {
        $post_id=false;
        if(count($post_params)>0) {
            $this->setPostParamsArray($post_params);
        }

        try {
            $post_id = $this->request('POST','/'.$targetUser.'/feed',$this->_post_params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    public function postToPhotos($curl_file, $message, $targetUser='me')
    {
        $post_id = false;

        $params = array(
            'source' => $curl_file,
            'message' => $message
        );

        try {
            $post_id = $this->request('POST','/'.$targetUser.'/photos',$params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception($e->getCode(), $e->getMessage());
        }

        return $post_id;
    }

    public function deletePost($post_id)
    {
        if($post_id != '') {
            $this->request('DELETE','/'.$post_id);
            return true;
        }
        throw new \Vegas\Social\Exception('SE6', 'Not valid post id!');
    }

    public function setDefaultMessage()
    {
        $userToken = $this->_fbsession->getToken();
        $userName = $this->getUserData()->getName();

        if ($userName) {
            $this->_post_params = array(
                'access_token' => $userToken,
                'name' => $userName,
                'link' => 'http://amsterdamstandard.nl/',
                'caption' => 'Test caption',
                'message' => 'Test message',
            );
            return true;
        }
        throw new \Vegas\Social\Exception('SE4', 'post_params error');
    }

    public function setPostCaption($string)
    {
        $this->_post_params['caption'] = $string;
        return true;
    }

    public function setPostMessage($string)
    {
        $this->_post_params['message'] = $string;
        return true;
    }

    public function setPostLink($string)
    {
        if (validateLink($string)) {
            $this->_post_params['link'] = $string;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE3', 'not valid link');
    }

    public function getPostParamsArray()
    {
        return $this->_post_params;
    }

    public function setPostParamsArray($array)
    {
        if (isset($array['link'])
            && ($this->validateLink($array['link'])
                && isset($array['message']))
        ) {
            $this->_post_params = $array;
            return $this;
        }
        return false;
        //throw new \Vegas\Social\Exception('SE9', 'not valid post params');
    }

    private function validateLink($string) {
        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        preg_match($pattern, $string, $matches);
        if (count($matches) == 1 && $matches[0] == $string) return true;
        return false;
    }
}

?>