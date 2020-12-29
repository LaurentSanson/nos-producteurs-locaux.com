<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\StockType;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/products")
 * @IsGranted("ROLE_PRODUCER")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('ui/product/index.html.twig', [
            "products" => $productRepository->findByFarm($this->getUser()->getFarm())
        ]);
    }

    /**
     * @Route("/create", name="product_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Votre produit a été créé avec succès"
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('ui/product/create.html.twig', [
            'productForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/update", name="product_update")
     * @param Product $product
     * @param Request $request
     * @return Response
     * @IsGranted("update", subject="product")
     */
    public function update(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Les informations de votre produit ont été modifiées avec succès"
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('ui/product/update.html.twig', [
            'productForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete")
     * @param Product $product
     * @return Response
     * @IsGranted("delete", subject="product")
     */
    public function delete(Product $product): Response
    {
        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash(
            "success",
            "Votre produit a été supprimé avec succès"
        );

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{id}/stock", name="product_stock")
     * @param Product $product
     * @param Request $request
     * @return Response
     * @IsGranted("update", subject="product")
     */
    public function stock(Product $product, Request $request): Response
    {
        $form = $this->createForm(StockType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Le stock de votre produit a été modifié avec succès"
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('ui/product/stock.html.twig', [
            'productForm' => $form->createView(),
        ]);
    }
}
