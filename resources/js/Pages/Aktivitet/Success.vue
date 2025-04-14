<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  success?: string;
}>();
</script>

<template>
  <GuestLayout>
    <Head title="Påmelding vellykket" />

    <v-container>
      <v-row class="justify-center">
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card class="pa-4">
            <v-card-title class="text-center text-h4 mb-4">Påmelding vellykket</v-card-title>
            
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

            <v-card-text class="text-center">
              <v-icon icon="mdi-check-circle" color="success" size="x-large" class="mb-4"></v-icon>
              
              <p class="text-body-1">
                Din påmelding er nå registrert og bekreftet. Takk for at du deltar!
              </p>

              <v-btn
                color="primary"
                :to="'/'"
                class="mt-6"
              >
                Gå til forsiden
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </GuestLayout>
</template>
