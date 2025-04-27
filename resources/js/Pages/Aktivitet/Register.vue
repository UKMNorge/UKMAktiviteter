<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  aktivitetNavn?: string;
  aktivitetBilde?: string;
  error?: string;
  errors?: any;
  tidspunkt?: {
    sted: string;
    start: string;
    slutt: string;
    varighetMinutter: number;
    kunInterne: boolean;
  };
}>();

// Format the date for display
const formatDate = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return date.toLocaleDateString('nb-NO', options);
};

// Calculate the duration in a friendly format
const formatDuration = (minutes: number) => {
  if (!minutes || minutes <= 0) {
    return 'Varighet ikke spesifisert';
  }
  if (minutes < 60) {
    return `${minutes} minutter`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  if (remainingMinutes === 0) {
    return `${hours} time${hours > 1 ? 'r' : ''}`;
  }
  return `${hours} time${hours > 1 ? 'r' : ''} og ${remainingMinutes} minutter`;
};

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
              <div class="aktivitet-header mb-4">
                <div v-if="props.aktivitetBilde" class="aktivitet-bilde-container">
                  <img :src="props.aktivitetBilde" :alt="props.aktivitetNavn" class="aktivitet-bilde" />
                </div>
                <div class="aktivitet-info">
                  <div class="responsive-title">Påmelding til</div>
                  <div class="activity-name">{{ props.aktivitetNavn || 'aktivitet' }}</div>
                </div>
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
              
              <!-- Time slot information -->
              <div v-if="props.tidspunkt" class="time-slot-info mb-4 pa-3">
                <div class="time-slot-title">{{ formatDate(props.tidspunkt.start) }}</div>
                <div class="time-slot-details mt-2">
                  <div>
                    <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
                    {{ formatDuration(props.tidspunkt.varighetMinutter) }}
                  </div>
                  <div v-if="props.tidspunkt.sted" class="mt-1">
                    <v-icon size="small" class="mr-1">mdi-map-marker</v-icon>
                    {{ props.tidspunkt.sted }}
                  </div>
                </div>
              </div>
              
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

.time-slot-info {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 12px 16px;
  border-left: 3px solid #00ff89;
}

.time-slot-title {
  font-weight: 500;
  font-size: 1.1rem;
}

.time-slot-details {
  font-size: 0.9rem;
  opacity: 0.9;
}

.aktivitet-header {
  display: flex;
  align-items: flex-start;
}

.aktivitet-bilde-container {
  flex-shrink: 0;
  margin-right: 16px;
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
}

.aktivitet-bilde {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.aktivitet-info {
  flex-grow: 1;
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
  
  .time-slot-title {
    font-size: 1rem;
  }
  
  .time-slot-details {
    font-size: 0.85rem;
  }
  
  .aktivitet-bilde-container {
    width: 60px;
    height: 60px;
    margin-right: 12px;
  }
}

@media (max-width: 400px) {
  .responsive-text {
    font-size: 0.9rem;
  }
}
</style>
