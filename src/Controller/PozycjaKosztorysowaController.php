<?php

namespace App\Controller;

use App\Entity\PozycjaKosztorysowa;
use App\Entity\TableRow;
use App\Entity\Kosztorys;
use App\Form\PozycjaKosztorysowaType;
use App\Repository\PozycjaKosztorysowaRepository;
use App\Repository\TableRowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pozycja/kosztorysowa")
 */
class PozycjaKosztorysowaController extends AbstractController
{
    /**
     * @Route("/", name="pozycja_kosztorysowa_index", methods={"GET"})
     */
    public function index(PozycjaKosztorysowaRepository $pozycjaKosztorysowaRepository): Response
    {
        return $this->render('pozycja_kosztorysowa/index.html.twig', [
            'pozycja_kosztorysowas' => $pozycjaKosztorysowaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pozycja_kosztorysowa_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pozycjaKosztorysowa = new PozycjaKosztorysowa();
        $form = $this->createForm(PozycjaKosztorysowaType::class, $pozycjaKosztorysowa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pozycjaKosztorysowa);
            $entityManager->flush();

            return $this->redirectToRoute('pozycja_kosztorysowa_index');
        }

        return $this->render('pozycja_kosztorysowa/new.html.twig', [
            'pozycja_kosztorysowa' => $pozycjaKosztorysowa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/{table_row_id}/{kosztorys}", name="pozycja_kosztorysowa_new_przez_kosztorys", methods={"GET","POST"})
     */
    public function newPrzezKosztorys(Request $request,int $table_row_id, Kosztorys $kosztorys, TableRowRepository $trRep): Response
    {
        $pozycjaKosztorysowa = new PozycjaKosztorysowa();
        $pozycjaKosztorysowa->setKosztorys($kosztorys);
        $table_row = $trRep->findLoadingFieldsSeparately($table_row_id);
        $pozycjaKosztorysowa->setPodstawaNormowa($table_row);
        $form = $this->createForm(PozycjaKosztorysowaType::class, $pozycjaKosztorysowa);
        $form->handleRequest($request);
        $koszId = $kosztorys->getId();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($pozycjaKosztorysowa);
            // $entityManager->flush();
            $obmiar = $pozycjaKosztorysowa->getObmiar();
            $conn = $entityManager->getConnection();
            $sql = "INSERT INTO pozycja_kosztorysowa (kosztorys_id,podstawa_normowa_id,obmiar) VALUES ($koszId,$table_row_id,$obmiar)";
            $conn->executeQuery($sql);
            // return $sql;
            return $this->redirectToRoute('kosztorys_show',['id'=>$kosztorys->getId()]);
        }

        return $this->render('pozycja_kosztorysowa/new.html.twig', [
            'pozycja_kosztorysowa' => $pozycjaKosztorysowa,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="pozycja_kosztorysowa_show", methods={"GET"})
     */
    public function show(PozycjaKosztorysowa $pozycjaKosztorysowa): Response
    {
        return $this->render('pozycja_kosztorysowa/show.html.twig', [
            'pozycja_kosztorysowa' => $pozycjaKosztorysowa,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pozycja_kosztorysowa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PozycjaKosztorysowa $pozycjaKosztorysowa): Response
    {
        $form = $this->createForm(PozycjaKosztorysowaType::class, $pozycjaKosztorysowa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pozycja_kosztorysowa_index');
        }

        return $this->render('pozycja_kosztorysowa/edit.html.twig', [
            'pozycja_kosztorysowa' => $pozycjaKosztorysowa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pozycja_kosztorysowa_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PozycjaKosztorysowa $pozycjaKosztorysowa): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pozycjaKosztorysowa->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pozycjaKosztorysowa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pozycja_kosztorysowa_index');
    }
}
