<?php

namespace App\Controller;

use App\Entity\CostItem;
use App\Form\CostItemType;
use App\Repository\CostItemRepository;
use App\Repository\ItemPriceRepository;
use App\Repository\TableRowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cost/item")
 */
class CostItemController extends AbstractController
{
    /**
     * @Route("/", name="cost_item_index", methods={"GET"})
     */
    public function index(CostItemRepository $costItemRepository): Response
    {
        return $this->render('cost_item/index.html.twig', [
            'cost_items' => $costItemRepository->findAll(),
        ]);
    }
    /**
     * @Route("/calculateAjax", name="table_row_calculateAjax", methods={"GET","POST"})
     */
    public function calculateAjax(Request $request,TableRowRepository $tableRowRepository, ItemPriceRepository $itemPriceRepository)
    {
        $id = $request->query->get("id");
        $tableRow = $tableRowRepository->find($id);
        $costItem = new CostItem;
        $costItem->Initialize($tableRow);
        $costItem->UpdatePricesFrom($itemPriceRepository);
        return $this->render('table_row/showCosts.html.twig', [
        'cost_item' => $costItem,
        ]);
    }
    /**
     * @Route("/new", name="cost_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $costItem = new CostItem();
        $form = $this->createForm(CostItemType::class, $costItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($costItem);
            $entityManager->flush();

            return $this->redirectToRoute('cost_item_index');
        }

        return $this->render('cost_item/new.html.twig', [
            'cost_item' => $costItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cost_item_show", methods={"GET"})
     */
    public function show(CostItem $costItem): Response
    {
        return $this->render('cost_item/show.html.twig', [
            'cost_item' => $costItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cost_item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CostItem $costItem): Response
    {
        $form = $this->createForm(CostItemType::class, $costItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cost_item_index');
        }

        return $this->render('cost_item/edit.html.twig', [
            'cost_item' => $costItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cost_item_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CostItem $costItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$costItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($costItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cost_item_index');
    }
}
