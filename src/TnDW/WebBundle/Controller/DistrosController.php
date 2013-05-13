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

        return $this->render('TnDWWebBundle:Distros:distro.html.twig', array(
        	'distrodata' => $distrodata,
			'countryurl' => $countryurl,
            'news' => $topnews,
            'updates' => $topupdates,
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

	public function isDeployedAction(/*$cid*/){
		$cid = 123;
		sleep(5);
		\Predis\Autoloader::register();
		$parameters = $this->container->parameters;
		$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
		$redis->auth($parameters['redis_password']);
		# either subscribe or get by cid
		#$output = $redis->publish('cloud', 'deploy:' . $distro);
		$output = $redis->get("asd");
		# parse response if output is not nil
		$response_data = array(
			'status' => "qwe",
		);
		$response = new Response(json_encode($response_data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
		
	public function deployAction($distro){
		//
		// prenes do dalsej action ktora to deployne
		// tato len skontroluje ci user uz nedeployoval a ci je lognuty a ma prava
		// a este aj ci neni uloha prave vykonavana aby zobrazil loading
		// bude vracat message userovi (poloflash) (has been deployed a tak)
		// a tak
		//

		#$retval = null;
		#$output = array();
		#//exec('python3 rpyc_communicator deploy ' + $distro, $output, $retval);
		#$output_lastline = exec('python3 ../src/TnDW/WebBundle/ServerCommunicator/communicator.py', $output, $retval);
		#if ($retval != 0) {
		#	return "error";
		#}

		#$process = new Process('./bundles/tndwweb/ServerCommunicator/communicator.py');
		#$process = new Process('/usr/local/bin/python3 bundles/tndwweb/ServerCommunicator/communicator.py');
		#$process->setTimeout(3600);
		#$process->run();
		#if (!$process->isSuccessful()) {
		#    throw new \RuntimeException($process->getErrorOutput());
		#}
		
		#$output = $process->getOutput();
		$cid = md5(mt_rand());

		\Predis\Autoloader::register();
		$parameters = $this->container->parameters;
		$redis = new Predis\Client('tcp://'.$parameters['redis_host'].':'.$parameters['redis_port']);
		$redis->auth($parameters['redis_password']);
		$output = $redis->publish('cloud', $cid . ':0:deploy_' . $distro); // could be distro_id yet?

		$id = 123; // id from backend
		$link = "/tryout/" . $id;
		$response_data = array(
			'distro' => $distro,
			'link' => $link,
			'output' => $output,
			'cid' => $cid
		);
		$response = new Response(json_encode($response_data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

}
