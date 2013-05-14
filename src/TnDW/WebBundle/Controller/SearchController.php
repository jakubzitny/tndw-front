<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

    public function indexAction(Request $request) {
		$searchstring = $request->query->get('search');
		if ($searchstring == "debian"){
			$distro = "debiangnulinux";
		} else if ($searchstring == "debiangnulinux"){
			$distro = "debiangnulinux";
		} else if (preg_match('/centos/i', $searchstring)){
			$distro = "centos";
		} else if (preg_match('/turnkey/i', $searchstring)){
			$distro = "turnkeylinux";
		} else if (preg_match('/ubuntu/i', $searchstring)){
			$distro = "ubuntu";
		} else {
			$parameters = $this->container->parameters;
			$ph = pg_Connect("dbname=".$parameters['database_name_trol']." user=".$parameters['database_user_trol']." password=".$parameters['database_password_trol']);
			$result = pg_query($ph, "select * from backing_distribution where shortname ~ '.*" . $searchstring . ".*'");
			$distrodata = pg_fetch_object($result);
			if (!$distrodata) {
				return $this->render('TnDWWebBundle:Search:index.html.twig', array(
					'searchstring' => $searchstring
				));
			} else {
				$distro = $distrodata->shortname;
			}
		}
		return $this->redirect($this->generateUrl('tndw_web_distro', array('distro' => $distro)));
    }

    public function searchAction($searchstring) {
        return $this->render('TnDWWebBundle:Search:search.html.twig', array(
        	'searchstring' => $searchstring
        ));
    }

}
