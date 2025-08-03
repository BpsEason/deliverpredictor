<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function index()
    {
        // 簡易的 Prometheus metrics 範例
        $metrics = "# HELP app_requests_total The total number of requests.\n";
        $metrics .= "# TYPE app_requests_total counter\n";
        $metrics .= "app_requests_total{endpoint=\"/api/predict\"} " . rand(100, 1000) . "\n";
        $metrics .= "app_requests_total{endpoint=\"/api/metrics\"} " . rand(10, 50) . "\n";

        return response($metrics)->header('Content-Type', 'text/plain');
    }
}
