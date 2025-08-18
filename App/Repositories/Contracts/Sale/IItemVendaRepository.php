<?php

namespace App\Repositories\Contracts\Sale;

interface IItemVendaRepository
{
    public function all(array $params = []);

    public function create(array $data);

    public function findById(int $id);

    public function findByUuid(string $uuid);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByVenda(int $vendaId);

    public function findByProduto(int $produtoId);

    public function removeFromSale(int $vendaId, int $produtoId);

    public function updateQuantity(int $id, int $quantity);

    public function getTotalByVenda(int $vendaId);
}
