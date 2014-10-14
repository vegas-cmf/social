<?php

namespace Vegas\Social\LinkedIn;

use Vegas\Social\PublishInterface;

class Publish extends Service implements PublishInterface
{

    public function postPhoto($curl_file, $message)
    {
        echo curl_getinfo($curl_file, CURLINFO_EFFECTIVE_URL);
        die();
        $post = array(
            'comment' => 'Test message' . rand(),
            'content' => array(
                'title' => 'Test' . rand(),
                'submittedUrl' => 'http://amsterdamstandard.com?q=' . rand(),
                'description' => 'Linkedin' . rand(),
                'submitted-image-url' => curl_getinfo($curl_file, CURLINFO_EFFECTIVE_URL)
            ),
            'visibility' => array('code' => 'anyone')
        );

        $extra_headers = array('Content-type' => 'application/json');

        $result = json_decode($this->service->request('/people/~/shares?format=json', 'POST', json_encode($post), $extra_headers), true);

        return $result;
    }

    public function deletePost($post_id)
    {
        //2014.10.14 deleting shares is currently not supported in LinkedIn Share API
        return false;
    }

    public function postOnWall($params_array_or_message)
    {
        $post = array(
            'comment' => 'Test message' . rand(),
            'content' => array(
                'title' => 'Test' . rand(),
                'description' => 'Linkedin',
                'submittedUrl' => 'http://amsterdamstandard.com?q=' . rand()
            ),
            'visibility' => array('code' => 'anyone')
        );

        $extra_headers = array('Content-type' => 'application/json');

        $result = json_decode($this->service->request('/people/~/shares?format=json', 'POST', json_encode($post), $extra_headers), true);

        return $result;
    }
}