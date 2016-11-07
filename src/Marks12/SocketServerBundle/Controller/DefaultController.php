<?php

namespace Marks12\SocketServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('Marks12SocketServerBundle:Default:index.html.twig');
    }
}
