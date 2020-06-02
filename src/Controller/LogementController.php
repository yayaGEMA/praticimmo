<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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


    /**
     * @Route("/locations/", name="location_list")
     */
    public function locationList(Request $request, PaginatorInterface $paginator)
    {

        // Récupération du manager des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête pour récupérer les logements en location
        $query = $em->createQuery('SELECT a FROM App\Entity\Logement a WHERE a.type = 0 ORDER BY a.publicationDate DESC');

        // On stocke dans $pageArticles les 10 logements de la page demandée dans l'URL
        $logements = $paginator->paginate(
            $query,     // Requête de selection des logements en BDD
        );

        // On envoi les logements récupérés à la vue
        return $this->render('logements/LocationList.html.twig', [
            'logements' => $logements
        ]);

    }

    /**
     * @Route("/ventes/", name="sell_list")
     */
    public function sellList(Request $request, PaginatorInterface $paginator)
    {

        // Récupération du manager des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête pour récupérer les logements en location
        $query = $em->createQuery('SELECT a FROM App\Entity\Logement a WHERE a.type = 1 ORDER BY a.publicationDate DESC');

        // On stocke dans $pageArticles les 10 logements de la page demandée dans l'URL
        $logements = $paginator->paginate(
            $query,     // Requête de selection des logements en BDD
        );

        // On envoi les logements récupérés à la vue
        return $this->render('logements/SellList.html.twig', [
            'logements' => $logements
        ]);

    }
}