<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TnDW\WebBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller {

    public function registerAction(Request $request) {
        $user = new User();

        $form = $this->createFormBuilder($user)
                ->add('username', 'text')
                ->add('email', 'text')
                ->add('password', 'text')
                ->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // perform some action, such as saving the task to the database

                return $this->redirect($this->generateUrl('tndw_web_register_success'));
            }
        }

        return $this->render('TnDWWebBundle:Register:register.html.twig', array(
                    'form' => $form->createView(),
                ));
    }

    public function successAction() {
        return $this->render('TnDWWebBundle:Register:success.html.twig', array( 
                ));
    }

}