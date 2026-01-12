<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div
            x-data="themeToggle()"
            x-init="init()"
            class="inline-flex items-center rounded-lg p-1 w-full max-w-2xl
         bg-gray-200 dark:bg-neutral-900/80"
        >
            <div class="relative flex-1">
                <input id="appearance-light" type="radio" name="appearance" value="light"
                       class="peer sr-only" x-model="appearance" @change="setTheme()" />
                <label for="appearance-light"
                       class="flex items-center justify-center gap-2 w-full px-5 h-11 text-sm font-medium rounded-md cursor-pointer transition
             text-gray-600 hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white
             peer-checked:bg-white peer-checked:text-gray-900 peer-checked:shadow
             dark:peer-checked:bg-neutral-700 dark:peer-checked:text-white dark:peer-checked:shadow-none">
                    Light
                </label>
            </div>

            <div class="relative flex-1">
                <input id="appearance-dark" type="radio" name="appearance" value="dark"
                       class="peer sr-only" x-model="appearance" @change="setTheme()" />
                <label for="appearance-dark"
                       class="flex items-center justify-center gap-2 w-full px-5 h-11 text-sm font-medium rounded-md cursor-pointer transition
             text-gray-600 hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white
             peer-checked:bg-white peer-checked:text-gray-900 peer-checked:shadow
             dark:peer-checked:bg-neutral-700 dark:peer-checked:text-white dark:peer-checked:shadow-none">
                    Dark
                </label>
            </div>

            <div class="relative flex-1">
                <input id="appearance-system" type="radio" name="appearance" value="system"
                       class="peer sr-only" x-model="appearance" @change="setTheme()" />
                <label for="appearance-system"
                       class="flex items-center justify-center gap-2 w-full px-5 h-11 text-sm font-medium rounded-md cursor-pointer transition
             text-gray-600 hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white
             peer-checked:bg-white peer-checked:text-gray-900 peer-checked:shadow
             dark:peer-checked:bg-neutral-700 dark:peer-checked:text-white dark:peer-checked:shadow-none">
                    System
                </label>
            </div>
        </div>

        <script>
            function themeToggle() {
                return {
                    appearance: localStorage.getItem('appearance') || 'system',
                    init() {
                        this.appearance = localStorage.getItem('appearance') || 'system';
                        this.applyTheme(this.appearance);
                    },
                    setTheme() {
                        localStorage.setItem('appearance', this.appearance);
                        this.applyTheme(this.appearance);
                    },
                    applyTheme(mode) {
                        const root = document.documentElement;
                        root.classList.remove('dark');

                        if (mode === 'dark') root.classList.add('dark');
                        else if (mode === 'system') {
                            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                                root.classList.add('dark');
                            }
                        }
                    }
                }
            }
        </script>







    </x-settings.layout>
</section>
