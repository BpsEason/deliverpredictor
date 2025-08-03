from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_predict_endpoint_returns_valid_response():
    # 測試 /predict 端點
    response = client.post("/predict", json={
        "past_late_count": 5,
        "leave_frequency": 2,
        "avg_delivery_time": 15.5,
        "rating": 4.2
    })
    assert response.status_code == 200
    data = response.json()
    assert "courier_id" in data
    assert "risk_score" in data
    assert "recommend_replacement" in data
    assert isinstance(data["risk_score"], float)

def test_metrics_endpoint_returns_prometheus_format():
    # 測試 /metrics 端點
    response = client.get("/metrics")
    assert response.status_code == 200
    assert "predict_requests_total" in response.text
