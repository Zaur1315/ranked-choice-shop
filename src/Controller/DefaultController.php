<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Framework\RequestConfig;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();

        dd($productList);
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

        $form = $this->createFormBuilder($product)
            ->add('title', TextType::class)
            ->getForm();
        dd($product, $form);
        return $this->render('main/default/edit_product.html.twig', []);
    }
}
