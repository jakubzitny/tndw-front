<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
    public function indexAction() {
		$distros = $this->getDoctrine()->getRepository('TnDWWebBundle:Distro')->findAll();
        if (!$distros) {
            throw $this->createNotFoundException('No distro found..');
        }

        $news = $this->getDoctrine()->getRepository('TnDWWebBundle:Article')->findByType("article");
        if (!$news) {
            throw $this->createNotFoundException('No updates were found..');
        }

        $updates = $this->getDoctrine()->getRepository('TnDWWebBundle:Article')->findByType("update");
        if (!$updates) {
            throw $this->createNotFoundException('No updates were found..');
        }
        
        $topupdates = array_slice($updates, 0, 2);
        $topnews = array_slice($news, 0, 2);
        $topdistros = array_slice($distros, 0, 25);
        return $this->render('TnDWWebBundle:Default:index.html.twig', array(
			'distros' => $topdistros,
            'news' => $topnews,
            'updates' => $topupdates,
		));
    }
}
