<x-layouts.app :title="__('Portfolio')">
    @include('partials.tittle', [
        'title' => __('Master Portfolio'),
        'subheading' => __('Project Land'),
    ])

    @php
        $milestoneMonthLabels = [
            __('January'), __('February'), __('March'), __('April'), __('May'), __('June'),
            __('July'), __('August'), __('September'), __('October'), __('November'), __('December'),
        ];
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="flex items-center justify-end gap-2 text-sm text-gray-500 dark:text-neutral-400">
            <x-dynamic-component component="heroicon-o-clock" class="size-4 shrink-0" />
            {{ __('Portfolios') }}: <span class="font-semibold text-gray-800 dark:text-white">{{ $rows->count() }}</span>
        </div>

        @forelse ($rows as $row)
            @php
                $portfolio = $row['portfolio'];
                $project = $row['project'];
            @endphp
            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">

                {{-- Portfolio header --}}
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 p-5 dark:border-neutral-700">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                            {{ __('Project Name') }}
                        </p>
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                            {{ $project->name ?? __('Unknown') }}
                        </h2>
                    </div>

                    <flux:button
                        size="sm"
                        variant="primary"
                        icon="pencil-square"
                        onclick="openEditPortfolioModal({{ json_encode([
                            'portfolio_id' => (string) $portfolio->id,
                            'phase' => $portfolio->phase,
                            'schedule_percent' => $portfolio->schedule_percent,
                            'original_budget' => $portfolio->original_budget,
                            'revised_budget' => $portfolio->revised_budget,
                            'spent_to_date' => $portfolio->spent_to_date,
                            'milestones' => $portfolio->milestones,
                        ]) }})"
                    >
                        {{ __('Edit') }}
                    </flux:button>
                </div>

                {{-- Stats --}}
                <div class="grid gap-4 px-5 pt-5 pb-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Current Phase') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white">{{ $portfolio->phase ?: __('Not set') }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Schedule %') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white">{{ $portfolio->schedule_percent }}%</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700 lg:col-span-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Original Budget') }}</p>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Budget Complete') }}</p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">${{ number_format($portfolio->original_budget) }}</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $portfolio->budget_complete_percent }}%</p>
                        </div>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-neutral-700">
                            <div class="h-full rounded-full bg-gray-800 dark:bg-white" style="width: {{ min(100, $portfolio->budget_complete_percent) }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Revised Budget') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white">${{ number_format($portfolio->revised_budget) }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Spent to Date') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white">${{ number_format($portfolio->spent_to_date) }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Variance') }}</p>
                        <p class="mt-1 text-lg font-semibold {{ $portfolio->variance < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">
                            {{ $portfolio->variance < 0 ? '-' : '' }}${{ number_format(abs($portfolio->variance)) }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">{{ __('Balance to Complete') }}</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white">${{ number_format($portfolio->balance_to_complete) }}</p>
                    </div>
                </div>

                {{-- Milestones timeline --}}
                @if (count($portfolio->milestones) > 0)
                    <div class="border-t border-gray-200 p-5 dark:border-neutral-700">
                        <div class="mb-6 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('Milestones') }}</p>
                            <p class="text-xs text-gray-400 dark:text-neutral-500">{{ __('Click circles to update status') }}</p>
                        </div>

                        <div class="relative flex items-start justify-between">
                            <div class="absolute top-3 right-0 left-0 h-px bg-gray-200 dark:bg-neutral-700"></div>

                            @foreach ($portfolio->milestones as $index => $milestone)
                                @php
                                    $isDone = ($milestone['status'] ?? 'pending') === 'done';
                                    $isCurrent = ($milestone['status'] ?? 'pending') === 'current';
                                @endphp
                                <button
                                    type="button"
                                    class="relative z-10 flex flex-1 flex-col items-center gap-2 focus:outline-hidden"
                                    title="{{ __('Click to update status') }}"
                                    data-status="{{ $milestone['status'] ?? 'pending' }}"
                                    onclick="cycleMilestoneStatus('{{ (string) $portfolio->id }}', {{ $index }}, this)"
                                >
                                    <span
                                        @class([
                                            'flex size-6 items-center justify-center rounded-full border-2',
                                            'border-gray-800 bg-gray-800 dark:border-white dark:bg-white' => $isDone,
                                            'border-gray-800 bg-white dark:border-white dark:bg-neutral-800' => $isCurrent,
                                            'border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800' => !$isDone && !$isCurrent,
                                        ])
                                    >
                                        @if ($isDone)
                                            <span class="size-2 rounded-full bg-white dark:bg-neutral-800"></span>
                                        @endif
                                    </span>

                                    @php
                                        $targetDate = $milestone['target'] ?? null;
                                        $actualDate = $milestone['actual'] ?? null;
                                    @endphp
                                    <span class="text-xs font-medium text-gray-800 dark:text-white">{{ $milestone['name'] }}</span>
                                    <span class="text-[11px] text-gray-500 dark:text-neutral-400">{{ __('TGT') }}: {{ $targetDate ? \Carbon\Carbon::parse($targetDate)->format('m/d/Y') : '-' }}</span>
                                    <span class="text-[11px] text-gray-400 dark:text-neutral-500">{{ __('ACT') }}: {{ $actualDate ? \Carbon\Carbon::parse($actualDate)->format('m/d/Y') : __('Pending') }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @empty
            <section class="flex min-h-[200px] items-center justify-center rounded-lg border border-gray-200 bg-white p-10 text-center dark:border-neutral-700 dark:bg-neutral-800">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('No portfolios have been created yet.') }}
                </p>
            </section>
        @endforelse

        <button
            type="button"
            class="flex items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 p-4 text-sm font-medium text-gray-500 hover:border-gray-400 hover:text-gray-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:border-neutral-600 dark:hover:text-neutral-200"
            data-hs-overlay="#add-portfolio"
        >
            <x-dynamic-component component="heroicon-o-plus" class="size-4 shrink-0" />
            {{ __('New Portfolio') }}
        </button>
    </div>

    {{-- Add portfolio panel --}}
    <div id="add-portfolio" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-sm w-full z-80 bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="add-portfolio-label">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="add-portfolio-label" class="font-bold text-gray-800 dark:text-white">
                {{ __('New Portfolio') }}
            </h3>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#add-portfolio">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="addPortfolioForm" method="POST" action="{{ route('portfolio.store') }}" class="space-y-6">
                @csrf
                <flux:select id="new-portfolio-project" label="{{ __('Project') }}" name="project_id">
                    <option value="">{{ __('Select a project') }}</option>
                    @foreach ($selectableProjects as $selectableProject)
                        <option value="{{ $selectableProject->id }}">{{ $selectableProject->name }}</option>
                    @endforeach
                </flux:select>
                <div id="addPortfolioFormErrors" class="text-red-500 text-sm"></div>
                <div class="flex gap-3">
                    <flux:spacer />
                    <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit portfolio panel --}}
    <div id="edit-portfolio" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-lg w-full z-80 overflow-y-auto bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="edit-portfolio-label">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="edit-portfolio-label" class="font-bold text-gray-800 dark:text-white">
                {{ __('Edit Portfolio') }}
            </h3>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#edit-portfolio">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="editPortfolioForm" method="POST" action="" class="space-y-6">
                @csrf
                @method('PUT')
                <flux:input id="edit-phase" label="{{ __('Current Phase') }}" name="phase" />
                <flux:input id="edit-schedule_percent" type="number" min="0" max="100" label="{{ __('Schedule %') }}" name="schedule_percent" />

                <div data-flux-field>
                    <label for="edit-original_budget" class="block text-base text-gray-700 dark:text-neutral-200">{{ __('Original Budget') }}</label>
                    <div class="relative">
                        <input type="text" inputmode="decimal" id="edit-original_budget" name="original_budget" placeholder="0.00" class="money-input w-full rounded-lg border border-gray-200 bg-white py-3 ps-9 pe-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                            <span class="text-gray-500 dark:text-neutral-400">$</span>
                        </div>
                    </div>
                </div>

                <div data-flux-field>
                    <label for="edit-revised_budget" class="block text-base text-gray-700 dark:text-neutral-200">{{ __('Revised Budget') }}</label>
                    <div class="relative">
                        <input type="text" inputmode="decimal" id="edit-revised_budget" name="revised_budget" placeholder="0.00" class="money-input w-full rounded-lg border border-gray-200 bg-white py-3 ps-9 pe-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                            <span class="text-gray-500 dark:text-neutral-400">$</span>
                        </div>
                    </div>
                </div>

                <div data-flux-field>
                    <label for="edit-spent_to_date" class="block text-base text-gray-700 dark:text-neutral-200">{{ __('Spent to Date') }}</label>
                    <div class="relative">
                        <input type="text" inputmode="decimal" id="edit-spent_to_date" name="spent_to_date" placeholder="0.00" class="money-input w-full rounded-lg border border-gray-200 bg-white py-3 ps-9 pe-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                            <span class="text-gray-500 dark:text-neutral-400">$</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label class="block text-base text-gray-700 dark:text-neutral-200">
                            {{ __('Milestones') }}
                        </label>
                        <button
                            type="button"
                            id="add-milestone-row"
                            class="py-1.5 px-2 inline-flex items-center gap-x-1 text-xs font-medium rounded-full border border-dashed border-gray-200 bg-white text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            <x-dynamic-component component="heroicon-o-plus" class="size-3.5 shrink-0" />
                            {{ __('Add milestone') }}
                        </button>
                    </div>
                    <div id="edit-milestones" class="space-y-4"></div>
                </div>

                <div id="editPortfolioFormErrors" class="text-red-500 text-sm"></div>
                <div class="flex gap-3">
                    <flux:spacer />
                    <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const milestoneMonthLabels = @json($milestoneMonthLabels);

        function todayIso() {
            const today = new Date();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${today.getFullYear()}-${mm}-${dd}`;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('milestoneDatePicker', (initialValue) => ({
                open: false,
                value: initialValue || todayIso(),
                viewMonth: (initialValue ? new Date(initialValue + 'T00:00:00') : new Date()).getMonth(),
                viewYear: (initialValue ? new Date(initialValue + 'T00:00:00') : new Date()).getFullYear(),
                weekDays: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                monthLabels: milestoneMonthLabels,

                monthLabel(index) {
                    return this.monthLabels[index];
                },

                get yearRange() {
                    const current = new Date().getFullYear();
                    const years = [];
                    for (let y = current - 20; y <= current + 20; y++) {
                        years.push(y);
                    }
                    return years;
                },

                daysInMonth(year, month) {
                    return new Date(year, month + 1, 0).getDate();
                },

                toIso(year, month, day) {
                    const mm = String(month + 1).padStart(2, '0');
                    const dd = String(day).padStart(2, '0');
                    return `${year}-${mm}-${dd}`;
                },

                get weeks() {
                    const firstDay = new Date(this.viewYear, this.viewMonth, 1);
                    let startOffset = firstDay.getDay() - 1;
                    if (startOffset < 0) startOffset = 6;

                    const totalDays = this.daysInMonth(this.viewYear, this.viewMonth);
                    const prevYear = this.viewMonth === 0 ? this.viewYear - 1 : this.viewYear;
                    const prevMonthIndex = this.viewMonth === 0 ? 11 : this.viewMonth - 1;
                    const prevMonthDays = this.daysInMonth(prevYear, prevMonthIndex);

                    const cells = [];
                    for (let i = 0; i < startOffset; i++) {
                        cells.push({ day: prevMonthDays - startOffset + i + 1, outside: true, iso: null });
                    }
                    for (let d = 1; d <= totalDays; d++) {
                        cells.push({ day: d, outside: false, iso: this.toIso(this.viewYear, this.viewMonth, d) });
                    }
                    let nextDay = 1;
                    while (cells.length % 7 !== 0) {
                        cells.push({ day: nextDay++, outside: true, iso: null });
                    }

                    const weeks = [];
                    for (let i = 0; i < cells.length; i += 7) {
                        weeks.push(cells.slice(i, i + 7));
                    }
                    return weeks;
                },

                prevMonth() {
                    this.viewMonth--;
                    if (this.viewMonth < 0) {
                        this.viewMonth = 11;
                        this.viewYear--;
                    }
                },

                nextMonth() {
                    this.viewMonth++;
                    if (this.viewMonth > 11) {
                        this.viewMonth = 0;
                        this.viewYear++;
                    }
                },

                isToday(iso) {
                    if (!iso) return false;
                    const today = new Date();
                    return iso === this.toIso(today.getFullYear(), today.getMonth(), today.getDate());
                },

                isSelected(iso) {
                    return Boolean(iso) && iso === this.value;
                },

                selectDay(iso) {
                    if (!iso) return;
                    this.value = iso;
                    this.open = false;
                },

                dropdownStyle: '',

                toggleOpen() {
                    if (!this.open) {
                        if (this.value) {
                            const base = new Date(this.value + 'T00:00:00');
                            this.viewMonth = base.getMonth();
                            this.viewYear = base.getFullYear();
                        }
                        const rect = this.$refs.trigger.getBoundingClientRect();
                        this.dropdownStyle = `position: fixed; bottom: ${window.innerHeight - rect.top + 4}px; left: ${rect.left}px; z-index: 9999;`;
                    }
                    this.open = !this.open;
                },

                displayDate(iso) {
                    if (!iso) return '';
                    const [y, m, d] = iso.split('-');
                    return `${m}/${d}/${y}`;
                },
            }));
        });

        function openEditPortfolioModal(portfolio) {
            if (window.HSOverlay) {
                HSOverlay.autoInit();
                HSOverlay.open('#edit-portfolio');
            }

            document.getElementById('edit-phase').value = portfolio.phase || '';
            document.getElementById('edit-schedule_percent').value = portfolio.schedule_percent || 0;
            document.getElementById('edit-original_budget').value = formatMoneyForInput(portfolio.original_budget);
            document.getElementById('edit-revised_budget').value = formatMoneyForInput(portfolio.revised_budget);
            document.getElementById('edit-spent_to_date').value = formatMoneyForInput(portfolio.spent_to_date);
            document.getElementById('editPortfolioForm').action = `/portfolio/${portfolio.portfolio_id}`;

            const milestonesWrapper = document.getElementById('edit-milestones');
            milestonesWrapper.innerHTML = '';

            const milestones = Array.isArray(portfolio.milestones) ? portfolio.milestones : [];
            milestones.forEach((milestone) => addMilestoneRow(milestone));
        }

        function renderMilestoneDatePicker(fieldName, label, value) {
            const initial = JSON.stringify(value || null);
            return `
                <div class="col-span-1 relative" x-data='milestoneDatePicker(${initial})'>
                    <label class="text-xs text-gray-500 dark:text-neutral-400">${label}</label>
                    <input x-ref="trigger" type="text" readonly @click="toggleOpen()" :value="displayDate(value)" placeholder="{{ __('Select') }}" class="mt-1 block w-full cursor-pointer rounded-lg border-gray-200 text-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                    <input type="hidden" name="${fieldName}" :value="value ?? ''">

                    <template x-teleport="body">
                        <div
                            x-show="open"
                            x-transition
                            @click.outside="open = false"
                            :style="dropdownStyle"
                            style="display: none;"
                            class="w-72 flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-lg rounded-xl overflow-hidden"
                        >
                            <div class="p-3 space-y-0.5">
                                <div class="grid grid-cols-5 items-center gap-x-2 mx-1 pb-2">
                                    <div class="col-span-1">
                                        <button type="button" @click="prevMonth()" class="size-7 flex justify-center items-center text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-full focus:outline-hidden">
                                            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                        </button>
                                    </div>
                                    <div class="col-span-3 flex justify-center items-center gap-x-1">
                                        <select x-model.number="viewMonth" class="rounded-md border-gray-200 bg-white text-xs py-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                                            <template x-for="(label, i) in monthLabels" :key="i">
                                                <option :value="i" x-text="label"></option>
                                            </template>
                                        </select>
                                        <select x-model.number="viewYear" class="rounded-md border-gray-200 bg-white text-xs py-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                                            <template x-for="y in yearRange" :key="y">
                                                <option :value="y" x-text="y"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" @click="nextMonth()" class="size-7 flex justify-center items-center text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-full focus:outline-hidden">
                                            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex pb-1">
                                    <template x-for="wd in weekDays" :key="wd">
                                        <span class="m-px w-9 block text-center text-xs text-gray-500 dark:text-neutral-500" x-text="wd"></span>
                                    </template>
                                </div>

                                <template x-for="(week, wIndex) in weeks" :key="wIndex">
                                    <div class="flex">
                                        <template x-for="(cell, cIndex) in week" :key="cIndex">
                                            <div>
                                                <button
                                                    type="button"
                                                    :disabled="cell.outside"
                                                    @click="selectDay(cell.iso)"
                                                    class="m-px size-9 flex justify-center items-center border-[1.5px] border-transparent text-xs rounded-full disabled:opacity-40 disabled:pointer-events-none focus:outline-hidden"
                                                    :class="isSelected(cell.iso) ? 'bg-accent text-accent-foreground font-medium' : (isToday(cell.iso) ? 'text-gray-800 dark:text-neutral-200 font-semibold ring-1 ring-inset ring-accent' : 'text-gray-800 dark:text-neutral-200 hover:border-accent hover:text-accent')"
                                                    x-text="cell.outside ? '' : cell.day"
                                                ></button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            `;
        }

        function addMilestoneRow(milestone = {}) {
            const milestonesWrapper = document.getElementById('edit-milestones');
            const index = milestonesWrapper.children.length;

            const row = document.createElement('div');
            row.className = 'relative grid grid-cols-2 gap-2 rounded-lg border border-gray-200 p-3 dark:border-neutral-700';
            row.innerHTML = `
                <button type="button" class="remove-milestone absolute top-2 right-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
                    </svg>
                </button>
                <label class="col-span-2 text-xs text-gray-500 dark:text-neutral-400">
                    {{ __('Name') }}
                    <input type="text" name="milestones[${index}][name]" value="${milestone.name ?? ''}" class="mt-1 block w-full rounded-lg border-gray-200 text-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                </label>
                ${renderMilestoneDatePicker(`milestones[${index}][target]`, '{{ __('Target') }}', milestone.target)}
                ${renderMilestoneDatePicker(`milestones[${index}][actual]`, '{{ __('Actual') }}', milestone.actual)}
                <label class="col-span-2 text-xs text-gray-500 dark:text-neutral-400">
                    {{ __('Status') }}
                    <select name="milestones[${index}][status]" class="mt-1 block w-full rounded-lg border-gray-200 text-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                        <option value="pending" ${(milestone.status ?? 'pending') === 'pending' ? 'selected' : ''}>{{ __('Pending') }}</option>
                        <option value="current" ${milestone.status === 'current' ? 'selected' : ''}>{{ __('Current') }}</option>
                        <option value="done" ${milestone.status === 'done' ? 'selected' : ''}>{{ __('Done') }}</option>
                    </select>
                </label>
            `;
            milestonesWrapper.appendChild(row);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('add-milestone-row').addEventListener('click', () => addMilestoneRow());

            document.getElementById('edit-milestones').addEventListener('click', (event) => {
                const button = event.target.closest('.remove-milestone');
                if (button) {
                    button.closest('.relative.grid').remove();
                    reindexMilestoneRows();
                }
            });
        });

        function reindexMilestoneRows() {
            const rows = document.querySelectorAll('#edit-milestones > div');
            rows.forEach((row, index) => {
                row.querySelectorAll('[name^="milestones["]').forEach((input) => {
                    const field = input.name.match(/\[([a-z]+)\]$/)[1];
                    input.name = `milestones[${index}][${field}]`;
                });
            });
        }

        function formatMoneyForInput(value) {
            return Number(value || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function moneyInputParts(value) {
            value = String(value || '').replace(/[^0-9,.]/g, '');

            if (!value) {
                return { integerPart: '', decimalPart: '', hasDecimal: false };
            }

            const lastComma = value.lastIndexOf(',');
            const lastDot = value.lastIndexOf('.');
            const separatorIndex = Math.max(lastComma, lastDot);
            const separator = separatorIndex === -1 ? '' : value[separatorIndex];
            const separatorCount = (value.match(/[,.]/g) || []).length;
            const digitsAfterSeparator = separatorIndex === -1 ? 0 : value.length - separatorIndex - 1;
            const hasDecimal = separatorIndex !== -1
                && (
                    value.endsWith(separator)
                    || lastComma !== -1 && lastDot !== -1
                    || separatorCount === 1 && digitsAfterSeparator <= 2
                );

            if (!hasDecimal) {
                return {
                    integerPart: value.replace(/[,.]/g, ''),
                    decimalPart: '',
                    hasDecimal: false,
                };
            }

            return {
                integerPart: value.slice(0, separatorIndex).replace(/[,.]/g, ''),
                decimalPart: value.slice(separatorIndex + 1).replace(/[,.]/g, '').substring(0, 2),
                hasDecimal: true,
            };
        }

        function formatMoneyInputWhileTyping(value) {
            const parts = moneyInputParts(value);

            if (!parts.integerPart && !parts.hasDecimal) {
                return '';
            }

            const integerPart = (parts.integerPart || '0')
                .replace(/^0+(?=\d)/, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            return parts.hasDecimal ? `${integerPart}.${parts.decimalPart}` : integerPart;
        }

        function normalizeMoneyInput(value) {
            const parts = moneyInputParts(value);
            const integerPart = parts.integerPart || '0';

            return parts.hasDecimal ? `${integerPart}.${parts.decimalPart}` : integerPart;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('editPortfolioForm').addEventListener('input', (event) => {
                if (event.target.classList.contains('money-input')) {
                    event.target.value = formatMoneyInputWhileTyping(event.target.value);
                }
            });
        });

        function cycleMilestoneStatus(portfolioId, index, button) {
            const order = ['pending', 'current', 'done'];
            const current = button.dataset.status || 'pending';
            const next = order[(order.indexOf(current) + 1) % order.length];

            $.ajax({
                url: `/portfolio/${portfolioId}/milestones/${index}`,
                method: 'POST',
                data: {
                    _method: 'PATCH',
                    _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                    status: next,
                },
                success: function () {
                    location.reload();
                },
            });
        }
    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#addPortfolioForm').on('submit', function (e) {
                    e.preventDefault();
                    const form = $(this);

                    $('#addPortfolioFormErrors').html('');
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function () {
                            location.reload();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = Object.values(errors).map((e) => e.join('<br>')).join('<br>');
                                $('#addPortfolioFormErrors').html(errorMessages);
                            }
                        },
                    });
                });

                $('#editPortfolioForm').on('submit', function (e) {
                    e.preventDefault();
                    const form = $(this);

                    const moneyInputs = form.find('.money-input');
                    moneyInputs.each(function () {
                        this.value = normalizeMoneyInput(this.value);
                    });
                    const serialized = form.serialize();
                    moneyInputs.each(function () {
                        this.value = formatMoneyInputWhileTyping(this.value);
                    });

                    $('#editPortfolioFormErrors').html('');
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: serialized + '&_method=PUT',
                        success: function () {
                            location.reload();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = Object.values(errors).map((e) => e.join('<br>')).join('<br>');
                                $('#editPortfolioFormErrors').html(errorMessages);
                            }
                        },
                    });
                });
            });
        </script>
    @endpush
</x-layouts.app>
