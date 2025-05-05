<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  success?: string;
  referrerUrl?: string; // Prop to store the original referring URL
}>();
</script>

<template>
  <GuestLayout>
    <Head title="Påmelding vellykket" />

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
                  <div class="responsive-title">Påmelding vellykket</div>
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

              <div class="text-center">
                <v-icon icon="mdi-check-circle" color="#00ff89" size="x-large" class="mb-4"></v-icon>
                
                <p class="text-body-1 responsive-text mb-6">
                  Din påmelding er nå registrert og bekreftet. Takk for at du deltar!
                </p>

                <v-btn
                  color="#00ff89"
                  href="https://ukm.no/festivalen/workshops/"
                  size="large"
                  class="mt-4"
                >
                  Gå tilbake til festivalsiden
                </v-btn>
              </div>
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
  text-align: center;
}

.aktivitet-header {
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

.aktivitet-info {
  flex-grow: 1;
  text-align: center;
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
