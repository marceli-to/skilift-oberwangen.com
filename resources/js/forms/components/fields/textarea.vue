<template>
  <div class="relative">
    <textarea
      :value="modelValue"
      :placeholder="placeholderText"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :class="[
        { '!border-red-500 placeholder:!text-red-500': errorMessage },
        ''
      ]">
    </textarea>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  error: {
    type: [String, Array],
    default: ''
  },
  placeholder: {
    type: [String, Array],
    default: ''
  }
});

const errorMessage = computed(() => {
  return Array.isArray(props.error) ? props.error[0] : props.error;
});

const placeholderText = computed(() => {
  return Array.isArray(props.placeholder) ? props.placeholder[0] : props.placeholder;
});

defineEmits(['update:modelValue', 'update:error']);
</script>
