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
            <v-row>
                <v-col cols="12">
                    <v-card class="mb-6 pa-4" color="#fff055">
                        <v-card-text>
                            <div class="text-center mb-6">
                                <v-icon icon="mdi-calendar-check" size="x-large" color="primary" class="mb-4"></v-icon>
                                <h1 class="text-h3 mb-2">{{ title }}</h1>
                                <p class="text-subtitle-1">{{ description }}</p>
                            </div>
                            
                            <v-alert
                                v-if="localWarning"
                                :type="localWarning === 'Aktiviteten finnes ikke.' ? 'error' : 'warning'"
                                :variant="localWarning === 'Aktiviteten finnes ikke.' ? 'flat' : 'tonal'"
                                :color="localWarning === 'Aktiviteten finnes ikke.' ? '#ff3d00' : undefined"
                                :class="['mb-4 text-center', localWarning === 'Aktiviteten finnes ikke.' ? 'error-message' : '']"
                                :border="localWarning === 'Aktiviteten finnes ikke.' ? 'top' : undefined"
                                elevation="3"
                            >
                                <strong v-if="localWarning === 'Aktiviteten finnes ikke.'">{{ localWarning }}</strong>
                                <template v-else>{{ localWarning }}</template>
                            </v-alert>
                            
                            <p class="text-body-1 mb-4">
                                Her kan du melde deg på til UKM-aktiviteter.
                            </p>
                            
                            <v-alert
                                v-if="!localWarning"
                                type="info"
                                variant="elevated"
                                class="mb-4"
                            >
                                Du kan ikke aksessere denne siden direkte.
                            </v-alert>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </GuestLayout>
</template>
