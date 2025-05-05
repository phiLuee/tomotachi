<?php

use Livewire\Volt\Volt;


Volt::route('/', 'pages.home.index')
    ->name('home');

// Route für eingeloggte Benutzer (Dashboard)
Volt::route('/dashboard', 'pages.dashboard.index')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Volt::route('/@{username}', 'pages.profile.index')->name('profile');

require __DIR__ . '/auth.php';
