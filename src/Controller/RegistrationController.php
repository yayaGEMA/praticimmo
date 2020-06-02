<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Recaptcha\RecaptchaValidator;  // Importation de notre service de validation du captcha
use Symfony\Component\Form\FormError;  // Importation de la classe permettant de créer des erreurs dans les formulaires
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;


class RegistrationController extends AbstractController
{
    /**
     * Page d'inscription
     *
     * @Route("/creer-un-compte/", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, RecaptchaValidator $recaptcha): Response
    {

        // Redirige de force vers l'accueil si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('main_index');
        }

        // Création d'un nouvel objet "User"
        $user = new User();

        // Création d'un formulaire d'inscription, lié à l'objet vide créé juste avant
        $form = $this->createForm(RegistrationType::class, $user);

        // Liaison des données de requête (POST) avec le formulaire
        $form->handleRequest($request);

        // Si le formulaire a été envoyé
        if ($form->isSubmitted()) {

            // Vérification que le captcha est valide
            $captchaResponse = $request->request->get('g-recaptcha-response', null);

            // Si le captcha est null ou si il est invalide, ajout d'une erreur générale sur le formulaire (qui sera considéré comme échoué après)
            if($captchaResponse == null || !$recaptcha->verify($captchaResponse, $request->server->get('REMOTE_ADDR'))){

                // Ajout de l'erreur au formulaire
                $form->addError(new FormError('Veuillez remplir le Captcha de sécurité'));
            }

            if($form->isValid()){

                // Hydratation du nouveau compte
                $user
                    // Hashage du mot de passe
                    ->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    )
                    // Date actuelle
                    ->setRegistrationDate( new DateTime() )
                ;

                // Sauvegarde du nouveau compte grâce au manager général des entités
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();


                // Message flash de succès
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Un email vous a été envoyé pour activer votre compte.');

                // Redirection de l'utilisateur vers la page de connexion
                return $this->redirectToRoute('main_index');

            }

        }

        // Appel de la vue en envoyant le formulaire à afficher
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
