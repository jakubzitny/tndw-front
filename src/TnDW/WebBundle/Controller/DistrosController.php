<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Predis;

class DistrosController extends Controller {

    public function indexAction() {
		$ph = pg_Connect("dbname=troller6 user=postgres password=root");
	  	$result = pg_query($ph, "select * from backing_distribution");
		$distros = array();	
		while($distro = pg_fetch_object($result))
			$distros[] = $distro;
		pg_free_result($result);
		pg_close($ph);

        return $this->render('TnDWWebBundle:Distros:index.html.twig', array(
        	'distros' => $distros
        ));
    }
    
    public function distroAction($distro){

        
		# Fetch troller distro data
		$parameters = $this->container->parameters;
		$ph = pg_Connect("dbname=".$parameters['database_name_trol']." user=".$parameters['database_user_trol']." password=".$parameters['database_password_trol']);
	  	$result = pg_query($ph, "select * from backing_distribution where shortname ilike '" . $distro . "'");
		#todo check for found
    	$distrodata = pg_fetch_object($result);
		if (!$distrodata) {
			throw $this->createNotFoundException('This distribution does not exist.');
		}
		# os_type
		$distrodata->os_type = pg_fetch_object(pg_query($ph, "select * from backing_ostype where id = " . $distrodata->os_type_id))->name;

		$tofetch = array("homepage", "mailing_lists", "user_forums", "documentations", "screenshots", "download_mirrors", "bug_trackers", "related_websites", "reviews");
		foreach ($tofetch as $param) {
			if (!isset($distrodata->$param) or 
				$distrodata->$param == "[]" or
				$distrodata->$param == "\"\"") continue;
			$links = pg_query($ph, "select * from backing_link where id in (" . substr($distrodata->$param, 1, -1) . ")");
			$distrodata->links[$param] = array();
			while($link = pg_fetch_object($links)){
				$distrodata->links[$param][] = $link;
			}
			pg_free_result($links);
		}

		# based ons
	  	$result = pg_query($ph, "select backing_basedondistro.name, backing_basedondistro.shortname, distribution_id from (" .
								"	backing_distribution_based_ons join backing_basedondistro " .
								"	on backing_distribution_based_ons.basedondistro_id = backing_basedondistro.id" .
								") join backing_distribution " .
									"on backing_distribution.shortname = backing_basedondistro.shortname " .
								"where backing_distribution_based_ons.distribution_id = " . $distrodata->id);
		$distrodata->basedons = array();
		while ($basedon = pg_fetch_object($result)){
			$distrodata->basedons[] = $basedon;
		}

		# inspireds
	  	$result = pg_query($ph, "select name, shortname, id from backing_distribution where id in (" .
								"select distinct(distribution_id) from backing_distribution_based_ons where basedondistro_id = (" .
								"	select id from backing_basedondistro where shortname = '" . $distrodata->shortname . "'))");
		$distrodata->inspireds = array();
		while ($inspired = pg_fetch_object($result)){
			$distrodata->inspireds[] = $inspired;
		}

		# screenshots
	  	$result = pg_query($ph, "select * from screenshots_fetch_sdscreenshot " .
								"where distro_id = " . $distrodata->sdscreenshot_id);
		$distrodata->screenshots = array();
		while ($shot = pg_fetch_object($result)){
			$distrodata->screenshots[] = $shot;
		}
		$distrodata->screenshot_default = $distrodata->screenshots[intval(count($distrodata->screenshots)/2)];


		# architectures
	  	$result = pg_query($ph, "select * from backing_distribution_architectures" .
								" join backing_architecture " .
								"on backing_distribution_architectures.architecture_id = backing_architecture.id " .
								"where distribution_id = " . $distrodata->id);
		$distrodata->architectures = array();
		while ($architecture = pg_fetch_object($result)){
			$distrodata->architectures[] = $architecture;
		}

		# desktops
	  	$result = pg_query($ph, "select * from backing_distribution_desktops" .
								" join backing_desktop " .
								"on backing_distribution_desktops.desktop_id = backing_desktop.id " .
								"where distribution_id = " . $distrodata->id);
		$distrodata->desktops = array();
		while ($desktop = pg_fetch_object($result)){
			$distrodata->desktops[] = $desktop;
		}

		# categories
	  	$result = pg_query($ph, "select * from backing_distribution_categories" .
								" join backing_category " .
								"on backing_distribution_categories.category_id = backing_category.id " .
								"where distribution_id = " . $distrodata->id);
		$distrodata->categories = array();
		while ($category = pg_fetch_object($result)){
			$distrodata->categories[] = $category;
		}

		# deployable
		$result = pg_query($ph, "select * from listener_deployplatforms " .
								"where shortname = '" . $distrodata->shortname . "'");
		$deployable = pg_fetch_object($result);
		if ($deployable) {
			$deployable = True;
		} else {
			$deployable = False;
		}
		pg_close($ph);

		# countryurl
		$countryurl = $this->getCountryOutline($distrodata->origin);

		# news and updates
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

		# deployment
		$user = $this->getUser();
		if (!$user) {
			$deployStatus = false;
		} else {
			\Predis\Autoloader::register();
			$parameters = $this->container->parameters;
			$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
			$redis->auth($parameters['redis_password']);
			$cid = $redis->get($user->getId() . '.' . $distrodata->shortname);
			if (!$cid) {
				$deployStatus = false;
			} else {
				$deployStatus = true;
			}
		}

        return $this->render('TnDWWebBundle:Distros:distro.html.twig', array(
        	'distrodata' => $distrodata,
			'countryurl' => $countryurl,
            'news' => $topnews,
            'updates' => $topupdates,
			'deployStatus' => $deployStatus,
			'deployable' => $deployable,
        ));
    }

	private function getCountryOutline($origin){
		if (preg_match('/,/', $origin)) return "";
		else if (preg_match('/Isle\ of\ Man/i', $origin)) return "";
		else if (preg_match('/usa/i', $origin)) return "http://0.tqn.com/d/geography/1/0/9/H/usa3.jpg";
		$country = strtolower($origin);
		$ch = curl_init("http://geography.about.com/library/blank/blx" . $country . ".htm");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$source = curl_exec($ch);
		$retval = preg_match('/http:\/\/0\.tqn\.com\/d\/geography.*jpg/', $source, $result);
		curl_close($ch);
		if ($retval	== FALSE)
			return "";
		return substr($result[0], 0, strpos($result[0], '"'));
	}

	public function isDeployedAction($distro){
		$user = $this->getUser();
		if (!$user) {
			$response_data = array(
				'response' => -1,
				'message' => 'not logged in'
			);
		} else {
			\Predis\Autoloader::register();
			$parameters = $this->container->parameters;
			$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
			$redis->auth($parameters['redis_password']);
			$cid = $redis->get($user->getId() . '.' . $distro);
			if (!$cid) {
				$response_data = array(
					'response' => -1,
					'message' => 'error'
				);
			} else {
				$state = $redis->get($cid);
				if (preg_match('/deployed_.*/', $state)) {
					$data_raw = preg_split('/_/', $state);
					$data = preg_split('/:/', $data_raw[1]);
					$response_data = array(
						'response' => 0,
						'message' => 'deployed',
						'username' => $data[0],
						'password' => $data[1],
						'ip' => $data[2],
						'vnc_host' => $data[3],
						'vnc_port' => $data[4],
						'vnc_password' => $data[5],
					);
				} else if ($state == 'failed') {
					$response_data = array(
						'response' => -1,
						'message' => 'failed'
					);
				} else {
					$response_data = array(
						'response' => 1,
						'message' => $state
					);
				}
			}
		}
		$response = new Response(json_encode($response_data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

	public function deployAction($distro){
		//
		// len skontroluje ci user uz nedeployoval a ci je lognuty a ma prava
		// a este aj ci neni uloha prave vykonavana aby zobrazil loading
		// bude vracat message userovi (poloflash) (has been deployed a tak)
		// a tak
		//

		$user = $this->getUser();
		if (!$user) {
			$response_data = array(
				'response' => -1,
				'message' => 'not logged in'
			);
		} else {
			\Predis\Autoloader::register();
			$parameters = $this->container->parameters;
			$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
			$redis->auth($parameters['redis_password']);
			$cid = $redis->get($user->getId() . '.' . $distro);
			if (!$cid) {
				$cid = md5(mt_rand());
				$redis->set($user->getId() . '.' . $distro, $cid);
				$redis->set($cid, 'deploying');
				$output = $redis->publish('cloud', $cid . ':0:deploy_' . $user->getId() . '_' .$distro);
			}
			$response_data = array(
				'response' => 0,
				'message' => 'loading',
				'distro' => $distro,
			);
		}
		$response = new Response(json_encode($response_data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

    public function vncAction($distro){
		if ($this->container->has('profiler')) {
			$this->container->get('profiler')->disable();
		}
		$user = $this->getUser();
		if (!$user) {
			return $this->redirect($this->generateUrl('tndw_web_distro', array('distro' => $distro)));
		}
		\Predis\Autoloader::register();
		$parameters = $this->container->parameters;
		$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
		$redis->auth($parameters['redis_password']);
		$cid = $redis->get($user->getId() . '.' . $distro);
		if (!$cid) {
			return $this->redirect($this->generateUrl('tndw_web_distro', array('distro' => $distro)));
		} else {
			$state = $redis->get($cid);
			if (preg_match('/deployed_.*/', $state)) {
				$data_raw = preg_split('/_/', $state);
				$data = preg_split('/:/', $data_raw[1]);
				$username = $data[0];
				$password = $data[1];
				$vnc_host = $data[3];
				$vnc_port = $data[4];
				$vnc_password = $data[5];
			} else {
				return $this->redirect($this->generateUrl('tndw_web_distro', array('distro' => $distro)));
			}
		}

        return $this->render('TnDWWebBundle:Distros:flashlight.html.twig', array(
			'username' => $username,
			'password' => $password,
			'vnc_host' => $vnc_host,
			'vnc_port' => $vnc_port,
			'vnc_password' => $vnc_password,
        ));
	}

}
