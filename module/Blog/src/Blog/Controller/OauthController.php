<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;


class OauthController extends AbstractActionController
{
    public function loginAction()
    {
        ini_set('display_errors',1);
        session_start();
        $config = array(
            'callbackUrl' => 'http://zf-blog',
            'consumerKey' => 'sSQ9DJU4rSBWx6ARQHpgA',
            'consumerSecret' => 'UsnMpyatd0FmfkWvOof4I6yyMecxlgM9QxzocGDMWA',
            'requestTokenUrl' => 'http://api.twitter.com/oauth2/request_token',
            'authorizeUrl' => 'http://api.twitter.com/oauth2/authorize',
            'accessTokenUrl' => 'http://api.twitter.com/oauth2/access_token',
        );
        $consumer = new Consumer($config);
        if (!isset($_SESSION['ACCESS_TOKEN'])) {
            if (!empty($_GET)) {
                $token = $consumer->getAccessToken($_GET, unserialize($_SESSION['REQUEST_TOKEN']));
                $_SESSION['ACCESS_TOKEN'] = serialize($token);
            } else {
                $token = $consumer->getRequestToken();
                $_SESSION['REQUEST_TOKEN'] = serialize($token);
                $consumer->redirect();
            }
        } else {
            $token = unserialize($_SESSION['ACCESS_TOKEN']);
            $_SESSION['ACCESS_TOKEN'] = null;
        }
    }
}