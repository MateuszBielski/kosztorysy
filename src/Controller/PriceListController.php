<?php

namespace App\Controller;

use App\Entity\PriceList;
use App\Form\PriceListType;
use App\Repository\CirculationNameAndUnitRepository;
use App\Repository\ItemPriceRepository;
use App\Repository\PriceListRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/price/list")
 */
class PriceListController extends AbstractController
{
    /**
     * @Route("/", name="price_list_index", methods={"GET"})
     */
    public function index(PriceListRepository $priceListRepository): Response
    {
        return $this->render('price_list/index.html.twig', [
            'price_lists' => $priceListRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="price_list_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $priceList = new PriceList();
        $form = $this->createForm(PriceListType::class, $priceList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($priceList);
            $entityManager->flush();

            return $this->redirectToRoute('price_list_index');
        }

        return $this->render('price_list/new.html.twig', [
            'price_list' => $priceList,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/newRandom", name="price_list_new_random", methods={"GET","POST"})
     */
    public function newRandom(Request $request, CirculationNameAndUnitRepository $cnur): Response
    {
        $priceList = new PriceList();
        
        $priceList->setName('ceny losowe'.(new \DateTime('now'))->format('Y-m-d'));
        $form = $this->createForm(PriceListType::class, $priceList);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //$circulations = array_slice($cnur->findAll(),0,16007);//16007ok, 17000nieok, 18000nieok, 20007 nieok, 19999 nieok
            $circulations = $cnur->findAll();
            $prices = $priceList->CreateRandomPrices($circulations,0.95,301.34);
            $entityManager = $this->getDoctrine()->getManager();
            // $prices = $priceList->getPrices();
            $num = count($prices);
            $batchSize = 30;
            for($i = 0 ; $i < $num ; $i++)
            {
                $entityManager->persist($prices[$i]);
                if (($i % $batchSize) === 0) {
                    $entityManager->flush();
                    $entityManager->clear(); // Detaches all objects from Doctrine!
                }
            }
            $entityManager->flush();
            $entityManager->clear();
            $entityManager->persist($priceList);
            $entityManager->flush();
            $entityManager->clear();

            return $this->redirectToRoute('price_list_index');
        }

        return $this->render('price_list/new.html.twig', [
            'price_list' => $priceList,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="price_list_show", methods={"GET"})
     */
    public function show(PriceList $priceList): Response
    {
        return $this->render('price_list/show.html.twig', [
            'price_list' => $priceList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="price_list_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PriceList $priceList): Response
    {
        $form = $this->createForm(PriceListType::class, $priceList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('price_list_index');
        }

        return $this->render('price_list/edit.html.twig', [
            'price_list' => $priceList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="price_list_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PriceList $priceList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$priceList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($priceList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('price_list_index');
    }
}
