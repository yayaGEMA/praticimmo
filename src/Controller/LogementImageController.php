<?php

namespace App\Controller;

use App\Entity\LogementImage;
use App\Form\LogementImage1Type;
use App\Repository\LogementImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logement/image")
 */
class LogementImageController extends AbstractController
{
    /**
     * @Route("/", name="logement_image_index", methods={"GET"})
     */
    public function index(LogementImageRepository $logementImageRepository): Response
    {
        return $this->render('logement_image/index.html.twig', [
            'logement_images' => $logementImageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="logement_image_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $logementImage = new LogementImage();
        $form = $this->createForm(LogementImage1Type::class, $logementImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($logementImage);
            $entityManager->flush();

            return $this->redirectToRoute('logement_image_index');
        }

        return $this->render('logement_image/new.html.twig', [
            'logement_image' => $logementImage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="logement_image_show", methods={"GET"})
     */
    public function show(LogementImage $logementImage): Response
    {
        return $this->render('logement_image/show.html.twig', [
            'logement_image' => $logementImage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="logement_image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, LogementImage $logementImage): Response
    {
        $form = $this->createForm(LogementImage1Type::class, $logementImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logement_image_index');
        }

        return $this->render('logement_image/edit.html.twig', [
            'logement_image' => $logementImage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="logement_image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, LogementImage $logementImage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logementImage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($logementImage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('logement_image_index');
    }
}
