# UKM Påmelding

Et system for håndtering av påmeldinger til UKM-aktiviteter.

## Om systemet

UKM Påmelding er et system som lar deltakere melde seg på til ulike aktiviteter. Systemet forventer en ID-parameter for å identifisere hvilken aktivitet påmeldingen gjelder. Systemet håndterer:

- Sjekk om aktivitet finnes
- Påmelding til aktiviteter via mobilnummer
- Sjekk om mobilnummer finnes hvis aktiviteten er intern
- Verifisering med SMS-kode

## Teknisk oppsett

Systemet er bygget med:
- Laravel (PHP-rammeverk)
- Vue.js / Vuetify (frontend)
- Inertia.js (for å koble Laravel og Vue)

## Installasjon og oppsett

1. Klon repositoriet
2. Kjør `composer install` for å installere PHP-avhengigheter
3. Kjør `npm install` for å installere JavaScript-avhengigheter
4. Kopier `.env.example` til `.env` og konfigurer miljøvariablene
5. Sett `DB_CONNECTION=none` i `.env`-filen
6. Forsikre deg om at `SESSION_DRIVER` er satt til `file` eller  cookie` (ikke `database`)
7. Kjør `php artisan key:generate` for å generere applikasjonsnøkkel
8. Endre cors domenet i `vite.config.js`

## Utvikling

- Start utviklingsserveren: `php artisan serve`
- Start Vite-server: `npm run dev`
