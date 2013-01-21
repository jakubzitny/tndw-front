<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UpdatesController extends Controller {

    public function indexAction() {
        return $this->render('TnDWWebBundle:Updates:index.html.twig');
    }
}
