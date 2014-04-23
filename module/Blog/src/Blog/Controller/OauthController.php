<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZendOAuth\Consumer;
use League\OAuth2\Client\Provider\Google;


class OauthController extends AbstractActionController
{
    public function indexAction()
    {
        $provider = new Google(array(
            'clientId'  =>  '555205517183-0vj2f4g537giasn626kihllqkljtev74.apps.googleusercontent.com',
            'clientSecret'  =>  'LKqC8sP00InoQBO3wOFukbmf',
            'redirectUri'   =>  "http://127.0.0.1/oauth/index",
        ));

        $provider->authorize();
    }
}