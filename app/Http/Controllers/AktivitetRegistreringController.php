<?php

namespace App\Http\Controllers;

use App\Http\Requests\AktivitetRegistreringRequest;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Kommunikasjon\SMS;
use UKMNorge\Kommunikasjon\Mottaker;

class AktivitetRegistreringController extends Controller
{
    /**
     * Show the registration form for an activity
     */
    public function showRegistrationForm($tidspunktId)
    {
        try {
            // Try to create an instance of AktivitetTidspunkt with the given ID
            // The constructor will throw an exception if the activity doesn't exist
            $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt(
                (int) $tidspunktId
            );
            
            // If no exception was thrown, get activity info and time slot details
            $aktivitetNavn = $aktivitetTidspunkt->getAktivitet()->getNavn();
            
            // Calculate duration from start and end time if varighetMinutter is 0
            $varighetMinutter = $aktivitetTidspunkt->getVarighetMinutter();
            if ($varighetMinutter == 0) {
                $start = $aktivitetTidspunkt->getStart();
                $slutt = $aktivitetTidspunkt->getSlutt();
                $interval = $start->diff($slutt);
                $varighetMinutter = ($interval->h * 60) + $interval->i;
            }
            
            // Format time slot for display
            $formattedTidspunkt = [
                'sted' => $aktivitetTidspunkt->getSted(),
                'start' => $aktivitetTidspunkt->getStart()->format('Y-m-d H:i'),
                'slutt' => $aktivitetTidspunkt->getSlutt()->format('Y-m-d H:i'),
                'varighetMinutter' => $varighetMinutter,
                'kunInterne' => $aktivitetTidspunkt->getErKunInterne()
            ];
            
            // Get the activity image
            $aktivitetBilde = $aktivitetTidspunkt->getAktivitet()->getImage();
            
            return Inertia::render('Aktivitet/Register', [
                'tidspunktId' => $tidspunktId,
                'aktivitetNavn' => $aktivitetNavn,
                'aktivitetBilde' => $aktivitetBilde,
                'tidspunkt' => $formattedTidspunkt
            ]);
        } catch (Exception $e) {
            // Activity doesn't exist or another error occurred, show a user-friendly message
            return redirect()->route('welcome')
                ->with('warning', 'Aktiviteten finnes ikke.');
        }
    }

    /**
     * Register a user for an activity time slot
     */
    public function register(AktivitetRegistreringRequest $request, $tidspunktId)
    {
        try {
            // First check if the activity has available spots
            $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt(
                (int) $tidspunktId
            );
            
            // Get participant collection and mobile number
            $deltakere = $aktivitetTidspunkt->getDeltakere();
            $mobileNumber = $request->get('mobileNumber');
            
            // Normalize the mobile number (remove spaces, +, etc.)
            $normalizedMobile = preg_replace('/[^0-9]/', '', $mobileNumber);
            
            // Get all participants and loop through them to check for duplicates
            $allDeltakere = $deltakere->getAll();
            if (is_array($allDeltakere)) {
                foreach ($allDeltakere as $deltaker) {
                    if (is_object($deltaker) && method_exists($deltaker, 'getMobil')) {
                        // Normalize the stored mobile number as well
                        $storedMobile = preg_replace('/[^0-9]/', '', $deltaker->getMobil());
                        
                        // Compare normalized numbers to catch formatting differences
                        if ($storedMobile === $normalizedMobile) {
                            throw new Exception('Deltaker er allerede påmeldt');
                        }
                    }
                }
            }
            
            // Register the participant and get the verification code
            // This method handles registration but NOT sending the SMS
            $smsCode = \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt::registrerDeltakerPaamelding(
                (int) $tidspunktId,
                $mobileNumber
            );
            
            // Now send the SMS with the verification code using UKMNorge\Kommunikasjon\SMS
            SMS::setSystemId('UKMaktivitet', 0);
            $melding = 'Hei! Din verifiseringskode for UKM-aktiviteten er: ' . $smsCode;
            
            $sms = new SMS('UKMNorge');
            $result = $sms->setMelding($melding)
                        ->setMottaker(Mottaker::fraMobil($mobileNumber))
                        ->send();
            
            if (!$result) {
                throw new Exception('Kunne ikke sende SMS med verifiseringskode. Vennligst prøv igjen senere.');
            }

            // Return to verification page with success message
            return Inertia::render('Aktivitet/Verify', [
                'tidspunktId' => $tidspunktId,
                'mobileNumber' => $request->get('mobileNumber'),
                'success' => 'En verifiseringskode er sendt til ditt mobilnummer.'
            ]);
        } catch (Exception $e) {
            // Format the error message
            $errorMessage = $e->getMessage();
            
            // Check for specific error types and provide user-friendly messages
            if (strpos($errorMessage, 'ikke plass til flere') !== false) {
                $errorMessage = 'Det er ikke flere ledige plasser på denne aktiviteten.';
            } else if (strpos($errorMessage, 'Deltaker er allerede påmeldt') !== false) {
                $errorMessage = 'Du er allerede påmeldt denne aktiviteten.';
            } else if (strpos($errorMessage, 'brukeren er ikke intern') !== false) {
                $errorMessage = 'Du må være registrert som intern deltaker for å melde deg på denne aktiviteten.';
            } else if (strpos($errorMessage, 'Brukeren er ikke intern i arrangementet og kan derfor ikke melde seg på') !== false) {
                $errorMessage = 'Mobilnummeret er ikke registrert i arrangementet. Bruk mobilnummeret du meldte deg på UKM med.';
            }
            
            // Add error to session flash AND validation errors
            session()->flash('error', $errorMessage);
            
            // Return to registration page with error message
            return back()->withErrors([
                'mobileNumber' => $errorMessage,
            ])->with('tidspunktId', $tidspunktId);
        }
    }

    /**
     * Verify a user's registration using an SMS code
     */
    public function verify(Request $request, $tidspunktId)
    {
        $request->validate([
            'mobileNumber' => 'required|string',
            'verificationCode' => 'required|string',
        ]);

        try {
            // Verify the participant using the UKM library
            $verified = \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt::verifyDeltakerPaamelding(
                (int) $tidspunktId,
                $request->get('mobileNumber'),
                $request->get('verificationCode')
            );

            if ($verified) {
                // Return to success page
                return Inertia::render('Aktivitet/Success', [
                    'tidspunktId' => $tidspunktId,
                    'success' => 'Du er nå påmeldt aktiviteten!'
                ]);
            }

            // Verification failed, return to verification page with error
            return Inertia::render('Aktivitet/Verify', [
                'tidspunktId' => $tidspunktId,
                'mobileNumber' => $request->get('mobileNumber'),
                'error' => 'Verifiseringskoden er ikke gyldig. Vennligst prøv igjen.'
            ]);
        } catch (Exception $e) {
            // Return to verification page with error message
            return Inertia::render('Aktivitet/Verify', [
                'tidspunktId' => $tidspunktId,
                'mobileNumber' => $request->get('mobileNumber'),
                'error' => $e->getMessage()
            ]);
        }
    }
}
