<?php

namespace App\Controller;

use App\Entity\Kosztorys;
use App\Form\KosztorysType;
use App\Repository\CatalogRepository;
use App\Repository\KosztorysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/kosztorys")
 */
class KosztorysController extends AbstractController
{
    /**
     * @Route("/", name="kosztorys_index", methods={"GET"})
     */
    public function index(KosztorysRepository $kosztorysRepository): Response
    {
        return $this->render('kosztorys/index.html.twig', [
            'kosztorys' => $kosztorysRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="kosztorys_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $kosztory = new Kosztorys();
        $form = $this->createForm(KosztorysType::class, $kosztory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($kosztory);
            $entityManager->flush();

            return $this->redirectToRoute('kosztorys_index');
        }

        return $this->render('kosztorys/new.html.twig', [
            'kosztory' => $kosztory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="kosztorys_show", methods={"GET"})
     */
    public function show(KosztorysRepository $kosztorysRepository, int $id): Response
    {
        $listaCenId = $kosztorysRepository->getListaCenIdDlaKosztorysu($id);
        $roboczogodzina = $kosztorysRepository->getRoboczogodzina($id);
        $symboleIopisy = $kosztorysRepository->getIdNumberDescriptionsNames_PozycjiKosztorysowychDlaKosztorysu($id);
        $wartosciIceny = $kosztorysRepository->getPkIdWartoscCenaDlaKosztorysIlistaCen($id,$listaCenId);
        $kosztorys = new Kosztorys;
        $kosztorys->setRoboczogodzina($roboczogodzina);
        $kosztorys->setId($id);
        $kosztorys->ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($symboleIopisy,$wartosciIceny);

        // $kosztorys = $kosztorysRepository->findLoadingFieldsSeparately($id);
        return $this->render('kosztorys/show.html.twig', [
            'kosztory' => $kosztorys,
        ]);
    }

    /**
     * @Route("/{id}/forTest", name="kosztorys_show_for_test", methods={"GET"})
     */
    public function showForTest(KosztorysRepository $kosztorysRepository, int $id): Response
    {
        $dane = [];
        $widokTablicy = function($raw,&$dane)
        {
            foreach($raw as $rekord){
                $sRekord = '[';
                foreach($rekord as $k => $v)
                {
                    $sRekord .= "'".$k."'=>";
                    $sRekord .= is_numeric($v) ? $v :"'".$v."'";
                    $sRekord .=",";
                }
                $sRekord = rtrim($sRekord,',');
                $sRekord .= ']';
                $dane[] = $sRekord;
            }
        };
        $listaCenId = $kosztorysRepository->getListaCenIdDlaKosztorysu($id);
        $widokTablicy($kosztorysRepository->getIdNumberDescriptionsNames_PozycjiKosztorysowychDlaKosztorysu($id),$dane);
        $widokTablicy($kosztorysRepository->getPkIdWartoscCenaDlaKosztorysIlistaCen($id,$listaCenId),$dane);

        return $this->render('kosztorys/showForTest.html.twig', [
            'dane' => $dane,
        ]);
    }

    
    public function showOld(Kosztorys $kosztory): Response
    {
        return $this->render('kosztorys/show.html.twig', [
            'kosztory' => $kosztory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="kosztorys_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Kosztorys $kosztory): Response
    {
        $form = $this->createForm(KosztorysType::class, $kosztory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('kosztorys_index');
        }

        return $this->render('kosztorys/edit.html.twig', [
            'kosztory' => $kosztory,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{id}/catalog/index", name="kosztorys_catalog_index", methods={"GET","POST"})
     */
    public function pokazKatalogi(Request $request, Kosztorys $kosztory, CatalogRepository $catalogRepository)
    {
        return $this->render('catalog/index.html.twig', [
            'catalogs' => $catalogRepository->findAllByName(),
            ]);
    }

    /**
     * @Route("/{id}", name="kosztorys_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Kosztorys $kosztory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$kosztory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($kosztory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('kosztorys_index');
    }
}
