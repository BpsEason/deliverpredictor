# DeliverPredictor

**DeliverPredictor** 是一個基於微服務架構的配送風險預測系統，旨在通過分析外送員的歷史數據（如逾期次數、請假頻率、平均配送時間、評分等）生成風險分數，並提供是否替換外送員的建議。系統將前端展示、後端業務邏輯和機器學習預測分離，實現高效、可擴展的服務架構。

## 專案亮點

- **模組化微服務架構**：系統分為 `web-app`（前端）、`api-server`（後端）和 `ml-api`（機器學習服務），各服務獨立開發與部署，確保靈活性與可維護性。
- **現代化技術棧**：
  - **前端**：Vue 3、Pinia、Vite、Chart.js，提供流暢的用戶體驗與數據視覺化。
  - **後端**：Laravel 11、PHP 8.2+、MySQL，實現穩定的業務邏輯與數據管理。
  - **機器學習**：FastAPI、Scikit-learn、Python 3.9+，提供高效的風險預測功能。
- **容器化部署**：使用 Docker Compose 實現一鍵部署，支援多平台（Linux/amd64、Linux/arm64）。
- **監控與 CI/CD**：
  - 整合 Sentry 進行錯誤追蹤，Prometheus 收集性能指標。
  - GitHub Actions 實現自動化測試與容器映像構建，確保程式碼品質。
- **可擴展性**：支援未來新增功能，如 Laravel Reverb 實時通訊或更複雜的 ML 模型。

## 系統架構

- **web-app**：前端介面，使用 Vue 3 構建，提供風險預測的儀表板，與後端 API 交互。
- **api-server**：後端服務，使用 Laravel 11，負責業務邏輯、資料庫操作及與 `ml-api` 的通訊。
- **ml-api**：機器學習服務，使用 FastAPI，載入預訓練模型並提供風險預測 API。
- **Nginx**：反向代理，負責路由前端與後端請求。
- **MySQL**：儲存外送員數據與系統設定。

## 技術棧

- **前端**：Vue 3, Pinia, Vue Router, Chart.js, Vite, Vitest
- **後端**：Laravel 11, PHP 8.2+, MySQL, Sentry, Prometheus
- **機器學習**：FastAPI, Python 3.9+, Scikit-learn, joblib, Sentry, Prometheus
- **部署**：Docker, Docker Compose, Nginx, GitHub Actions

## 關鍵程式碼片段

以下是專案中各模組的核心程式碼，附上中文註解，展示系統如何協同工作。

### 1. 後端：`api-server/app/Http/Controllers/PredictController.php`

```php
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
        // 定義 ML 服務的 API 端點
        $mlServiceUrl = 'http://ml-api:8000/predict';
        
        try {
            // 使用 Guzzle HTTP 客戶端發送 POST 請求到 ML 服務
            $response = Http::timeout(10)->post($mlServiceUrl, $request->all());

            // 若 ML 服務回應錯誤，拋出異常
            $response->throw();

            // 返回 ML 服務的 JSON 回應
            return response()->json($response->json());
        } catch (\Exception $e) {
            // 處理異常情況，返回錯誤訊息
            return response()->json([
                'message' => 'Failed to get prediction from ML service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

**說明**：此控制器負責接收前端的預測請求，將數據轉發給 `ml-api` 的 `/predict` 端點，並處理回應或錯誤。

### 2. 機器學習服務：`ml-api/main.py`

```python
from fastapi import FastAPI
from pydantic import BaseModel
import joblib
import os
from prometheus_client import Counter, generate_latest

# 初始化 FastAPI 應用
app = FastAPI()

# 載入預訓練的 LogisticRegression 模型
model = joblib.load('model.pkl')

# Prometheus 計數器，用於追蹤預測請求數量
PREDICT_REQUESTS = Counter('predict_requests_total', 'Total number of predict requests.')

# 定義輸入數據的 Pydantic 模型
class CourierData(BaseModel):
    past_late_count: int
    leave_frequency: int
    avg_delivery_time: float
    rating: float

# 定義 /predict 端點，處理風險預測
@app.post("/predict")
def predict_risk(data: CourierData):
    PREDICT_REQUESTS.inc()  # 增加 Prometheus 計數器
    
    # 準備模型輸入特徵
    features = [[data.past_late_count, data.leave_frequency, data.avg_delivery_time, data.rating]]
    
    # 使用模型預測風險
    prediction = model.predict(features)[0]
    risk_score = model.predict_proba(features)[0][1]  # 取得高風險機率
    
    # 判斷是否建議替換外送員
    recommend_replacement = risk_score > 0.5
    
    # 返回預測結果
    return {
        "courier_id": "C001",
        "risk_score": round(risk_score, 2),
        "recommend_replacement": bool(recommend_replacement)
    }
```

**說明**：此程式碼定義了 FastAPI 的 `/predict` 端點，接收外送員數據，載入預訓練模型進行風險預測，並返回風險分數與建議。

### 3. 前端：`web-app/src/views/Dashboard.vue`

```vue
<template>
  <div>
    <h2>外送員風險儀表板</h2>
    <div v-if="store.loading">
      <p>正在從後端 API 取得預測結果...</p>
    </div>
    <div v-else-if="store.error">
      <p style="color: red;">載入失敗: {{ store.error }}</p>
    </div>
    <div v-else-if="store.predictionResult">
      <h3>外送員 #{{ store.predictionResult.courier_id }} 預測結果</h3>
      <RiskCard :score="store.predictionResult.risk_score" />
      <p>是否推薦替換：{{ store.predictionResult.recommend_replacement ? '是' : '否' }}</p>
      
      <!-- 風險分數圓環圖 -->
      <div style="width: 300px; margin: 2rem auto;">
        <canvas ref="riskChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useCourierStore } from '../store/useCourierStore.js';
import RiskCard from '../components/RiskCard.vue';
import Chart from 'chart.js/auto';

const store = useCourierStore();
const riskChart = ref(null);
let myChart;

// 創建風險分數圓環圖
const createChart = (score) => {
  if (myChart) {
    myChart.destroy(); // 銷毀舊圖表
  }
  myChart = new Chart(riskChart.value, {
    type: 'doughnut',
    data: {
      labels: ['風險分數', '剩餘'],
      datasets: [{
        data: [score, 1 - score],
        backgroundColor: [
          score > 0.7 ? 'red' : (score > 0.4 ? 'orange' : 'green'),
          '#e0e0e0'
        ],
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      }
    }
  });
};

// 在元件掛載時觸發預測請求
onMounted(() => {
  store.fetchPrediction();
});

// 監聽預測結果，更新圖表
watch(() => store.predictionResult, (newResult) => {
  if (newResult) {
    createChart(newResult.risk_score);
  }
});
</script>
```

**說明**：此 Vue 元件展示風險預測結果，使用 Chart.js 繪製圓環圖，並通過 Pinia 狀態管理與後端 API 交互。

## 安裝與執行

### 先決條件

- Docker 和 Docker Compose
- Node.js 18+（用於本地開發）
- PHP 8.2+ 和 Composer（用於後端開發）
- Python 3.9+（用於 ML 服務開發）

### 設置步驟

1. **複製專案**
   ```bash
   git clone <your-repo-url>
   cd deliverpredictor
   ```

2. **啟動服務**
   ```bash
   docker-compose up -d --build
   ```

3. **初始化資料庫**
   ```bash
   docker-compose exec api-server php artisan migrate
   docker-compose exec api-server php artisan db:seed
   ```

4. **訪問應用**
   - 前端：`http://localhost:80`
   - 後端 API：`http://localhost:80/api`
   - ML API：`http://localhost:8000`

5. **運行測試**
   - 後端：`docker-compose exec api-server ./vendor/bin/phpunit`
   - ML API：`docker-compose exec ml-api pytest tests/`
   - 前端：`cd web-app && npm run test`

### 環境變數

- 複製 `api-server/.env.example` 到 `api-server/.env`，並設置：
  - `APP_KEY`：運行 `php artisan key:generate` 生成。
  - `SENTRY_DSN`：若使用 Sentry，設置監控 DSN。
  - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`：MySQL 連線資訊。
- 在 `docker-compose.yml` 中設置 `SENTRY_DSN_SECRET`。

## 未來改進

- **模型優化**：引入更複雜的 ML 模型（如 XGBoost）或更多特徵。
- **實時功能**：整合 Laravel Reverb 實現即時風險更新。
- **高級監控**：擴展 Prometheus 和 Grafana 的儀表板，監控更多指標。
- **身份驗證**：新增完整的用戶身份驗證與權限管理。

## 貢獻

歡迎提交問題或 PR！請遵循以下步驟：
1. Fork 專案
2. 創建特性分支 (`git checkout -b feature/YourFeature`)
3. 提交變更 (`git commit -m 'Add YourFeature'`)
4. 推送到分支 (`git push origin feature/YourFeature`)
5. 開啟 Pull Request

## 授權

本專案採用 MIT 授權，詳見 `LICENSE` 文件。
