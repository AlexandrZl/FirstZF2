<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;
use ZendOAuth\Token\Access;
use ZendService\Twitter\Twitter;

class OauthController extends AbstractActionController
{
    public function indexAction()
    {
        ini_set('display_errors',1);
        $config = array(
            'access_token' => array(
                'token'  => '1715923526-SaO9r6YDjNVDeokyfrHfX89I3iv8NLPtqbGt7Pu',
                'secret' => '3U1wF27Vuxkr2ieLKbiDz71L9Ui9Bzra1w1ShFB9uQ7pU',
            ),
            'oauth_options' => array(
                'consumerKey' => 'EQ8wKIveUS2hZTKGAAMIH1LcS',
                'consumerSecret' => 'EWKF1IQ1sGrJs3yHQRIBVkP6KM2DjkUaMY16pM6EolZ1WI1jsh',
            ),
            'http_client_options' => array(
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ),
            ),
        );

        $twitter = new Twitter($config);
        $response = $twitter->account->verifyCredentials();
        if ($response->isError()) {
            die('Something is wrong with my credentials!');
        }
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