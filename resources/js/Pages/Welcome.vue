<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref, onMounted } from 'vue';

const props = defineProps<{
    title: string;
    description: string;
    warning?: string;
}>();

const localWarning = ref(props.warning || '');

// Check if the URL path indicates a direct access to registration without ID
onMounted(() => {
    const path = window.location.pathname;
    const params = new URLSearchParams(window.location.search);
    
    // Check if there's a noId parameter or if the path indicates direct access without ID
    if (params.get('noId') === 'true' || path === '/aktivitet/register' || path === '/aktivitet') {
        localWarning.value = 'Du må velge en hendelse du vil melde deg på først';
    }
});

// No sample activities - they should be provided through a link with ID
</script>

<template>
    <Head title="UKM Aktiviteter" />
    <GuestLayout>
        <v-container>
            <v-row class="justify-center">
                <v-col cols="12" sm="10" md="8" lg="6">
                    <!-- Custom card with guaranteed white border -->
                    <div class="custom-card-outer">
                        <div class="custom-card-inner">
                            <div class="text-center mb-6">
                                <v-icon icon="mdi-calendar-check" size="x-large" color="primary" class="mb-4"></v-icon>
                                <h1 class="text-h3 mb-2">{{ title }}</h1>
                                <p class="text-subtitle-1">{{ description }}</p>
                            </div>
                            
                            <v-alert
                                v-if="localWarning"
                                :type="localWarning === 'Aktiviteten finnes ikke.' ? 'error' : localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.' ? 'error' : 'warning'"
                                :variant="localWarning === 'Aktiviteten finnes ikke.' || localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.' ? 'flat' : 'tonal'"
                                :color="localWarning === 'Aktiviteten finnes ikke.' ? '#00ff89' : localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.' ? '#00ff89' : undefined"
                                :class="['mb-4 text-center', (localWarning === 'Aktiviteten finnes ikke.' || localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.') ? 'error-message' : '']"
                                :border="localWarning === 'Aktiviteten finnes ikke.' || localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.' ? 'top' : undefined"
                                elevation="3"
                            >
                                <strong v-if="localWarning === 'Aktiviteten finnes ikke.' || localWarning === 'Det er ikke flere ledige plasser på denne aktiviteten.'">{{ localWarning }}</strong>
                                <template v-else>{{ localWarning }}</template>
                            </v-alert>
                            
                            <p v-if="!localWarning" class="text-body-1 mb-4">
                                Her kan du melde deg på til UKM-aktiviteter.
                            </p>
                            
                            <v-alert
                                v-if="!localWarning"
                                type="info"
                                variant="elevated"
                                class="mb-4"
                                color="#00ff89"
                            >
                                Du kan ikke aksessere denne siden direkte.
                            </v-alert>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </GuestLayout>
</template>

<style scoped>
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
  padding: 16px;
  color: white;
}
</style>
