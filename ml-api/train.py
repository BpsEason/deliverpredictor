import pandas as pd
from sklearn.linear_model import LogisticRegression
import joblib

# 建立一個簡單的假資料
data = {
    'past_late_count': [1, 5, 8, 2, 0, 9, 3, 4, 6, 7],
    'leave_frequency': [0, 1, 3, 0, 0, 2, 1, 1, 2, 3],
    'avg_delivery_time': [15.2, 25.1, 32.5, 18.9, 12.3, 35.8, 20.4, 22.1, 28.7, 30.0],
    'rating': [4.5, 3.8, 2.5, 4.9, 4.8, 2.1, 4.1, 3.9, 3.5, 2.9],
    'risk': [0, 1, 1, 0, 0, 1, 0, 0, 1, 1] # 0 = 低風險, 1 = 高風險
}

df = pd.DataFrame(data)

# 訓練模型
X = df[['past_late_count', 'leave_frequency', 'avg_delivery_time', 'rating']]
y = df['risk']

model = LogisticRegression()
model.fit(X, y)

# 儲存模型
joblib.dump(model, 'model.pkl')

print("模型訓練完成並儲存為 model.pkl")
