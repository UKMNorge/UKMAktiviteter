<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    return Inertia::render('Welcome', [
        'title' => 'UKM Aktiviteter',
        'description' => 'Velkommen til påmeldingssystemet for UKM aktiviteter.',
        'warning' => $request->session()->get('warning'),
    ]);
})->name('welcome');

// Direct activity ID in URL (e.g., aktiviteter.ukm.dev/32)
Route::get('/{id}', function ($id) {
    // Validate that the ID is numeric
    if (is_numeric($id)) {
        // First try as aktivitet_id
        try {
            $aktivitet = new \UKMNorge\Arrangement\Aktivitet\Aktivitet((int) $id);
            // If we get here, it's a valid aktivitet_id, so redirect to the show view
            return redirect()->route('aktivitet.show', ['aktivitetId' => $id]);
        } catch (\Exception $e) {
            // Try as tidspunkt_id instead
            try {
                $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt((int) $id);
                
                // Check if the activity is full
                $deltakere = $aktivitetTidspunkt->getDeltakere()->getAll();
                $antallDeltakere = is_array($deltakere) ? count($deltakere) : 0;
                
                if ($aktivitetTidspunkt->getMaksAntall() > 0 && $antallDeltakere >= $aktivitetTidspunkt->getMaksAntall()) {
                    // Activity is full - redirect to welcome with error
                    return redirect()->route('welcome')
                        ->with('warning', 'Det er ikke flere ledige plasser på denne aktiviteten.');
                }
                
                // Activity exists and has spots available, redirect to register form
                return redirect()->route('aktivitet.register', ['tidspunktId' => $id]);
            } catch (\Exception $e2) {
                // Neither aktivitet_id nor tidspunkt_id is valid
                return redirect()->route('welcome')
                    ->with('warning', 'Aktiviteten finnes ikke.');
            }
        }
    }
    // If not numeric, treat as a 404 or redirect to welcome
    return redirect()->route('welcome');
})->where('id', '[0-9]+');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Aktivitet routes
use App\Http\Controllers\AktivitetController;
use App\Http\Controllers\AktivitetRegistreringController;

// Redirect to welcome page with warning if no ID is provided
Route::get('/aktivitet/register', function () {
    return redirect()->route('welcome', ['noId' => 'true'])->with('warning', 'Du må velge en hendelse du vil melde deg på først');
})->name('aktivitet.register.noId');

// Route to view an activity with its time slots
Route::get('/aktivitet/view/{aktivitetId}', [AktivitetController::class, 'show'])
    ->name('aktivitet.show')
    ->where('aktivitetId', '[0-9]+');

// Direct URL with just aktivitet/ID (without /register)
Route::get('/aktivitet/{id}', function ($id) {
    if (!is_numeric($id)) {
        return redirect()->route('welcome');
    }
    
    // First try to interpret as aktivitet_id
    try {
        $aktivitet = new \UKMNorge\Arrangement\Aktivitet\Aktivitet((int) $id);
        // If we get here, it's a valid aktivitet_id, so redirect to the show view
        return redirect()->route('aktivitet.show', ['aktivitetId' => $id]);
    } catch (\Exception $e) {
        // Not an aktivitet, try to interpret as tidspunkt_id
        try {
            $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt((int) $id);
            
            // Check if the activity has available spots
            $deltakere = $aktivitetTidspunkt->getDeltakere()->getAll();
            $antallDeltakere = is_array($deltakere) ? count($deltakere) : 0;
            
            if ($aktivitetTidspunkt->getMaksAntall() > 0 && $antallDeltakere >= $aktivitetTidspunkt->getMaksAntall()) {
                // Activity is full - redirect to welcome with error
                return redirect()->route('welcome')
                    ->with('warning', 'Det er ikke flere ledige plasser på denne aktiviteten.');
            }
            
            // It's a valid tidspunkt_id with available spots, redirect to register form
            return redirect()->route('aktivitet.register', ['tidspunktId' => $id]);
        } catch (\Exception $e2) {
            // Neither aktivitet_id nor tidspunkt_id is valid
            return redirect()->route('welcome')
                ->with('warning', 'Aktiviteten finnes ikke.');
        }
    }
})->where('id', '[0-9]+');

Route::get('/aktivitet/{tidspunktId}/register', [AktivitetRegistreringController::class, 'showRegistrationForm'])
    ->name('aktivitet.register');
Route::post('/aktivitet/{tidspunktId}/register', [AktivitetRegistreringController::class, 'register'])
    ->name('aktivitet.register.submit');
Route::post('/aktivitet/{tidspunktId}/verify', [AktivitetRegistreringController::class, 'verify'])
    ->name('aktivitet.verify');

require __DIR__.'/auth.php';
