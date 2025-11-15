<template>
  <div v-if="error" :class="classes" class="text-red-500 absolute top-3 right-0">{{ errorMessage }}</div>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
  error: {
    type: [String, Array],
    default: ''
  },
  classes: {
    type: String,
    default: ''
  }
});

const errorMessage = computed(() => {
  return Array.isArray(props.error) ? props.error[0] : props.error;
});
</script>