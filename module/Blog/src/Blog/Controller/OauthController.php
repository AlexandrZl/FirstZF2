<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;


class OauthController extends AbstractActionController
{
    public function loginAction()
    {
        $config = array(
            'callbackUrl' => 'http://zf-blog',
            'siteUrl' => 'http://api.twitter.com/oauth2',
            'consumerKey' => "",
            'consumerSecret' => "",
        );

        $consumer = new Consumer($config);
        $token = $consumer->getRequestToken(); 
        $this->session->twitter_request_token = serialize($token); 
        $consumer->redirect();
    }
    
}