<template>
  <div class="relative">
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholderText"
      :class="[
        { '!border-red-500 placeholder:!text-red-500': errorMessage },
      ]"
    >
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: [String, Array],
    default: ''
  },
  error: {
    type: [String, Array],
    default: ''
  },
});

const errorMessage = computed(() => {
  return Array.isArray(props.error) ? props.error[0] : props.error;
});

const placeholderText = computed(() => {
  return Array.isArray(props.placeholder) ? props.placeholder[0] : props.placeholder;
});

defineEmits(['update:modelValue', 'update:error']);
</script>
