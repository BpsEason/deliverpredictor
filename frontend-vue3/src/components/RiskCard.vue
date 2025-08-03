<template>
  <div class="risk-card" :style="{ backgroundColor: scoreColor }">
    <h3>風險分數</h3>
    <span>{{ score }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  score: Number
});

const scoreColor = computed(() => {
  if (props.score > 0.7) return 'red';
  if (props.score > 0.4) return 'orange';
  return 'green';
});
</script>

<style scoped>
.risk-card {
  padding: 1rem;
  border-radius: 8px;
  color: white;
  width: 200px;
  margin: 1rem auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
