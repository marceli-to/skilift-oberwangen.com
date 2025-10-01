<template>
  <button :type="type" :class="classes" :disabled="disabled">
    <template v-if="submitting">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 h-12 relative">
        <path fill="currentColor" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path>
      </svg>
    </template>
    {{ label }}
  </button>
</template>
<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'submit'
  },
  label: {
    type: String,
    required: true
  },
  classes: {
    type: String,
    default: 'bg-azure hover:bg-navy transition-all text-white uppercase font-bold flex items-center gap-x-10 lg:gap-x-15 px-10 lg:px-20 pt-6 pb-7 lg:pt-8 lg:pb-9 text-xxs lg:text-xs leading-none'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  submitting: {
    type: Boolean,
    default: true
  }
});
</script>