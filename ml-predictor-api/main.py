from fastapi import FastAPI
from pydantic import BaseModel
import joblib
import os
import sentry_sdk
from prometheus_client import Counter, generate_latest

# 檢查 SENTRY_DSN 環境變數並初始化 Sentry
if "SENTRY_DSN" in os.environ:
    sentry_sdk.init(
        dsn=os.environ["SENTRY_DSN"],
        traces_sample_rate=1.0,
    )

app = FastAPI()

# 載入預訓練的模型
model = joblib.load('model.pkl')

# Prometheus Metrics
PREDICT_REQUESTS = Counter('predict_requests_total', 'Total number of predict requests.')

# 定義接收的資料格式
class CourierData(BaseModel):
    past_late_count: int
    leave_frequency: int
    avg_delivery_time: float
    rating: float

# 預測風險分數的 POST 路由
@app.post("/predict")
def predict_risk(data: CourierData):
    PREDICT_REQUESTS.inc()
    
    features = [[data.past_late_count, data.leave_frequency, data.avg_delivery_time, data.rating]]
    prediction = model.predict(features)[0]
    risk_score = model.predict_proba(features)[0][1] # 取得高風險的機率
    
    recommend_replacement = risk_score > 0.5
    
    return {
        "courier_id": "C001",
        "risk_score": round(risk_score, 2),
        "recommend_replacement": bool(recommend_replacement)
    }

# Prometheus metrics 端點
@app.get("/metrics")
def get_metrics():
    return generate_latest()
