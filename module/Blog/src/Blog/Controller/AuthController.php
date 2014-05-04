<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Blog\Entity\User; 
use Blog\Form\AuthForm; 
use Blog\Form\LoginFilter;     


class AuthController extends AbstractActionController
{
    public function loginAction()
    {
        $form = new AuthForm();
        $form->get('submit')->setValue('Login');
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) 
        {
            return $this->redirect()->toRoute('auth', array('controller' => 'auth', 'action' => 'index'));
        }   
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $form->setInputFilter(new LoginFilter($this->getServiceLocator()));
            $form->setData($request->getPost());
            if ($form->isValid()) 
            {
                $data = $form->getData();
                //$authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                $adapter = $authService->getAdapter();
                $adapter->setIdentityValue($data['email']); 
                $adapter->setCredentialValue($data['password']); 
                $authResult = $authService->authenticate();  
                if ($authResult->isValid()) 
                {
                    $identity = $authResult->getIdentity();
                    $authService->getStorage()->write($identity);
                    return $this->redirect()->toRoute('auth', array('controller' => 'auth', 'action' => 'index'));
                } 
                else
                {
                    $message = 'Your authentication credentials are not valid';
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
        }
        return array('form'  => $form);
    }
    
    public function logoutAction()
    {
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) 
        {
            $identity = $auth->getIdentity();
        }
        $auth->clearIdentity();    
        return $this->redirect()->toRoute('home');
    }

     public function indexAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $users = $em
            ->getRepository('Blog\Entity\OAuthUser')
            ->findAll();

        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) 
        {
            $auth_user = $auth->getIdentity();
        }
        return array(
            'users' => $users,
            'auth_user' => $auth_user,
        );
    }
}