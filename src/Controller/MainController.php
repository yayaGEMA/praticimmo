<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Logement;
use App\Form\LogementType;
use App\Entity\User;
use \DateTime;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index()
    {
        return $this->render('main/index.html.twig');
    }

}
