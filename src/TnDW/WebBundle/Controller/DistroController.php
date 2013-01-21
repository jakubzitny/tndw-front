<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DistroController extends Controller {

    public function indexAction($distro) {
        return $this->render('TnDWWebBundle:Distro:index.html.twig', array(
			'distro' => $distro
		));
    }

}
