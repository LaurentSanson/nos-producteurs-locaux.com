<?php

namespace App\Controller;

use App\Entity\Product;
use App\Handler\CartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class CartController
 * @package App\Controller
 * @IsGranted("ROLE_CUSTOMER")
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/add/{id}", name="cart_add")
     * @IsGranted("add_to_cart", subject="product")
     * @param Product $product
     * @return RedirectResponse
     */
    public function add(Product $product): RedirectResponse
    {
        $this->getUser()->addToCart($product);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("success", "Le produit a bien été ajouté à votre panier");
        return $this->redirectToRoute('farm_show', ['slug' => $product->getFarm()->getSlug()]);
    }

    /**
     * @param Request $request
     * @param CartHandler $handler
     * @return Response
     * @Route("/", name="cart_index")
     */
    public function index(Request $request, CartHandler $handler): Response
    {

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("cart_index");
        }

        return $this->render("ui/cart/index.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}
