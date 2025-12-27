<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function perPage(Request $request, int $default = 15, int $max = 100): int
    {
        $perPage = (int) $request->integer('per_page', $default);

        if ($perPage < 1) {
            return $default;
        }

        return min($perPage, $max);
    }

    protected function paginate(Request $request, Builder $query, int $defaultPerPage = 15)
    {
        return $query->paginate($this->perPage($request, $defaultPerPage));
    }

    protected function paginateCollection(Request $request, $items, int $defaultPerPage = 15): LengthAwarePaginator
    {
        $page = (int) $request->integer('page', 1);
        $perPage = $this->perPage($request, $defaultPerPage);

        $total = $items->count();
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
