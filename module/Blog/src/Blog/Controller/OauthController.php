<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Facebook;
use Blog\Entity\OAuthUser;


class OauthController extends AbstractActionController
{
    public function google_config()
    {
        $configuration = array(
            'clientId'  =>  '555205517183-k9d9sgm8d20kn7s5092unjvcl79vp21l.apps.googleusercontent.com',
            'clientSecret'  =>  'Y_GSuxrvAiljNhkFXcWWTOkO',
            'redirectUri'   =>  "http://".$_SERVER['SERVER_NAME']."/oauth/callback",
        );
        return $configuration;
    }

    public function facebook_config()
    {
        $configuration = array(
            'clientId'  =>  '718727578178547',
            'clientSecret'  =>  'cba5479fe5c0cd04051f2480d6c38cdc',
            'redirectUri'   =>  "http://".$_SERVER['SERVER_NAME']."/oauth/callback",
        );
        return $configuration;
    }

    public function callback($transfer)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $users = $objectManager
            ->getRepository('\Blog\Entity\OAuthUser')
            ->findBy(array('email' => $transfer['email']));
        if (!$users){
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $user = new OAuthUser();
            $user->exchangeArray($transfer);
            $objectManager->persist($user);
            $objectManager->flush();
            $message = 'You have successfully authenticated and authorized';
            $this->flashMessenger()->addMessage($message);
        } else {
            $message = 'You have successfully authenticated';
            $this->flashMessenger()->addMessage($message);          
        }
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($transfer['email']); 
        $adapter->setCredentialValue($transfer['name']); 
        $authResult = $authService->authenticate();  
        if ($authResult->isValid()){
            $identity = $authResult->getIdentity();
            $authService->getStorage()->write($identity);
            return $this->redirect()->toRoute('auth', array('controller' => 'auth', 'action' => 'index'));
        } else {
            return $this->redirect()->toRoute('blog');
        }
    }

    public function indexAction()
    {        
    }

    public function facebookAction()
    {
        session_start();
        echo $_SESSION['mark'] = "facebook";
        $provider = new Facebook($this->facebook_config());
        $provider->authorize();
    }

    public function googleAction()
    {
        session_start();
        echo $_SESSION['mark'] = "google";
        $provider = new Google($this->google_config());
        $provider->authorize();
    }

    public function callbackAction()
    {
        session_start();
        if ($_SESSION['mark'] == "facebook"){
            $provider = new Facebook($this->facebook_config());
        } elseif ($_SESSION['mark'] == "google") {
            $provider = new Google($this->google_config());
        }
        $token = $provider->getAccessToken('authorization_code', array('code' => $_GET['code']));
        try {
            $userDetails = $provider->getUserDetails($token);
        } catch (Exception $exeption) {
            die("Failed to get user details");
            }
        $data = array(
                "name"  => $userDetails->name,
                "email" => $userDetails->email,
        );
        $this->callback($data);
    }
}