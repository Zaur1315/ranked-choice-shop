<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Framework\RequestConfig;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'main_homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('main/default/index.html.twig', []);
    }

    #[Route('/edit-product/{id}', name: 'product_edit', requirements: ['id'=>'\d+'], methods: 'GET|POST')]
    #[Route('/add-product/', name: 'product_add', methods: 'GET|POST')]
    public function editProduct(ManagerRegistry $doctrine, Request $request, int $id = null): Response
    {
        $entityManager = $doctrine->getManager();

        if ($id) {
            $product = $entityManager->getRepository(Product::class)->find($id);
        }else{
            $product = new Product();
        }

        $form = $this->createForm(EditProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('main/default/edit_product.html.twig', ['form' => $form->createView()]);
    }
}
