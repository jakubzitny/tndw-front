<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

    public function indexAction(Request $request) {
		$searchstring = $request->query->get('search');
        return $this->render('TnDWWebBundle:Search:index.html.twig', array(
        	'searchstring' => $searchstring
        ));
    }

    public function searchAction($searchstring) {
        return $this->render('TnDWWebBundle:Search:search.html.twig', array(
        	'searchstring' => $searchstring
        ));
    }

}
