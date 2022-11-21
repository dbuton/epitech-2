<?php

namespace App\Controller;

use App\Domain\Search;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use App\Service\ManageIllustrationService;
use App\Service\Tools;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/nos-produits", name="products")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) :Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->productRepository->findWithSearch($search);
        } else {
            $products = $this->productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/nouveau-produit", name="product_new")
     *
     * @param Request                   $request
     * @param ManageIllustrationService $illustrationService
     *
     * @return Response
     */
    public function new(Request $request, ManageIllustrationService $illustrationService) : Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newSlug = Tools::slugify($product->getName());
            $product->setSlug($newSlug);

            $illustration = $form->get('illustration')->getData();
            $illustrationName = $illustrationService->manageIllustration($illustration);
            $product->setIllustration($illustrationName);

            $this->productRepository->save($product, true);
            return $this->redirectToRoute('product', ['slug' => $newSlug]);
        }

        return $this->render('product/add.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     *
     * @param $slug
     * @return Response
     */
    public function show($slug) : Response
    {
        $product = $this->productRepository->findOneBy(
            ['slug' => $slug]
        );

        $products = $this->productRepository->findBy(
            ['isBest' => 1]
        );

        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }

    /**
     * @Route("/modification-produit/{slug}", name="product_modify")
     *
     * @param Request                   $request
     * @param string                    $slug
     * @param ManageIllustrationService $illustrationService
     * @return RedirectResponse|Response
     */
    public function modify(Request $request, string $slug, ManageIllustrationService $illustrationService) : RedirectResponse|Response
    {
        $product = $this->productRepository->findOneBy(
            ['slug' => $slug]
        );

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newSlug = Tools::slugify($product->getName());
            $product->setSlug($newSlug);

            if ($form->get('illustration')->getData() !== null) {
                $illustration = $form->get('illustration')->getData();
                $illustrationName = $illustrationService->manageIllustration($illustration);
                $product->setIllustration($illustrationName);
            }
            $this->productRepository->save($product, true);
            return $this->redirectToRoute('product', ['slug' => $newSlug]);
        }

        return $this->render('product/modify.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/suppression-produit/{slug}", name="product_delete")
     *
     * @param string $slug
     * @return Response
     */
    public function delete(string $slug) : Response
    {
        $product = $this->productRepository->findOneBy(
            ['slug' => $slug]
        );

        $this->productRepository->remove($product, true);

        return $this->redirectToRoute('products');
    }
}
