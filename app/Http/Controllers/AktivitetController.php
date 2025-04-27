<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use UKMNorge\Arrangement\Aktivitet\Aktivitet;

class AktivitetController extends Controller
{
    /**
     * Show the activity details with all available time slots
     */
    public function show($aktivitetId)
    {
        try {
            // Try to create an instance of Aktivitet with the given ID
            // The constructor will throw an exception if the activity doesn't exist
            $aktivitet = new Aktivitet((int) $aktivitetId);
            
            // Get all time slots for the activity
            $tidspunkter = $aktivitet->getTidspunkter()->getAll();
            
            // Format time slots for frontend display
            $formattedTidspunkter = [];
            $availableTidspunkter = []; // Track available time slots for auto-redirect
            
            foreach ($tidspunkter as $tidspunkt) {
                // Skip time slots that are full
                $deltakere = $tidspunkt->getDeltakere()->getAll();
                $antallDeltakere = is_array($deltakere) ? count($deltakere) : 0;
                $erFullt = ($tidspunkt->getMaksAntall() > 0 && $antallDeltakere >= $tidspunkt->getMaksAntall());
                
                // Only add time slots that have registration enabled
                if ($tidspunkt->getHarPaamelding()) {
                    // Calculate duration from start and end time if varighetMinutter is 0
                    $varighetMinutter = $tidspunkt->getVarighetMinutter();
                    if ($varighetMinutter == 0) {
                        $start = $tidspunkt->getStart();
                        $slutt = $tidspunkt->getSlutt();
                        $interval = $start->diff($slutt);
                        $varighetMinutter = ($interval->h * 60) + $interval->i;
                    }
                    
                    $formattedTidspunkt = [
                        'id' => $tidspunkt->getId(),
                        'sted' => $tidspunkt->getSted(),
                        'start' => $tidspunkt->getStart()->format('Y-m-d H:i'),
                        'slutt' => $tidspunkt->getSlutt()->format('Y-m-d H:i'),
                        'varighetMinutter' => $varighetMinutter,
                        'maksAntall' => $tidspunkt->getMaksAntall(),
                        'antallDeltakere' => $antallDeltakere,
                        'erFullt' => $erFullt,
                        'kunInterne' => $tidspunkt->getErKunInterne()
                    ];
                    
                    $formattedTidspunkter[] = $formattedTidspunkt;
                    
                    // Add to available time slots if not full
                    if (!$erFullt) {
                        $availableTidspunkter[] = $formattedTidspunkt;
                    }
                }
            }
            
            // Sort time slots by date and then time
            usort($formattedTidspunkter, function($a, $b) {
                // First compare the date part
                $dateA = substr($a['start'], 0, 10); // Get YYYY-MM-DD
                $dateB = substr($b['start'], 0, 10);
                
                if ($dateA !== $dateB) {
                    return $dateA <=> $dateB; // Use spaceship operator for comparison
                }
                
                // If dates are the same, compare the time part
                $timeA = substr($a['start'], 11); // Get HH:MM
                $timeB = substr($b['start'], 11);
                
                return $timeA <=> $timeB;
            });

            // If there's only one time slot in total (regardless of availability), redirect directly to registration
            // But only if that single time slot is not full
            if (count($formattedTidspunkter) === 1 && !$formattedTidspunkter[0]['erFullt']) {
                return redirect()->route('aktivitet.register', ['tidspunktId' => $formattedTidspunkter[0]['id']]);
            }
            
            // Return the activity details and its time slots
            return Inertia::render('Aktivitet/ShowTidspunkter', [
                'aktivitetId' => $aktivitet->getId(),
                'aktivitetNavn' => $aktivitet->getNavn(),
                'aktivitetSted' => $aktivitet->getSted(),
                'aktivitetBilde' => $aktivitet->getImage(),
                'tidspunkter' => $formattedTidspunkter
            ]);
        } catch (Exception $e) {
            // Activity doesn't exist or another error occurred, show a user-friendly message
            return redirect()->route('welcome')
                ->with('warning', 'Aktiviteten finnes ikke: ' . $e->getMessage());
        }
    }
}
