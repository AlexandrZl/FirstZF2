<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;


class OauthController extends AbstractActionController
{
    public function config()
    {
       $configuration = array(
           'callbackUrl' => 'http://zf-blog/oauth/callback',
           'siteUrl' => 'http://api.twitter.com/oauth',
           'consumerKey' => 'KlIlftDYQ8nIRIGbZfe2sVtU8',
           'consumerSecret' => 'HifE8kMmdcKx3Y9QFXNkkH0HZJDEIuNdzZCz1YPHm3KdXOIIj0'
       );
     
       return $configuration;
    }
    public function indexAction()
    {
        ini_set('display_errors',1);
        session_start();
        $consumer = new Consumer($this->config());
        $token = $consumer->getRequestToken();
        $_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
        $consumer->redirect();
    }

    public function callbackAction()
    {
        session_start();
        $consumer = new Consumer($this->config());
        $token = $consumer->getAccessToken(
            $_GET,
            unserialize($_SESSION['TWITTER_REQUEST_TOKEN'])
        );
        $_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);
    }
}