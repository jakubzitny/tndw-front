<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$topdistros = array('mint', 'ubuntu', 'debian', 'fedora', 'greenie', 'oracle', 'redhat', 'centos', 'mandriva', 'freebsd',
			'puppy', 'damnsmall', 'openbsd', 'bodhi', 'arch', 'yellowdog', 'xubuntu');
        return $this->render('TnDWWebBundle:Default:index.html.twig', array(
			'distros' => $topdistros
		));
    }
}
