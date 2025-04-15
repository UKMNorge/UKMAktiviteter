<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  aktivitetNavn?: string;
  error?: string;
  errors?: any;
}>();

// Form setup
const form = useForm({
  mobileNumber: '',
});

// If there's a server-side error, set it in the form errors
if (props.errors?.mobileNumber) {
  form.errors.mobileNumber = props.errors.mobileNumber;
}

// Submit handler
const submit = () => {
  form.clearErrors();
  form.post(
    route('aktivitet.register.submit', { tidspunktId: props.tidspunktId }),
    {
      preserveState: true
    }
  );
};
</script>

<template>
  <GuestLayout>
    <Head title="Påmelding til aktivitet" />

    <v-container>
      <v-row class="justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
          <!-- Custom card with guaranteed white border -->
          <div class="custom-card-outer">
            <div class="custom-card-inner">
              <div class="text-center mb-4">
                <div class="responsive-title">Påmelding til</div>
                <div class="activity-name">{{ props.aktivitetNavn || 'aktivitet' }}</div>
              </div>
            
            <div v-if="error" class="mb-4">
              <v-alert 
                type="error" 
                variant="flat"
                color="#ff3d00"
                border="top"
                elevation="3"
                class="text-center error-message"
              >
                <strong>{{ error }}</strong>
              </v-alert>
            </div>
            
              <!-- <p class="mb-4 text-body-1 responsive-text">
                For å melde deg på {{ props.aktivitetNavn ? 'aktiviteten "' + props.aktivitetNavn + '"' : 'denne aktiviteten' }}, vennligst skriv inn ditt mobilnummer nedenfor.
                Du vil motta en verifiseringskode på SMS for å bekrefte påmeldingen.
              </p> -->
              <p class="mb-4 text-body-1 responsive-text">
                For å melde deg på, vennligst skriv inn ditt mobilnummer nedenfor.
                Du vil motta en verifiseringskode på SMS for å bekrefte påmeldingen.
              </p>

              <form @submit.prevent="submit">
                <v-text-field
                  v-model="form.mobileNumber"
                  label="Mobilnummer"
                  variant="solo"
                  type="tel"
                  :error-messages="form.errors.mobileNumber"
                  required
                  autofocus
                ></v-text-field>

                <v-btn
                  type="submit"
                  color="#00ff89"
                  block
                  size="large"
                  :loading="form.processing"
                  class="mt-4"
                >
                  Meld meg på
                </v-btn>
              </form>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </GuestLayout>
</template>

<style scoped>
/* Custom card with white border */
.custom-card-outer {
  background-color: white;
  border-radius: 8px;
  padding: 3px;
  margin-bottom: 24px;
  margin-top: 12px;
}

.custom-card-inner {
  background-color: #01004c;
  border-radius: 6px;
  padding: 20px;
}

.responsive-title {
  font-size: 1.8rem; 
  font-weight: 500;
  line-height: 1.2;
  margin-bottom: 0.5rem;
}

.activity-name {
  font-size: 1.5rem;
  font-weight: 500;
  word-break: break-word;
  hyphens: auto;
}

@media (max-width: 600px) {
  .responsive-title {
    font-size: 1.4rem;
  }
  
  .activity-name {
    font-size: 1.2rem;
  }
}

@media (max-width: 400px) {
  .responsive-title {
    font-size: 1.2rem;
  }
  
  .activity-name {
    font-size: 1.1rem;
  }
}

.responsive-text {
  font-size: 1rem;
}

@media (max-width: 600px) {
  .responsive-text {
    font-size: 0.95rem;
  }
  
  .v-card {
    padding: 1rem !important;
  }
  
  .v-text-field {
    margin-top: 0.5rem;
  }
}

@media (max-width: 400px) {
  .responsive-text {
    font-size: 0.9rem;
  }
}
</style>
