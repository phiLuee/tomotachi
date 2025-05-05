
<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts.app')] // Setzt das Layout für die Seite
    #[Title('Dashboard')] // Setzt den Seitentitel für das Dashboard
    class extends Component {}; ?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6">
                <livewire:components.createpost />
            </div>
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <livewire:components.timeline />
            </div>
        </div>
    </div>
</div>