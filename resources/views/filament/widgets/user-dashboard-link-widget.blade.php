<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Kullanici Dashboard</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">Kullanici tarafindaki okuma ve not ekranina gecis.</p>
            </div>

            <x-filament::button
                tag="a"
                :href="route('user.dashboard')"
                icon="heroicon-m-arrow-top-right-on-square"
            >
                Kullanici Dashboard'a Git
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
