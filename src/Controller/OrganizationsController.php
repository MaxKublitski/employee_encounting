<?php

namespace App\Controller;

use App\Entity\Organizations;
use App\Form\OrganizationsType;
use App\Repository\OrganizationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class OrganizationsController extends AbstractController
{
    /**
     * @Route("/", name="organizations_index", methods={"GET"})
     */
    public function index(OrganizationsRepository $organizationsRepository): Response
    {
        return $this->render('organizations/index.html.twig', [
            'organizations' => $organizationsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="organizations_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $organization = new Organizations();
        $form = $this->createForm(OrganizationsType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organization);
            $entityManager->flush();

            return $this->redirectToRoute('organizations_index');
        }

        return $this->render('organizations/new.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organizations_show", methods={"GET"})
     */
    public function show(Organizations $organization): Response
    {
        return $this->render('organizations/show.html.twig', [
            'organization' => $organization,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="organizations_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Organizations $organization): Response
    {
        $form = $this->createForm(OrganizationsType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organizations_index');
        }

        return $this->render('organizations/edit.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organizations_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Organizations $organization): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organization->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($organization);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organizations_index');
    }
}
