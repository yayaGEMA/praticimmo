<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Logement;
use App\Form\LogementType;
use App\Entity\User;
use \DateTime;
use Symfony\Component\Form\FormError;


/**
 * @Route("/logement", name="logement_")
 */
class LogementController extends AbstractController
{

    /**
     * @Route("/ajouter", name="new_logement")
     */
    public function newLogement(Request $request)
    {
        // Création d'un logement vide
        $logement = new Logement();

        // Création d'un nouveau formulaire basé sur "LogementType", qui hydratera notre logement "$logement"
        $form = $this->createForm(LogementType::class, $logement);

        // Remplissage du traitement du formulaire avec les données POST (sous forme d'objet $request)
        $form->handleRequest($request);

        // Si le formulaire a été envoyé
        if($form->isSubmitted() && $form->isValid()){

            // Récupération de l'user actuellement connecté
            $userConnected = $this->getUser();

            // Hydratation de la publicationDate et de l'auteur de l'article
            $logement
                ->setPublicationDate(new DateTime())
                ->setAuthor($userConnected)
            ;

            // Récupération du manager général des entités
            $em = $this->getDoctrine()->getManager();

            // Persistance de l'article auprès de Doctrine
            $em->persist($logement);

            // Sauvegarder en bdd
            $em->flush();

            // TODO: ajouter un message flash de succès
            // $this->addFlash('success', 'Annonce publiée avec succès !');

            // Redirige sur la page d'accueil
            return $this->redirectToRoute('main_index');
        }

        // On appelle la vue en lui transmettant l'affichage du formulaire dans une variable "form"
        return $this->render('logements/newLogement.html.twig', [
            'form' => $form->createView()
        ]);
    }
}