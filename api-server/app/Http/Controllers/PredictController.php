<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictController extends Controller
{
    /**
     * 將預測請求轉發到 ML 服務並處理回應。
     */
    public function predict(Request $request)
    {
        // 假設 ML 服務運行在 ml-api 容器的 8000 port
        $mlServiceUrl = 'http://ml-api:8000/predict';
        
        try {
            // 轉發請求並取得回應
            $response = Http::timeout(10)->post($mlServiceUrl, $request->all());

            // 檢查回應狀態
            $response->throw();

            return response()->json($response->json());
        } catch (\Exception $e) {
            // 處理錯誤
            return response()->json([
                'message' => 'Failed to get prediction from ML service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
