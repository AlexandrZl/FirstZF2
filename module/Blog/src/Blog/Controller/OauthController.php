<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use League\OAuth2\Client\Provider\Google;


class OauthController extends AbstractActionController
{
    public function config()
    {
        $configuration = array(
            'clientId'  =>  '555205517183-0vj2f4g537giasn626kihllqkljtev74.apps.googleusercontent.com',
            'clientSecret'  =>  'LKqC8sP00InoQBO3wOFukbmf',
            'redirectUri'   =>  "http://127.0.0.3/oauth/callback",
        );
        return $configuration;
    }

    public function indexAction()
    {
        $provider = new Google($this->config());
        $provider->authorize();
    }

    public function callbackAction()
    {
        $provider = new Google($this->config());
        $t = $provider->getAccessToken('authorization_code', array('code' => $_GET['code']));
        try {
            $userDetails = $provider->getUserDetails($t);
        } catch (Exception $e) {
            die("Failed to get user details");
            }
        return array(
            'user' => $userDetails,
        );
    }
}