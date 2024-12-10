<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

#[Route('/admin/product', name: 'admin_product_')]
class ProductController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(ProductRepository $productRepository): Response
    {
        $product = $productRepository->findBy(['isDeleted' => false], ['id' => 'DESC'], 50);
        return $this->render('admin/product/list.html.twig', [
            'products' => $product,
        ]);
    }

    #[Route('/edit{id}', name: 'edit')]
    #[Route('/add', name: 'add')]
    public function edit(): Response|true
    {
        return true;
    }

    #[Route('/delete{id}', name: 'delete')]
    public function delete(): Response|true
    {
        return true;
    }
}
