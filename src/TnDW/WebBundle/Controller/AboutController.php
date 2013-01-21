<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller {

    public function indexAction() {
        return $this->render('TnDWWebBundle:About:index.html.twig');
    }
}
