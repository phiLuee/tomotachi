<?php

use Livewire\Volt\Volt;

// Route für eingeloggte Benutzer (Dashboard)
Volt::route('/', 'pages.home.index')
    ->name('home'); // Benenne die Route (optional, aber nützlich)


// Route für eingeloggte Benutzer (Dashboard)
Volt::route('/dashboard', 'pages.dashboard.index')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Volt::route('/@{username}', 'pages.profile.show')->name('profile.show');


// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__ . '/auth.php';
