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

const createChart = (score) => {
  if (myChart) {
    myChart.destroy();
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
        legend: {
          display: false
        }
      }
    }
  });
};

onMounted(() => {
  store.fetchPrediction();
});

watch(() => store.predictionResult, (newResult) => {
  if (newResult) {
    createChart(newResult.risk_score);
  }
});
</script>
