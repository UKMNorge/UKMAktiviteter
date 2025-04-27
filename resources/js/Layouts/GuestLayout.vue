<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <v-app>
        <v-app-bar flat color="#01004c" dark>
            <v-container class="d-flex align-center">
                <a href="https://ukm.no/festivalen" class="d-flex align-center text-decoration-none" target="_blank">
                    <img src="https://grafikk.ukm.no/profil/logoer/jubileum/PNG/UKM_40_Liggende_Gr%C3%B8nn.png" 
                         alt="UKM Logo" 
                         class="mr-2 ukm-logo" />
                </a>
                <v-spacer></v-spacer>
            </v-container>
        </v-app-bar>

        <v-main class="ukm-background">
            <v-container>
                <slot />
            </v-container>
        </v-main>

        <v-footer app color="#01004c" dark class="text-center d-flex flex-column">
            <div>
                &copy; {{ new Date().getFullYear() }} UKM Norge
            </div>
        </v-footer>
    </v-app>
</template>

<style>
.ukm-background {
    background-color: #01004c;
    background-image: linear-gradient(rgba(1, 0, 76, 0.97), rgba(1, 0, 76, 0.95)), url('https://grafikk.ukm.no/profil/grafiske-elementer/ikoner/PNG/raster-pattern-staaende.png');
    background-size: cover;
    background-position: center;
    color: white;
    min-height: 100vh;
    padding: 20px;
}

.ukm-logo {
    height: 120px;
    max-width: 100%;
    object-fit: contain;
}

.v-app-bar {
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
}

.v-footer {
    padding: 10px 0;
}
</style>
