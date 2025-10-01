<template>
  <div :class="['checkboxes relative', classes]">
    <Error :error="error" class="!relative !mb-10" />
    <div class="flex items-center gap-x-10 lg:gap-x-15">
      <input 
        :id="id" 
        :name="name" 
        :value="modelValue" 
        :checked="checked" 
        :disabled="disabled" 
        :required="required" 
        type="checkbox" 
        @change="handleChange"
        class="mt-2 shrink-0"
        :class="[
          { '!border-red-500': error },
        ]"
      />
      <label :for="id" v-html="label"></label>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Error from './error.vue';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [String, Boolean],
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    required: true,
  },
  classes: {
    type: [String, Array, Object],
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const checked = computed(() => props.modelValue);

function handleChange(event) {
  emit('update:modelValue', event.target.checked);
  if (event.target.checked) {
    emit('update:error', '');
  }
}
</script>