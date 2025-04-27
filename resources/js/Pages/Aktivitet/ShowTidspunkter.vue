<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Navigation method
const navigateToRegister = (tidspunktId: number) => {
  router.visit(route('aktivitet.register', { tidspunktId }));
};

// Props definition
const props = defineProps<{
  aktivitetId: number;
  aktivitetNavn: string;
  aktivitetSted: string;
  aktivitetBilde?: string;
  tidspunkter: Array<{
    id: number;
    sted: string;
    start: string;
    slutt: string;
    varighetMinutter: number;
    maksAntall: number;
    antallDeltakere: number;
    erFullt: boolean;
    kunInterne: boolean;
  }>;
}>();

// Format the date for display
const formatDate = (dateString: string) => {
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
</script>

<template>
  <GuestLayout>
    <Head :title="`${aktivitetNavn} - UKM Aktiviteter`" />
    
    <v-container>
      <v-row class="justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
          <!-- Custom card with guaranteed white border -->
          <div class="custom-card-outer">
            <div class="custom-card-inner">
              <div class="aktivitet-header">
                <div v-if="aktivitetBilde" class="aktivitet-bilde-container">
                  <img :src="aktivitetBilde" :alt="aktivitetNavn" class="aktivitet-bilde" />
                </div>
                <div class="aktivitet-info">
                  <div class="responsive-title">{{ aktivitetNavn }}</div>
                  <div v-if="aktivitetSted" class="text-subtitle-1 mt-2">
                    <v-icon>mdi-map-marker</v-icon> {{ aktivitetSted }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Time slots card -->
          <div class="custom-card-outer">
            <div class="custom-card-inner">
              <div class="text-center mb-4">
                <div class="responsive-title">Velg tidspunkt</div>
              </div>
              
              <div v-if="tidspunkter.length === 0" class="text-center text-body-1 responsive-text">
                Det er ingen tilgjengelige tidspunkter for denne aktiviteten.
              </div>
              
              <div v-else class="time-slot-list">
                <div 
                  v-for="tidspunkt in tidspunkter" 
                  :key="tidspunkt.id"
                  class="time-slot-item mb-3"
                  :class="{'cursor-pointer': !tidspunkt.erFullt, 'time-slot-disabled': tidspunkt.erFullt}"
                  @click="!tidspunkt.erFullt && navigateToRegister(tidspunkt.id)"
                >
                  <div class="time-slot-header">
                    <div class="time-slot-title">{{ formatDate(tidspunkt.start) }}</div>
                    <div class="time-slot-chips">
                      <v-chip
                        v-if="tidspunkt.erFullt"
                        color="error"
                        size="small"
                        class="ml-2"
                      >
                        Fullt
                      </v-chip>
                      <v-chip
                        v-else
                        color="success"
                        size="small"
                        class="ml-2"
                      >
                        {{ tidspunkt.maksAntall > 0 
                          ? `${tidspunkt.antallDeltakere} / ${tidspunkt.maksAntall} plasser` 
                          : 'Åpen påmelding' }}
                      </v-chip>
                    </div>
                  </div>
                  <div class="time-slot-details">
                    <span>
                      <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
                      {{ formatDuration(tidspunkt.varighetMinutter) }}
                    </span>
                    <span v-if="tidspunkt.sted && tidspunkt.sted !== aktivitetSted" class="ml-4">
                      <v-icon size="small" class="mr-1">mdi-map-marker</v-icon>
                      {{ tidspunkt.sted }}
                    </span>
                  </div>
                </div>
              </div>
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
  color: white;
}

.responsive-title {
  font-size: 1.8rem; 
  font-weight: 500;
  line-height: 1.2;
  margin-bottom: 0.5rem;
}

.time-slot-list {
  margin-top: 1rem;
}

.time-slot-item {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 12px 16px;
  transition: background-color 0.2s;
}

.time-slot-item:hover:not(.time-slot-disabled) {
  background-color: rgba(255, 255, 255, 0.2);
}

.time-slot-disabled {
  opacity: 0.7;
  position: relative;
  overflow: hidden;
}

.time-slot-disabled::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: repeating-linear-gradient(
    45deg,
    rgba(255, 0, 0, 0.1),
    rgba(255, 0, 0, 0.1) 10px,
    rgba(0, 0, 0, 0.05) 10px,
    rgba(0, 0, 0, 0.05) 20px
  );
  pointer-events: none;
}

.time-slot-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
  flex-wrap: wrap;
}

.time-slot-title {
  font-weight: 500;
  font-size: 1.1rem;
}

.time-slot-details {
  display: flex;
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

.cursor-pointer {
  cursor: pointer;
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
  .responsive-title {
    font-size: 1.2rem;
  }
  
  .responsive-text {
    font-size: 0.9rem;
  }
  
  .time-slot-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .time-slot-chips {
    margin-top: 4px;
    margin-left: -2px;
  }
}
</style>
