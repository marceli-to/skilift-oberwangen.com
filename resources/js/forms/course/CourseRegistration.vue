<template>
  <template v-if="formSuccess">
    <success-alert>
      Vielen Dank für Ihre Anmeldung!
    </success-alert>
  </template>
  <template v-if="formError">
    <error-alert>
      Bitte überprüfen Sie die eingegebenen Daten.
    </error-alert>
  </template>
  <form @submit.prevent="submitForm" class="space-y-10 lg:space-y-25" v-if="isLoaded">
    <div class="flex flex-col gap-10 lg:gap-20">
      <div class="lg:w-1/2 flex flex-col gap-10 lg:gap-20">
        <form-group>
          <form-text-field
            v-model="form.firstname"
            :error="errors.firstname"
            @update:error="errors.firstname = $event"
            :placeholder="errors.firstname ? errors.firstname : 'Vorname (Kind) *'"
            label="Vorname (Kind)"
            aria-label="Vorname (Kind)"
          />
        </form-group>
        <form-group>
          <form-text-field
            v-model="form.name"
            :error="errors.name"
            @update:error="errors.name = $event"
            :placeholder="errors.name ? errors.name : 'Name (Kind) *'"
            label="Name (Kind)"
            aria-label="Name (Kind)"
          />
        </form-group>
        <form-group>
          <form-masked-text-field
            v-model="form.dob"
            :error="errors.dob"
            @update:error="errors.dob = $event"
            :placeholder="errors.dob ? errors.dob : 'Geburtsdatum (Kind), z.B. 12.12.2022 *'"
            label="Geburtsdatum (Kind)"
            aria-label="Geburtsdatum (Kind)"
            mask="##.##.####"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.name_parents"
            :error="errors.name_parents"
            @update:error="errors.name_parents = $event"
            :placeholder="errors.name_parents ? errors.name_parents : 'Vorname, Name (Erziehungsberechtigte) *'"
            label="Vorname, Name (Erziehungsberechtigte)"
            aria-label="Vorname, Name (Erziehungsberechtigte)"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.street"
            :error="errors.street"
            @update:error="errors.street = $event"
            :placeholder="errors.street ? errors.street : 'Adresse *'"
            label="Adresse"
            aria-label="Adresse"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.location"
            :error="errors.location"
            @update:error="errors.location = $event"
            :placeholder="errors.location ? errors.location : 'PLZ/Ort *'"
            label="PLZ/Ort"
            aria-label="PLZ/Ort"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.phone"
            :error="errors.phone"
            @update:error="errors.phone = $event"
            :placeholder="errors.phone ? errors.phone : 'Telefon (für Notfälle) *'"
            label="Telefon (für Notfälle)"
            aria-label="Telefon (für Notfälle)"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="email"
            v-model="form.email"
            :error="errors.email"
            @update:error="errors.email = $event"
            :placeholder="errors.email ? errors.email : 'E-Mail *'"
            label="E-Mail"
            aria-label="E-Mail"
          />
        </form-group>
        <form-group>
          <form-textarea-field
            v-model="form.remarks"
            :error="errors.remarks"
            @update:error="errors.remarks = $event"
            :placeholder="errors.remarks ? errors.remarks : 'Mitteilungen'"
            label="Mitteilungen"
            aria-label="Mitteilungen"
          />
        </form-group>
      </div>
      <div class="max-w-4xl">
        Die Anmeldung für den Skikurs ist verbindlich. Vor Beginn des Kurses muss die Skistunde am Skischulwagen bezahlt werden. Anschliessend erhalten Sie dort eine Skischulweste. Die Bezahlung kann entweder mit Twint oder in bar erfolgen.
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
      <form-group class="!mt-15">
        <form-button 
          type="submit" 
          :label="'Anmelden'"
          :disabled="isSubmitting"
          :submitting="isSubmitting"
        />
      </form-group>
    </div>
  </form>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import FormGroup from '@/forms/components/fields/group.vue';
import FormTextField from '@/forms/components/fields/text.vue';
import FormMaskedTextField from '@/forms/components/fields/masked-text.vue';
import FormTextareaField from '@/forms/components/fields/textarea.vue';
import FormButton from '@/forms/components/fields/button.vue';
import FormCheckbox from '@/forms/components/fields/checkbox.vue';
import SuccessAlert from '@/forms/components/alerts/success.vue';
import ErrorAlert from '@/forms/components/alerts/error.vue';
import { useFormScroll } from '@/composables/useFormScroll';

const { scrollToForm } = useFormScroll();
const props = defineProps({
  courseId: {
    type: String,
    required: true,
  },
});

const isSubmitting = ref(false);
const isLoaded = ref(false);
const formSuccess = ref(false);
const formError = ref(false);

const hasOpenSeats = ref(false);

const form = ref({
  course_id: props.courseId,
  name: null,
  firstname: null,
  dob: null,
  name_parents: null,
  street: null,
  location: null,
  phone: null,
  email: null,
  remarks: null,
  privacy: false
});

const errors = ref({
  name: '',
  firstname: '',
  dob: '',
  name_parents: '',
  street: '',
  location: '',
  phone: '',
  email: '',
  remarks: '',
  privacy: '',
});


onMounted(async () => {
  try {
    const response = await axios.get(`/api/course/${props.courseId}`);
    isLoaded.value = true;
    hasOpenSeats.value = response.data.has_open_seats;
  }
  catch (error) {
    console.error(error);
  }
});

async function submitForm() {
  isSubmitting.value = true;
  formSuccess.value = false;
  formError.value = false;

  try {
    const response = await axios.post('/api/course/register', {
      ...form.value
    });
    handleSuccess();
  } catch (error) {
    handleError(error);
  }
}

function handleSuccess() {
  form.value = {
    course_id: props.courseId,
    name: null,
    firstname: null,
    dob: null,
    name_parents: null,
    street: null,
    location: null,
    phone: null,
    email: null,
    remarks: null,
    privacy: false
  };

  errors.value = {
    name: '',
    firstname: '',
    dob: '',
    name_parents: '',
    street: '',
    location: '',
    phone: '',
    email: '',
    remarks: '',
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