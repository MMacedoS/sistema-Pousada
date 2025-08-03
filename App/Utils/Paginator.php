<?php

namespace App\Utils;

class Paginator {
    protected $items;
    protected $perPage;
    protected $currentPage;
    protected $totalItems;
    protected $totalPages;
    protected $lastPage;

    public function __construct(array $items, int $perPage = 2, int $currentPage = 1) {
        $this->items = $items;
        $this->perPage = $perPage;
        $this->totalItems = count($items);
        $this->lastPage = (int) ceil($this->totalItems / $this->perPage);
        $this->currentPage = max(1, min($currentPage, $this->lastPage));
    }

    public function getPaginatedItems() {
        $start = ($this->currentPage - 1) * $this->perPage;
        return array_slice($this->items, $start, $this->perPage);
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function links() {
        $links = '<nav><ul class="pagination">';
        
        // Previous button
        if ($this->currentPage > 1) {
            $prevPage = $this->currentPage - 1;
            $links .= "<li class='page-item'><a class='page-link' href='?page=$prevPage'>&laquo;</a></li>";
        } 
        if ($this->currentPage <= 1) {
            $links .= "<li class='page-item disabled'><a class='page-link' href=''>&laquo;</a></li>";
        }

        // Page numbers
        for ($i = 1; $i <= $this->totalPages; $i++) {
            if ($i == $this->currentPage) {
                $links .= "<li class='page-item active'><a class='page-link' href=''>$i</a></li>";
            } 
            if ($i != $this->currentPage) {
                $links .= "<li class='page-item'><a class='page-link' href='?page=$i'>$i</a></li>";
            }
        }

        // Next button
        if ($this->currentPage < $this->totalPages) {
            $nextPage = $this->currentPage + 1;
            $links .= "<li class='page-item'><a class='page-link' href='?page=$nextPage'>&raquo;</a></li>";
        } 
        if ($this->currentPage >= $this->totalPages) {
            $links .= "<li class='page-item disabled'><a class='page-link' href=''>&raquo;</a></li>";
        }

        $links .= '</ul></nav>';
        return $links;
    }
}
