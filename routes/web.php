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
Route::get('/{tidspunktId}', function ($tidspunktId) {
    // Validate that the ID is numeric
    if (is_numeric($tidspunktId)) {
        try {
            // First check if the activity exists in the database
            $sql = new \UKMNorge\Database\SQL\Query(
                "SELECT COUNT(*) AS count FROM `" . \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt::TABLE . "` 
                WHERE `tidspunkt_id` = '#id'",
                [
                    'id' => (int) $tidspunktId
                ]
            );
            
            $result = $sql->run();
            $row = \UKMNorge\Database\SQL\Query::fetch($result);
            
            if ($row['count'] == 0) {
                // Activity doesn't exist at all
                return redirect()->route('welcome')
                    ->with('warning', 'Aktiviteten finnes ikke.');
            }
            
            // Activity exists, now create the object
            $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt(
                (int) $tidspunktId
            );
            
            // Check if the activity is full by getting the current participants
            $deltakere = $aktivitetTidspunkt->getDeltakere()->getAll();
            // The getAll() method returns an array, not an object with num_rows
            $antallDeltakere = is_array($deltakere) ? count($deltakere) : 0;
            
            if ($aktivitetTidspunkt->getMaksAntall() > 0 && $antallDeltakere >= $aktivitetTidspunkt->getMaksAntall()) {
                // Activity is full - redirect to welcome with error
                return redirect()->route('welcome')
                    ->with('warning', 'Det er ikke flere ledige plasser på denne aktiviteten.');
            }
            
            // Activity exists and has spots available, redirect to register form
            return redirect()->route('aktivitet.register', ['tidspunktId' => $tidspunktId]);
        } catch (\Exception $e) {
            // Activity exists but there was an error, show a user-friendly message
            return redirect()->route('welcome')
                ->with('warning', 'Aktiviteten finnes ikke.');
        }
    }
    // If not numeric, treat as a 404 or redirect to welcome
    return redirect()->route('welcome');
})->where('tidspunktId', '[0-9]+');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Aktivitet registration routes
use App\Http\Controllers\AktivitetRegistreringController;

// Redirect to welcome page with warning if no ID is provided
Route::get('/aktivitet/register', function () {
    return redirect()->route('welcome', ['noId' => 'true'])->with('warning', 'Du må velge en hendelse du vil melde deg på først');
})->name('aktivitet.register.noId');

// Direct URL with just aktivitet/ID (without /register)
Route::get('/aktivitet/{tidspunktId}', function ($tidspunktId) {
    // Reuse the same validation logic as the root route
    try {
        // Try to create an activity instance to check if it exists
        $aktivitetTidspunkt = new \UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt(
            (int) $tidspunktId
        );
        
        // Check if the activity is full by getting the current participants
        $deltakere = $aktivitetTidspunkt->getDeltakere()->getAll();
        // The getAll() method returns an array, not an object with num_rows
        $antallDeltakere = is_array($deltakere) ? count($deltakere) : 0;
        
        if ($aktivitetTidspunkt->getMaksAntall() > 0 && $antallDeltakere >= $aktivitetTidspunkt->getMaksAntall()) {
            // Activity is full - redirect to welcome with error
            return redirect()->route('welcome')
                ->with('warning', 'Det er ikke flere ledige plasser på denne aktiviteten.');
        }
        
        // Activity exists and has spots available, redirect to register form
        return redirect()->route('aktivitet.register', ['tidspunktId' => $tidspunktId]);
    } catch (\Exception $e) {
        // Activity doesn't exist, redirect to welcome with error
        return redirect()->route('welcome')
            ->with('warning', 'Aktiviteten finnes ikke: ' . $e->getMessage());
    }
})->where('tidspunktId', '[0-9]+');

Route::get('/aktivitet/{tidspunktId}/register', [AktivitetRegistreringController::class, 'showRegistrationForm'])
    ->name('aktivitet.register');
Route::post('/aktivitet/{tidspunktId}/register', [AktivitetRegistreringController::class, 'register'])
    ->name('aktivitet.register.submit');
Route::post('/aktivitet/{tidspunktId}/verify', [AktivitetRegistreringController::class, 'verify'])
    ->name('aktivitet.verify');

require __DIR__.'/auth.php';
