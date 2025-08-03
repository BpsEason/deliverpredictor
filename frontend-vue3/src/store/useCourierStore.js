import { defineStore } from 'pinia'

export const useCourierStore = defineStore('courier', {
  state: () => ({
    predictionResult: null,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchPrediction() {
      this.loading = true;
      this.error = null;
      try {
        const response = await fetch('/api/predict', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            past_late_count: 5,
            leave_frequency: 2,
            avg_delivery_time: 15.5,
            rating: 4.2
          })
        });
        if (!response.ok) {
            throw new Error('API request failed');
        }
        const data = await response.json();
        this.predictionResult = data;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },
  },
})
