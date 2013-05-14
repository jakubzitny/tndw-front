<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
    public function indexAction() {
		$parameters = $this->container->parameters;
		$ph = pg_Connect("dbname=".$parameters['database_name_trol']." user=".$parameters['database_user_trol']." password=".$parameters['database_password_trol']);
		$result = pg_query($ph, "select * from backing_distribution limit 25");
		$distros = array();
		while ($distro = pg_fetch_object($result)){
			$distros[] = $distro;
		}

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
        //$topdistros = array_slice($distros, 0, 25);
        return $this->render('TnDWWebBundle:Default:index.html.twig', array(
			'distros' => $distros,
            'news' => $topnews,
            'updates' => $topupdates,
		));
    }
}
