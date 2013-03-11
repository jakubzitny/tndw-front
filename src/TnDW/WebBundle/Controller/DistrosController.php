<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Predis;

class DistrosController extends Controller {

    public function indexAction() {

        $distros = $this->getDoctrine()->getRepository('TnDWWebBundle:Distro')->findAll();
        if (!$distros) {
            throw $this->createNotFoundException('No distro found..');
        }

        return $this->render('TnDWWebBundle:Distros:index.html.twig', array(
                    'distros' => $distros
                ));
    }
    
    public function distroAction($distro){
        
        $distrodata = $this->getDoctrine()->getRepository('TnDWWebBundle:Distro')->findOneBy(array('shortname' => $distro));
        if (!$distrodata) {
            $distrodata = $this->getDoctrine()->getRepository('TnDWWebBundle:Distro')->findOneBy(array('name' => $distro));
            if (!$distrodata){
                throw $this->createNotFoundException('No distro found..');
            }
        }
        
        return $this->render('TnDWWebBundle:Distros:distro.html.twig', array(
                    'distro' => $distrodata,
                ));
    }

	public function isDeployedAction(/*$cid*/){
		$cid = 123;
		sleep(5);
		\Predis\Autoloader::register();
		$redis = new Predis\Client('tcp://localhost:6379');
		$redis->auth('lcPi2ouVfjDQzRGA7ZUcC2Wc9yQQRRh');
		# either subscribe or get by cid
		#$output = $redis->publish('cloud', 'deploy:' . $distro);
		$output = $redis->get("asd");
		# parse response if output is not nil
		$response_data = array(
			'status' => $output,
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
		$cid = 123;

		\Predis\Autoloader::register();
		$redis = new Predis\Client('tcp://localhost:6379');
		$redis->auth('lcPi2ouVfjDQzRGA7ZUcC2Wc9yQQRRh');
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
