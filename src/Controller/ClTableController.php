<?php

namespace App\Controller;

use App\Entity\ClTable;
use App\Form\ClTableType;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cl/table")
 */
class ClTableController extends AbstractController
{
    /**
     * @Route("/", name="cl_table_index", methods={"GET"})
     */
    public function index(TableRepository $tableRepository): Response
    {
        return $this->render('cl_table/index.html.twig', [
            'cl_tables' => $tableRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="cl_table_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $clTable = new ClTable();
        $form = $this->createForm(ClTableType::class, $clTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clTable);
            $entityManager->flush();

            return $this->redirectToRoute('cl_table_index');
        }

        return $this->render('cl_table/new.html.twig', [
            'cl_table' => $clTable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cl_table_show", methods={"GET"})
     */
    public function show(ClTable $clTable): Response
    {
        return $this->render('cl_table/show.html.twig', [
            'cl_table' => $clTable,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cl_table_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ClTable $clTable): Response
    {
        $form = $this->createForm(ClTableType::class, $clTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cl_table_index');
        }

        return $this->render('cl_table/edit.html.twig', [
            'cl_table' => $clTable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cl_table_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ClTable $clTable): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clTable->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clTable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cl_table_index');
    }
}
