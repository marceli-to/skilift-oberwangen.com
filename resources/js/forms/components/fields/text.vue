<template>
  <div class="relative">
    <input
      :type="type"
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
});

defineEmits(['update:modelValue', 'update:error']);
</script>
