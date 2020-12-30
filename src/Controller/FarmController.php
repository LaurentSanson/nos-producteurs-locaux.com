<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FarmController
 * @package App\Controller
 * @Route("/farm")
 */
class FarmController extends AbstractController
{
    /**
     * @Route("/all", name="farm_all")
     * @param FarmRepository $farmRepository
     * @return JsonResponse
     */
    public function all(FarmRepository $farmRepository): JsonResponse
    {
        return $this->json($farmRepository->findAll(), JsonResponse::HTTP_OK, [], ["groups" => "read"]);
    }

    /**
     * @Route("/{id}/show", name="farm_show")
     * @param Farm $farm
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function show(Farm $farm, ProductRepository $productRepository): Response
    {
        return $this->render("ui/farm/show.html.twig", [
            "farm" => $farm,
            "products" => $productRepository->findByFarm($farm)
        ]);
    }

    /**
     * @Route("/update", name="farm_update")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_PRODUCER")
     */
    public function update(Request $request): Response
    {
        $form = $this->createForm(FarmType::class, $this->getUser()->getFarm())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success",
                "Les informations de votre exploitation ont été modifiées avec succès"
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('ui/farm/update.html.twig', [
            'farmForm' => $form->createView(),
        ]);
    }
}
