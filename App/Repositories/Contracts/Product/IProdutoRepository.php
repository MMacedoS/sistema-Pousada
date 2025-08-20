<?php

namespace App\Repositories\Contracts\Product;

interface IProdutoRepository
{
    public function all(array $params = []);

    public function create(array $data);

    public function findById(int $id);

    public function findByUuid(string $uuid);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByCategory(string $category);

    public function findActive();

    public function findInStock();

    public function findAvailable();

    public function updateStock(int $id, int $quantity);

    public function checkStock(int $id, int $quantity);

    public function getCategories();
}
