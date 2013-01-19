<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TnDWWebBundle:Default:index.html.twig');
    }
}
