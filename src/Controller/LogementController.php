<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Logement;
use App\Form\LogementType;
use App\Form\EditLogementType;
use App\Form\EditPhotoType;
use App\Entity\User;
use \DateTime;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route("/logement", name="logement_")
 */
class LogementController extends AbstractController
{

    /**
     * @Route("/ajouter/", name="new_logement")
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

            // Extraction de l'objet de la photo envoyée dans le formulaire
            $mainPhoto = $form->get('main_photo')->getData();

            // Création d'un nouveau nom aléatoire pour la photo avec son extension (récupérée via la méthode guessExtension() )
            $newFileName = md5(time() . rand() . uniqid() ) . '.' . $mainPhoto->guessExtension();

            // Déplacement de la photo dans le dossier que l'on avait paramétré dans le fichier services.yaml, avec le nouveau nom qu'on lui a généré
            $mainPhoto->move(
                $this->getParameter('app.user.photo.directory'),     // Emplacement de sauvegarde du fichier
                $newFileName    // Nouveau nom du fichier
            );

            // Récupération de l'user actuellement connecté
            $userConnected = $this->getUser();

            $logement->setMainPhoto($newFileName);

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
            $this->addFlash('success', 'Annonce publiée avec succès !');

            // Redirige sur la page d'accueil
            return $this->redirectToRoute('main_index');
        }

        // On appelle la vue en lui transmettant l'affichage du formulaire dans une variable "form"
        return $this->render('logements/newLogement.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Modification des photos par Vich
     *
     * @Route("/{id}/edit", name="logement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Logement $logement): Response
    {
        $form = $this->createForm(LogementType::class, $logement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logementImages = $logement->getLogementImages();
            foreach($logementImages as $key => $logementImage){
                $logementImage->setLogement($logement);
                $logementImages->set($key,$logementImage);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($logement);
            $entityManager->flush();

            return $this->redirectToRoute('main_index', [
                'id' => $logement->getId(),
            ]);
        }

        return $this->render('logement/edit.html.twig', [
            'logement' => $logement,
            'form' => $form->createView(),
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

    /**
     * Page d'affichage d'une annonce en détail
     *
     * @Route("/{slug}/", name="logement_view")
     */
    public function logementView(Logement $logement, Request $request){

        // Récupération de l'user actuellement connecté
        $userConnected = $this->getUser();

        // Appel de la vue en lui envoyant le logement
        return $this->render('logements/logementView.html.twig', [
            'logement' => $logement,
            'user' => $userConnected,
        ]);
    }

    /**
     * Page user permettant de modifier une annonce existant via son slug passé dans l'url
     *
     * @Route("/{slug}/modifier/", name="logement_edit")
     * @Security("logement.isAuthor(user)")
     */
    public function logementEdit(Logement $logement, request $request)
    {

        // Création du formulaire de modification (c'est le même que le formulaire permettant de créer un nouveau logement, sauf qu'il sera déjà rempli avec les données de "$logement")
        $form = $this->createForm(EditLogementType::class, $logement);

        // Liaison des données de requête (POST) avec le formulaire
        $form->handleRequest($request);

        // Si le formulaire est envoyé et n'a pas d'erreur
        if($form->isSubmitted() && $form->isValid()){

            // Sauvegarde des changements faits via le manager général des entités
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Message flash de type "success"
            $this->addFlash('success', 'Annonce modifiée avec succès !');

            // Redirection vers la page du bien modifié
            return $this->redirectToRoute('logement_logement_view', ['slug' => $logement->getSlug()]);

        }

        // Appel de la vue en lui envoyant le formulaire à afficher
        return $this->render('logements/editLogement.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * Page user permettant de modifier une photo
     *
     * @Route("/{slug}/modifier-photo/", name="photo_edit")
     * @Security("logement.isAuthor(user)")
     */
    public function photoEdit(Logement $logement, request $request)
    {

        // Création du formulaire de modification (c'est le même que le formulaire permettant de créer un nouveau logement, sauf qu'il sera déjà rempli avec les données de "$logement")
        $form = $this->createForm(EditPhotoType::class);

        // Liaison des données de requête (POST) avec le formulaire
        $form->handleRequest($request);

        // Si le formulaire est envoyé et n'a pas d'erreur
        if($form->isSubmitted() && $form->isValid()){

            if($this->getLogement()->getMainPhoto() != null){

                // Suppression de l'ancienne photo
                unlink($this->getParameter('app.user.photo.directory') . $this->getLogement()->getMainPhoto());
            }

            do{

                // guessExtension() permet de récupérer la vrai extension du fichier, calculée par rapport à son vrai type MIME
                $newFileName = md5( $this->getLogement()->getId() . random_bytes(100) ) . '.' . $mainPhoto->guessExtension();

            } while(file_exists($this->getParameter('app.user.photo.directory') . $newFileName));

            // Changement du nom de la photo stockée dans l'utilisateur connecté
            $this->getLogement()->setMainPhoto($newFileName);

            // Sauvegarde des changements faits via le manager général des entités
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $mainPhoto->move(
                $this->getParameter('app.user.photo.directory'),    // Emplacement cible du déplacement
                $newFileName        // Nouveau nom du fichier déplacé
            );

            // Message flash de type "success"
            $this->addFlash('success', 'Photo modifiée avec succès !');

            // Redirection vers la page du bien modifié
            return $this->redirectToRoute('logement_view', ['slug' => $logement->getSlug()]);

        }

        // Appel de la vue en lui envoyant le formulaire à afficher
        return $this->render('logements/editPhoto.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}