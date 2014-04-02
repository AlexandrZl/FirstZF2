<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;    
use Zend\View\Model\JsonModel;
use Blog\Form\CommentForm;
use Blog\Form\CommentFilter;  

class AjaxController extends AbstractActionController
{
    public function addcommentAction()
    {
        if ($this->request->isXmlHttpRequest()) 
        {   
            $form = new CommentForm();
            $request = $this->getRequest();
            if ($request->isPost()) 
            {
                $form->setInputFilter(new CommentFilter($this->getServiceLocator()));
                $form->setData($request->getPost());
                if ($form->isValid()) 
                {
                    $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                    $comment = new \Blog\Entity\Comment();
                    $comment->exchangeArray($form->getData());
                    $objectManager->persist($comment);
                    $comment->setUserId($_POST['id']);
                    $objectManager->flush();
                }
            }
        }

        $result = new JsonModel(array(
        'response' => "success",
            'success'=>true,
        ));  

        return array(
            'result' => $result,
            );   
    }
}