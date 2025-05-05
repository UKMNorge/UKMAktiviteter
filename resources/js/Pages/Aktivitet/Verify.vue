<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  mobileNumber: string;
  referrerUrl?: string;
  success?: string;
  error?: string;
}>();

// Form setup
const form = useForm({
  mobileNumber: props.mobileNumber,
  verificationCode: '',
  referrerUrl: props.referrerUrl || '',
});

// Submit handler
const submit = () => {
  form.post(route('aktivitet.verify', { tidspunktId: props.tidspunktId }));
};
</script>

<template>
  <GuestLayout>
    <Head title="Verifiser påmelding" />

    <v-container>
      <v-row class="justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
          <!-- Custom card with guaranteed white border to match Register page -->
          <div class="custom-card-outer">
            <div class="custom-card-inner">
              <!-- Festival return button -->
              <div class="festival-return-btn-container mb-3" v-if="props.referrerUrl">
                <v-btn 
                  variant="text" 
                  :href="props.referrerUrl" 
                  prepend-icon="mdi-arrow-left" 
                  class="festival-return-btn"
                >
                  Gå tilbake til festivalsiden
                </v-btn>
              </div>
              
              <div class="aktivitet-header mb-4">
                <div class="aktivitet-info">
                  <div class="responsive-title">Verifiser påmelding</div>
                </div>
              </div>
              
              <div v-if="success" class="mb-4">
                <v-alert 
                  type="success"
                  variant="flat"
                  color="#4caf50"
                  border="top"
                  elevation="3"
                  class="text-center"
                >
                  <strong>{{ success }}</strong>
                </v-alert>
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

              <p class="mb-4 text-body-1 responsive-text">
                En verifiseringskode er sendt til {{ mobileNumber }}.
              </p>

              <form @submit.prevent="submit">
                <v-text-field
                  v-model="form.verificationCode"
                  label="Verifiseringskode"
                  variant="solo"
                  type="text"
                  :error-messages="form.errors.verificationCode"
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
                  Verifiser
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
/* Custom card with white border - copied from Register.vue */
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

.aktivitet-header {
  display: flex;
  align-items: flex-start;
}

.aktivitet-info {
  flex-grow: 1;
}

.responsive-text {
  font-size: 1rem;
}

@media (max-width: 600px) {
  .responsive-title {
    font-size: 1.4rem;
  }
  
  .responsive-text {
    font-size: 0.95rem;
  }
  
  .v-text-field {
    margin-top: 0.5rem;
  }
}

@media (max-width: 400px) {
  .responsive-title {
    font-size: 1.2rem;
  }
  
  .responsive-text {
    font-size: 0.9rem;
  }
}

/* Festival return button styling */
.festival-return-btn-container {
  display: flex;
  justify-content: flex-start;
}

.festival-return-btn {
  color: #00ff89 !important;
  font-size: 0.9rem;
  padding: 0;
}
</style>
