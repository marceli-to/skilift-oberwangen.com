<template>
  <template v-if="formSuccess">
    <success-alert>
      Vielen Dank für Ihre Anfrage!
    </success-alert>
  </template>
  <template v-if="formError">
    <error-alert>
      Bitte überprüfen Sie die eingegebenen Daten.
    </error-alert>
  </template>
  <form @submit.prevent="submitForm" class="space-y-10 lg:space-y-25">
    <div class="lg:w-1/2 flex flex-col gap-10 lg:gap-20">
      <form-group>
        <form-text-field 
          v-model="form.firstname" 
          :error="errors.firstname"
          @update:error="errors.firstname = $event"
          :placeholder="errors.firstname ? errors.firstname : 'Vorname *'"
        />
      </form-group>
      <form-group>
        <form-text-field 
          v-model="form.name" 
          :error="errors.name"
          @update:error="errors.name = $event"
          :placeholder="errors.name ? errors.name : 'Name *'"
        />
      </form-group>
      <form-group>
        <form-text-field 
          v-model="form.street" 
          :error="errors.street"
          @update:error="errors.street = $event"
          :placeholder="errors.street ? errors.street : 'Strasse, Nr. *'"
        />
      </form-group>
      <form-group>
        <form-text-field 
          v-model="form.location" 
          :error="errors.location"
          @update:error="errors.location = $event"
          :placeholder="errors.location ? errors.location : 'PLZ/Ort *'"
        />
      </form-group>
      <form-group>
        <form-text-field 
          v-model="form.phone" 
          :error="errors.phone"
          @update:error="errors.phone = $event"
          placeholder="Telefon"
        />
      </form-group>
      <form-group>
        <form-text-field 
          type="email"
          v-model="form.email" 
          :error="errors.email"
          @update:error="errors.email = $event"
          :placeholder="errors.email ? errors.email : 'E-Mail *'"
        />
      </form-group>
      <form-group>
        <form-textarea-field
          v-model="form.message"
          :error="errors.message"
          @update:error="errors.message = $event"
          :placeholder="errors.message ? errors.message : 'Nachricht *'"
        />
      </form-group>
    </div>
    <form-group>
      <form-checkbox
        v-model="form.privacy"
        :error="errors.privacy"
        @update:error="errors.privacy = $event"
        id="privacy-contact"
        name="privacy"
        label="Ich habe die <a href='/datenschutz' class='decoration-1'>Datenschutzerklärung</a> gelesen und stimme dieser zu.*"
      />
    </form-group>
    <form-group class="!mt-30 lg:!mt-35">
      <form-button 
        type="submit" 
        :label="'Absenden'"
        :disabled="isSubmitting"
        :submitting="isSubmitting"
      />
    </form-group>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import FormGroup from '@/forms/components/fields/group.vue';
import FormTextField from '@/forms/components/fields/text.vue';
import FormTextareaField from '@/forms/components/fields/textarea.vue';
import FormButton from '@/forms/components/fields/button.vue';
import FormCheckbox from '@/forms/components/fields/checkbox.vue';
import SuccessAlert from '@/forms/components/alerts/success.vue';
import ErrorAlert from '@/forms/components/alerts/error.vue';
import { useFormScroll } from '@/composables/useFormScroll';

const { scrollToForm } = useFormScroll();
const isSubmitting = ref(false);
const formSuccess = ref(false);
const formError = ref(false);

const form = ref({
  name: null,
  firstname: null,
  phone: null,
  street: null,
  location: null,
  email: null,
  message: null,
  privacy: false
});

const errors = ref({
  name: '',
  firstname: '',
  street: '',
  location: '',
  email: '',
  message: '',
  privacy: '',
});

// Watch for changes in privacy checkbox
watch(() => form.value.privacy, (newValue) => {
  if (newValue === true) {
    errors.value.privacy = '';
  }
});

async function submitForm() {
  isSubmitting.value = true;
  formSuccess.value = false;
  formError.value = false;
  try {
    const response = await axios.post('/api/contact/submit', {
      ...form.value
    });
    handleSuccess();
  } catch (error) {
    handleError(error);
  }
}

function handleSuccess() {
  form.value = {
    name: null,
    firstname: null,
    street: null,
    location: null,
    email: null,
    message: null,
    phone: null,
    privacy: false,
  };
  
  errors.value = {
    name: '',
    firstname: '',
    street: '',
    location: '',
    email: '',
    message: '',
    phone: '',
    privacy: '',
  };
  
  isSubmitting.value = false;
  formSuccess.value = true;
  scrollToForm();
}

function handleError(error) {
  isSubmitting.value = false;
  formError.value = true;

  if (error.response && error.response.data && typeof error.response.data.errors === 'object') {
    Object.keys(error.response.data.errors).forEach(key => {
      errors.value[key] = error.response.data.errors[key];
    });
  }
  scrollToForm();
}
</script>