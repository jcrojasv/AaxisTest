<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
#[IsGranted('ROLE_USER')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $repo): JsonResponse
    {
        return $this->json($repo->findAll());
    }

    #[Route('/product/{sku}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/product', name: 'app_product_create', methods: ['POST'])]
    public function create(ProductRepository $repo, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        $errors = $validator->validate($product);
        if (count($errors)) {
            return $this->json($errors, 422);
        }
        $repo->save($product, true);
        return $this->json($product, 201);
    }

    #[Route('/product/{sku}', name: 'app_product_update', methods: ['PUT'])]
    public function update(ProductRepository $repo, Request $request, SerializerInterface $serializer, Product $product, string $sku, ValidatorInterface $validator): JsonResponse
    {
        if (!$product) {
            throw $this->createNotFoundException('Product not found for SKU: ' . $sku);
        }

        $errors = $validator->validate($product);
        if (count($errors)) {
            return $this->json($errors, 422);
        }

        $updatedProduct = $serializer->deserialize($request->getContent(), Product::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $product
        ]);

        $repo->save($updatedProduct, true);
        return $this->json($updatedProduct);
    }

    #[Route('/product/{sku}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(ProductRepository $repo, Product $product): JsonResponse
    {
        $repo->remove($product, true);
        return $this->json('', 204);
    }
}
