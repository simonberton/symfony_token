<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products')]
class APIController extends AbstractController
{
    #[Route('', name: 'product_list', methods: ['GET'])]
    public function list(ProductRepository $repository): JsonResponse
    {
        $products = $repository->findAllProducts();

        $data = array_map(fn(Product $p) => [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'sku' => $p->getSku(),
        ], $products);

        return $this->json($data, 200);
    }

    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, ProductRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['sku'])) {
            return $this->json(['error' => 'Both sku and name should be present on request body'], 400);
        }

        $existingProduct = $repository->findOneBy(['sku' => $data['sku']]);
        if ($existingProduct) {
            return $this->json(['error' => 'A product with this SKU already exists'], 409);
        }


        $product = new Product();
        $product->setName($data['name']);
        $product->setSku($data['sku']);

        $repository->save($product);

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
        ], 201);
    }

    #[Route('/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(int $id, Request $request, ProductRepository $repository): JsonResponse
    {
        $product = $repository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $existingProduct = $repository->findOneBy(['sku' => $data['sku']]);
        if ($existingProduct && $existingProduct->getId() !== $id) {
            return $this->json(['error' => 'A product with this SKU already exists'], 409);
        }

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }

        if (isset($data['sku'])) {
            $product->setSku($data['sku']);
        }

        $repository->update($product);

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
        ], 200);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id, ProductRepository $repository): JsonResponse
    {
        $product = $repository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $repository->delete($product);

        return new JsonResponse(null, 200);
    }
}
