<template>
  <div class="relative">
    <input
      :type="type"
      v-maska="mask"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :class="[
        { '!border-red-500 placeholder:!text-red-500': error },
      ]"
    >
  </div>
</template>

<script setup>
import { vMaska } from "maska/vue"

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
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  mask: {
    type: String,
    required: true
  },
});

defineEmits(['update:modelValue', 'update:error']);
</script>
