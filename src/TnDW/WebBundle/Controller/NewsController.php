<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsController extends Controller {

    public function indexAction() {
        return $this->render('TnDWWebBundle:News:index.html.twig');
    }
}
