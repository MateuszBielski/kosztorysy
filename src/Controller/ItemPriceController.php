<?php

namespace App\Controller;

use App\Entity\ItemPrice;
use App\Form\ItemPriceType;
use App\Repository\ItemPriceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/item/price")
 */
class ItemPriceController extends AbstractController
{
    /**
     * @Route("/", name="item_price_index", methods={"GET"})
     */
    public function index(ItemPriceRepository $itemPriceRepository): Response
    {
        return $this->render('item_price/index.html.twig', [
            'item_prices' => $itemPriceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="item_price_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $itemPrice = new ItemPrice();
        $form = $this->createForm(ItemPriceType::class, $itemPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($itemPrice);
            $entityManager->flush();

            return $this->redirectToRoute('item_price_index');
        }

        return $this->render('item_price/new.html.twig', [
            'item_price' => $itemPrice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="item_price_show", methods={"GET"})
     */
    public function show(ItemPrice $itemPrice): Response
    {
        return $this->render('item_price/show.html.twig', [
            'item_price' => $itemPrice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="item_price_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ItemPrice $itemPrice): Response
    {
        $form = $this->createForm(ItemPriceType::class, $itemPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('item_price_index');
        }

        return $this->render('item_price/edit.html.twig', [
            'item_price' => $itemPrice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="item_price_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ItemPrice $itemPrice): Response
    {
        if ($this->isCsrfTokenValid('delete'.$itemPrice->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($itemPrice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('item_price_index');
    }
}
