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
           'consumerKey' => '03pANGHR0BsEh0lHd67O6htga',
           'consumerSecret' => 'HWyEp0m7zAAf1T4IG2yPcO7RoDpWBEqf2myrbMnH9u8a8jOLb9'
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