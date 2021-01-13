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
            $cnus =  array_merge($mnur->findAll(),$enur->findAll());
            // $cnus = array_slice($mnur->findAll(),0,20);
            
            // $prices = $priceList->getPrices();
            $randomPrices = [];
            
            foreach($cnus as $mat)
            {
                $it = new ItemPrice;
                $it->setPriceList($priceList);
                $it->setNameAndUnit($mat);
                $randomPrices[] = $it;
            }
            $priceList->AssignRandomPrices($randomPrices,0.95,301.34);

            // $this->persistUsingDetach($priceList,$randomPrices);
            $this->persistUsingGeneratedQuery($priceList,$randomPrices);

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
    private function persistUsingDetach($priceList,$randomPrices)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($priceList);
            // $entityManager->flush();
            // w tym miejscu zapisany obiekt ma już swoje id.
            $id_z_Controllera = $priceList->getId();
            $num = count($randomPrices);
            
            
            
            
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
                        $entityManager->detach($randomPrices[$ind]->getNameAndUnit());
                    }
                    $indeksyDoPrzetworzenia = array();
                    // $entityManager->clear(); // Detaches all objects from Doctrine!
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
    }
    private function persistUsingGeneratedQuery($priceList,$randomPrices)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $conn = $entityManager->getConnection();
        $entityManager->persist($priceList);
        $entityManager->flush();
        $conn->executeQuery($priceList->GenerateInsertQueryForItemPrices($randomPrices));
    }
}
