<?php
namespace App\Services;
class ViewTableService{
    public function getHeaders(string $page): array
    {
        return config("admin_table_header.{$page}") ?? [];
    }

    public function getSearch(string $page): array
    {
        return config("admin_table_search.{$page}") ?? [];
    }
}
