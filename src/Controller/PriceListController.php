<?php

namespace App\Controller;

use App\Entity\PriceList;
use App\Entity\ItemPrice;
use App\Form\PriceListType;
use App\Repository\CirculationNameAndUnitRepository;
use App\Repository\EquipmentNURepository;
use App\Repository\MaterialNURepository;
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
    public function newRandom(Request $request, MaterialNURepository $mnur, EquipmentNURepository $enur): Response //
    {
        $priceList = new PriceList();
        $id_z_Controllera = 'brak';
        $priceList->setName('cenyLos'.(new \DateTime('now'))->format('Y-m-d H:i'));
        $form = $this->createForm(PriceListType::class, $priceList);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $materials =  $mnur->findAll();
            // $materials = array_slice($mnur->findAll(),0,500);
            $entityManager = $this->getDoctrine()->getManager();
            // $prices = $priceList->getPrices();
            
            $entityManager->persist($priceList);
            // $entityManager->flush();
            // w tym miejscu zapisany obiekt ma już swoje id.
            $id_z_Controllera = $priceList->getId();
            $num = count($materials);
            
            
            $randomPrices = [];
            foreach($materials as $mat)
            {
                $it = new ItemPrice;
                $it->setPriceList($priceList);
                $it->setNameAndUnit($mat);
                $randomPrices[] = $it;
            }
            $priceList->AssignRandomPrices($randomPrices,0.95,301.34);
            
            $batchSize = 100;
            
            $indeksyDoPrzetworzenia = array();
            for($i = 1 ; $i <= $num ; $i++)
            {
                $indeksyDoPrzetworzenia[] = $i-1;
                if (($i % $batchSize) === 0) {
                    foreach($indeksyDoPrzetworzenia as $ind)
                    {
                        $entityManager->persist($randomPrices[$ind]);

                    }
                    $entityManager->flush();
                    foreach($indeksyDoPrzetworzenia as $ind)
                    {
                        $entityManager->detach($randomPrices[$ind]);
                        $entityManager->detach($materials[$ind]);
                    }
                    $indeksyDoPrzetworzenia = array();
                    // $entityManager->clear(); // Detaches all objects from Doctrine!
                    // $entityManager->persist($priceList);
                    // $entityManager->persist($materials[$i-1]);
                }
            }
            /*
            */
            foreach($indeksyDoPrzetworzenia as $ind)
            {
                $entityManager->persist($randomPrices[$ind]);

            }
            $entityManager->flush();
            $entityManager->clear();

            //*******przekierowanie tymczasowe */
            return $this->render('price_list/show.html.twig', [
                'price_list' => $priceList,
                'id_z_controllera' => $id_z_Controllera,
            ]);
        }
        
        return $this->render('price_list/new.html.twig', [
            'price_list' => $priceList,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/newRandomOld", name="price_list_new_randomOld", methods={"GET","POST"})
     */
    public function newRandomOld(Request $request, CirculationNameAndUnitRepository $cnur): Response
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

            $entityManager->persist($priceList);
            $entityManager->flush();
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
            // $entityManager->clear();

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
