<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

}
