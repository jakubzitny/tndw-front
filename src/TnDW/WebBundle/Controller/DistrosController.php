<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DistrosController extends Controller {

    public function indexAction() {
        return $this->render('TnDWWebBundle:Distros:index.html.twig', array(
		));
    }

}
