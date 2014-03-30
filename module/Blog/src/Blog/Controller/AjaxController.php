<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;    
use Zend\View\Model\JsonModel;
use Blog\Form\CommentForm;

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
                $form->setData($request->getPost());
                if ($form->isValid()) 
                {
                    $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                    $comment = new \Blog\Entity\Comment();
                    $comment->setComment($_POST['comment']);
                    $objectManager->persist($comment);
                    $comment->setUserId( $_POST['id']);
                    $objectManager->flush();
                }
            }
        }

        $result = new JsonModel(array(
        'response' => "Success send comment",
            'success'=>true,
        ));  

        return array(
            'result' => $result,
            );   
    }
}