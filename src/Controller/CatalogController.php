<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Entity\Kosztorys;
use App\Form\CatalogType;
use App\Repository\CatalogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

require_once(__DIR__.'/../Service/Constants.php');

/**
 * @Route("/catalog")
 */
class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="catalog_index", methods={"GET"})
     */
    public function index(CatalogRepository $catalogRepository): Response
    {
        
        return $this->render('catalog/index.html.twig', [
            'catalogs' => $catalogRepository->findAllByName(),
        ]);
    }
    /**
     * @Route("/kosztorys/{kosztorys}", name="catalog_index_przez_kosztorys", methods={"GET"})
     */
    public function indexPrzezKosztorys(CatalogRepository $catalogRepository, Kosztorys $kosztorys): Response
    {
        
        return $this->render('catalog/index.html.twig', [
            'catalogs' => $catalogRepository->findAllByName(),
            'kosztorys_id' => $kosztorys->getId(),
        ]);
    }
    
    /**
     * @Route("/indexAjax", name="catalog_indexAjax", methods={"GET", "POST"})
     */
    public function indexAjax(Request $request,CatalogRepository $catalogRepository): Response
    {
        $params = [];
        $params['catalogs'] = $catalogRepository->findByNameDescription($request->query->get("str"));
        $kId = $request->query->get("kosztorys_id");
        if($kId)$params['kosztorys_id'] = $kId;
        return $this->render('catalog/indexAjax.html.twig', $params);
        
    }

    /**
     * @Route("/indexAjaxDebug",name="catalog_indexAjax_debug", methods={"GET", "POST"})
     */
    public function indexAjaxDebug(Request $request,CatalogRepository $catalogRepository): Response
    {
        return $this->render('catalog/index.html.twig', [
            'catalogs' => $catalogRepository->findByNameDescription($request->query->get("str")),
            ]);
    }
    /**
     * @Route("/completeDescription", name="catalog_complete_description", methods={"GET"})
     */
    public function completeDescription(CatalogRepository $catalogRepository)
    {
        $catalogs = $catalogRepository->findAllIndexedByName();
        if(reset($catalogs)->getDescription() == null)
        {
            $catalogsWithDescrip = Catalog::LoadFrom(__DIR__.'/../../resources/Norma3/Kat/',CATALOG);
            $entityManager = $this->getDoctrine()->getManager();
            foreach($catalogsWithDescrip as $cdes)
            {
                $cdes->setId(0);
                $catalog = $catalogs[$cdes->getName()];
                $catalog->setDescription($cdes->getDescription());
                $entityManager->persist($catalog);
            }
            $entityManager->flush();
        }
        return $this->render('catalog/index.html.twig', [
            // 'catalogs' => $catalogsWithDescrip,
            'catalogs' => $catalogs,
        ]);
    }

    /**
     * @Route("/new", name="catalog_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $catalog = new Catalog();
        $form = $this->createForm(CatalogType::class, $catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($catalog);
            $entityManager->flush();

            return $this->redirectToRoute('catalog_index');
        }

        return $this->render('catalog/new.html.twig', [
            'catalog' => $catalog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="catalog_show", methods={"GET"})
     */
    public function show(Catalog $catalog): Response
    {
        return $this->render('catalog/show.html.twig', [
            'catalog' => $catalog,
        ]);
    }

    /**
     * @Route("/{id}/{kosztorys_id}", name="catalog_show_przez_kosztorys", methods={"GET"})
     */
    public function showPrzezKosztorys(Catalog $catalog,$kosztorys_id): Response
    {
        return $this->render('catalog/show.html.twig', [
            'catalog' => $catalog,
            'kosztorys_id' => $kosztorys_id,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="catalog_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Catalog $catalog): Response
    {
        $form = $this->createForm(CatalogType::class, $catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('catalog_index');
        }

        return $this->render('catalog/edit.html.twig', [
            'catalog' => $catalog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="catalog_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Catalog $catalog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$catalog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($catalog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('catalog_index');
    }
}
