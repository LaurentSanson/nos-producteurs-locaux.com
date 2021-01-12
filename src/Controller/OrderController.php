<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/create", name="order_create")
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function create(): RedirectResponse
    {
        $order = new Order();
        $order->setCustomer($this->getUser());
        /** @var CartItem $cartItem */
        foreach ($this->getUser()->getCart() as $cartItem) {
            $line = new OrderLine();
            $line->setOrder($order);
            $line->setQuantity($cartItem->getQuantity());
            $line->setProduct($cartItem->getProduct());
            $line->setPrice($cartItem->getProduct()->getPrice());

            $order->getLines()->add($line);
        }
        $order->setFarm($this->getUser()->getCart()->first()->getProduct()->getFarm());
        $this->getUser()->getCart()->clear();
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('order_history');
    }

    /**
     * @Route("/history", name="order_manage")
     * @param OrderRepository $orderRepository
     * @return Response
     * @IsGranted("ROLE_PRODUCER")
     */
    public function manage(OrderRepository $orderRepository): Response
    {
        return $this->render("ui/order/manage.html.twig", [
            "orders" => $orderRepository->findByFarm($this->getUser()->getFarm())
        ]);
    }

    /**
     * @Route("/history", name="order_history")
     * @param OrderRepository $orderRepository
     * @return Response
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function history(OrderRepository $orderRepository): Response
    {
        return $this->render("ui/order/history.html.twig", [
            "orders" => $orderRepository->findByCustomer($this->getUser())
        ]);
    }

    /**
     * @Route("/{id}/cancel", name="order_cancel")
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @IsGranted("cancel", subject="order")
     */
    public function cancel(Order $order, WorkflowInterface $orderStateMachine): RedirectResponse
    {
        $orderStateMachine->apply($order, 'cancel');
        return $this->redirectToRoute("order_history");
    }

    /**
     * @Route("/{id}/refuse", name="order_refuse")
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @IsGranted("refuse", subject="order")
     */
    public function refuse(Order $order, WorkflowInterface $orderStateMachine): RedirectResponse
    {
        $orderStateMachine->apply($order, 'refuse');
        return $this->redirectToRoute("order_manage");
    }
}
