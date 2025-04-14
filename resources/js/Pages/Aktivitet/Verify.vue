<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props
const props = defineProps<{
  tidspunktId: number;
  mobileNumber: string;
  success?: string;
  error?: string;
}>();

// Form setup
const form = useForm({
  mobileNumber: props.mobileNumber,
  verificationCode: '',
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
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card class="pa-4">
            <v-card-title class="text-center text-h4 mb-4">Verifiser påmelding</v-card-title>
            
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

            <v-card-text>
              <p class="mb-4 text-body-1">
                En verifiseringskode er sendt til {{ mobileNumber }}.
              </p>

              <form @submit.prevent="submit">
                <v-text-field
                  v-model="form.verificationCode"
                  label="Verifiseringskode"
                  variant="outlined"
                  type="text"
                  :error-messages="form.errors.verificationCode"
                  required
                  autofocus
                ></v-text-field>

                <v-btn
                  type="submit"
                  color="primary"
                  block
                  size="large"
                  :loading="form.processing"
                  class="mt-4"
                >
                  Verifiser
                </v-btn>
              </form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </GuestLayout>
</template>
