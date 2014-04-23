<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;
use ZendService\Oauth2\Client\Client;


class OauthController extends AbstractActionController
{
    public function config()
    {
       $configuration = array(
            'client' => array(
                'client_id' => '2455247748',
                'client_secret' => 'HWyEp0m7zAAf1T4IG2yPcO7RoDpWBEqf2myrbMnH9u8a8jOLb9',
                'authorization_url' => 'https://api.twitter.com/oauth/authorize',
                'access_token_url' => 'https://api.twitter.com/oauth/access_token',
                'redirect_uri' => 'http://zf-blog/oauth/callback',
                'state' => 'somerandomstate',
            ),
            'http' => array(
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ),
            ),
        );
     
       return $configuration;
    }

    public function indexAction()
    {
        $client = new Client($this->config());
        $url = $client->getAuthorizationRequestUrl();
        return $this->redirect()->toUrl($url);
    }

    public function callbackAction()
    {
        $client = new Client($this->config());
        $code = $_GET['code'];
        $accessToken = $client->getAccessToken(array(
            'code' => $code
        ));
    }
}