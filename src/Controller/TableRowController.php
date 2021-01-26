<?php

namespace App\Controller;

use App\Entity\TableRow;
use App\Form\TableRowType;
use App\Repository\TableRowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/table/row")
 */
class TableRowController extends AbstractController
{
    /**
     * @Route("/", name="table_row_index", methods={"GET"})
     */
    public function index(TableRowRepository $tableRowRepository): Response
    {
        return $this->render('table_row/index.html.twig', [
            'table_rows' => $tableRowRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="table_row_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tableRow = new TableRow();
        $form = $this->createForm(TableRowType::class, $tableRow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tableRow);
            $entityManager->flush();

            return $this->redirectToRoute('table_row_index');
        }

        return $this->render('table_row/new.html.twig', [
            'table_row' => $tableRow,
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * @Route("/{id}", name="table_row_show", methods={"GET"})
     */
    public function show(TableRowRepository $tableRowRepository,int $id): Response
    {
        $tableRow = $tableRowRepository->findLoadingFieldsSeparately($id);
        return $this->render('table_row/show.html.twig', [
            'table_row' => $tableRow,
        ]);
    }
    
    
    
    
     public function showOld(TableRow $tableRow): Response
    {
        return $this->render('table_row/show.html.twig', [
            'table_row' => $tableRow,
        ]);
    }
   
    /**
     * @Route("/{id}/{kosztorys}", name="table_row_show_przez_kosztorys", methods={"GET"})
     */
    public function showPrzezKosztorys(TableRow $tableRow): Response
    {
        return $this->render('table_row/show.html.twig', [
            'table_row' => $tableRow,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="table_row_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TableRow $tableRow): Response
    {
        $form = $this->createForm(TableRowType::class, $tableRow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('table_row_index');
        }

        return $this->render('table_row/edit.html.twig', [
            'table_row' => $tableRow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="table_row_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TableRow $tableRow): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tableRow->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tableRow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('table_row_index');
    }
}
