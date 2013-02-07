<?php

namespace TnDW\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsController extends Controller {

    public function indexAction() {

        $news = $this->getDoctrine()->getRepository('TnDWWebBundle:Article')->findByType("article");
        if (!$news) {
            throw $this->createNotFoundException('No updates were found..');
        }

        return $this->render('TnDWWebBundle:News:index.html.twig', array(
            'news' => $news
        ));
    }

}
