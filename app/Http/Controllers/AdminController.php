<?php

namespace App\Http\Controllers;

use App\Services\AdminStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(AdminStatsService $stats): View
    {
        return view('admin.index', ['stats' => $stats->snapshot()]);
    }

    public function stats(AdminStatsService $stats): JsonResponse
    {
        return response()->json($stats->snapshot());
    }
}
