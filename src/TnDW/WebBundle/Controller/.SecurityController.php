<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller {

	public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get( SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('TnDWWebBundle:Security:login.html.twig', array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'facebookAppId' => $this->container->getParameter('facebookAppId'),
                'facebook'      => $this->get('facebook'),
        ));
    }
    
    public function fbLoginAction() {
        $facebook = $this->get('facebook');
            if ( $facebookId = $facebook->getUser() ){
                $userProfile = $facebook->api('/me');
                // do whatever you want with it.
                var_dump($userProfile);
            } else {
                // not authenticated.
                return $this->redirect($facebook->getLoginUrl(array('redirect_uri' => '...')));
            }
    }

	public function registerAction() {
		return $this->render('TnDWWebBundle:Security:register.html.twig', array(
			
		));
	}
}
