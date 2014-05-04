<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use OAuth2\Client\Provider\Google;
use Blog\Form\RegistrationForm;
use Blog\Entity\OAuthUser;


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
        ini_set('display_errors',1);
        $provider = new Google($this->config());
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
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $users = $objectManager
            ->getRepository('\Blog\Entity\OAuthUser')
            ->findBy(array('email' => $data['email']));
        if (!$users){
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $post = new OAuthUser();
            $post->exchangeArray($data);
            $objectManager->persist($post);
            $objectManager->flush();
            $message = 'You have successfully authenticated and authorized';
            $this->flashMessenger()->addMessage($message);
        } else{
            $message = 'You have successfully authenticated';
            $this->flashMessenger()->addMessage($message);          
        }
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($userDetails->email); 
        $adapter->setCredentialValue($userDetails->name); 
        $authResult = $authService->authenticate();  
        if ($authResult->isValid()) 
        {
            $identity = $authResult->getIdentity();
            $authService->getStorage()->write($identity);
            return $this->redirect()->toRoute('auth', array('controller' => 'auth', 'action' => 'index'));
        }
        else {
            return $this->redirect()->toRoute('blog');
        }
    }
}



