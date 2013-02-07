<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdatesController extends Controller {

    public function indexAction() {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $test = 'test';
        } else {
            $test = 'aha!';
        }

        $updates = $this->getDoctrine()->getRepository('TnDWWebBundle:Article')->findByType("update");
        if (!$updates) {
            throw $this->createNotFoundException('No updates were found..');
        }

        return $this->render('TnDWWebBundle:Updates:index.html.twig', array(
                    'updates' => $updates
                ));
    }

}
