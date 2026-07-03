@php
    $fieldPrefix = $fieldPrefix ?? 'insurance';
    $initialEffective = $initialEffective ?? '';
    $initialExpiration = $initialExpiration ?? '';
    $monthOptions = [
        __('January'), __('February'), __('March'), __('April'), __('May'), __('June'),
        __('July'), __('August'), __('September'), __('October'), __('November'), __('December'),
    ];
    $yearOptions = range(now()->year - 60, now()->year + 20);
@endphp

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('insuranceDateRangePicker', (initialEffective, initialExpiration, monthLabels) => ({
                    open: false,
                    startDate: initialEffective || null,
                    endDate: initialExpiration || null,
                    pendingStart: initialEffective || null,
                    pendingEnd: initialExpiration || null,
                    leftMonth: new Date().getMonth(),
                    leftYear: new Date().getFullYear(),
                    leftMonthOpen: false,
                    leftYearOpen: false,
                    rightMonthOpen: false,
                    monthLabels: monthLabels,
                    weekDays: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],

                    monthLabel(index) {
                        return this.monthLabels[index];
                    },

                    init() {
                        this.resetViewToSelection();
                    },

                    setInitialDates(effective, expiration) {
                        this.startDate = effective || null;
                        this.endDate = expiration || null;
                        this.pendingStart = this.startDate;
                        this.pendingEnd = this.endDate;
                        this.resetViewToSelection();
                    },

                    resetViewToSelection() {
                        const base = this.startDate ? new Date(this.startDate + 'T00:00:00') : new Date();
                        this.leftMonth = base.getMonth();
                        this.leftYear = base.getFullYear();
                    },

                    get rightMonth() {
                        return (this.leftMonth + 1) % 12;
                    },

                    set rightMonth(value) {
                        this.setRightMonth(value);
                    },

                    get rightYear() {
                        return this.leftMonth === 11 ? this.leftYear + 1 : this.leftYear;
                    },

                    set rightYear(value) {
                        this.setRightYear(value);
                    },

                    prevMonth() {
                        this.leftMonth--;
                        if (this.leftMonth < 0) {
                            this.leftMonth = 11;
                            this.leftYear--;
                        }
                    },

                    nextMonth() {
                        this.leftMonth++;
                        if (this.leftMonth > 11) {
                            this.leftMonth = 0;
                            this.leftYear++;
                        }
                    },

                    setLeftMonth(value) {
                        this.leftMonth = parseInt(value, 10);
                    },

                    setLeftYear(value) {
                        this.leftYear = parseInt(value, 10);
                    },

                    setRightMonth(value) {
                        const month = parseInt(value, 10);
                        if (month === 0) {
                            this.leftMonth = 11;
                            this.leftYear = this.rightYear - 1;
                        } else {
                            this.leftMonth = month - 1;
                        }
                    },

                    setRightYear(value) {
                        const year = parseInt(value, 10);
                        this.leftYear = this.leftMonth === 11 ? year - 1 : year;
                    },

                    daysInMonth(year, month) {
                        return new Date(year, month + 1, 0).getDate();
                    },

                    toIso(year, month, day) {
                        const mm = String(month + 1).padStart(2, '0');
                        const dd = String(day).padStart(2, '0');
                        return `${year}-${mm}-${dd}`;
                    },

                    buildWeeks(year, month) {
                        const firstDay = new Date(year, month, 1);
                        let startOffset = firstDay.getDay() - 1;
                        if (startOffset < 0) startOffset = 6;

                        const totalDays = this.daysInMonth(year, month);
                        const prevYear = month === 0 ? year - 1 : year;
                        const prevMonthIndex = month === 0 ? 11 : month - 1;
                        const prevMonthDays = this.daysInMonth(prevYear, prevMonthIndex);

                        const cells = [];
                        for (let i = 0; i < startOffset; i++) {
                            cells.push({ day: prevMonthDays - startOffset + i + 1, outside: true, iso: null });
                        }
                        for (let d = 1; d <= totalDays; d++) {
                            cells.push({ day: d, outside: false, iso: this.toIso(year, month, d) });
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

                    get leftWeeks() {
                        return this.buildWeeks(this.leftYear, this.leftMonth);
                    },

                    get rightWeeks() {
                        return this.buildWeeks(this.rightYear, this.rightMonth);
                    },

                    isToday(iso) {
                        if (!iso) return false;
                        const today = new Date();
                        return iso === this.toIso(today.getFullYear(), today.getMonth(), today.getDate());
                    },

                    isRangeStart(iso) {
                        return Boolean(iso) && iso === this.pendingStart;
                    },

                    isRangeEnd(iso) {
                        return Boolean(iso) && iso === this.pendingEnd;
                    },

                    isInRange(iso) {
                        if (!iso || !this.pendingStart || !this.pendingEnd) return false;
                        return iso > this.pendingStart && iso < this.pendingEnd;
                    },

                    selectDay(iso) {
                        if (!iso) return;

                        if (!this.pendingStart || (this.pendingStart && this.pendingEnd)) {
                            this.pendingStart = iso;
                            this.pendingEnd = null;
                            return;
                        }

                        if (iso < this.pendingStart) {
                            this.pendingEnd = this.pendingStart;
                            this.pendingStart = iso;
                        } else {
                            this.pendingEnd = iso;
                        }
                    },

                    toggleOpen() {
                        if (!this.open) {
                            this.pendingStart = this.startDate;
                            this.pendingEnd = this.endDate;
                            this.resetViewToSelection();
                        }
                        this.open = !this.open;
                    },

                    cancel() {
                        this.pendingStart = this.startDate;
                        this.pendingEnd = this.endDate;
                        this.open = false;
                    },

                    scrollToSelected(container) {
                        if (!container) return;
                        const el = container.querySelector('[data-selected="true"]');
                        if (el) el.scrollIntoView({ block: 'center' });
                    },

                    apply() {
                        this.startDate = this.pendingStart;
                        this.endDate = this.pendingEnd || this.pendingStart;
                        this.open = false;
                    },

                    displayDate(iso) {
                        if (!iso) return '';
                        const [y, m, d] = iso.split('-');
                        return `${m}/${d}/${y}`;
                    },
                }));
            });
        </script>
    @endpush
@endonce

<div
    id="{{ $fieldPrefix }}_date_range_root"
    x-data="insuranceDateRangePicker(@js($initialEffective), @js($initialExpiration), @js($monthOptions))"
    class="relative"
    @click.outside="open = false"
>
    <div class="grid grid-cols-2 gap-4">
        <div data-flux-field>
            <label class="block text-base text-gray-700 dark:text-neutral-200 mb-2">{{ __('Effective Date') }}</label>
            <input
                type="text"
                readonly
                @click="toggleOpen()"
                :value="displayDate(pendingStart)"
                placeholder="{{ __('Select') }}"
                class="w-full rounded-lg border border-gray-200 bg-white py-3 px-4 text-sm text-gray-800 cursor-pointer focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200"
            />
        </div>
        <div data-flux-field>
            <label class="block text-base text-gray-700 dark:text-neutral-200 mb-2">{{ __('Expiration Date') }}</label>
            <input
                type="text"
                readonly
                @click="toggleOpen()"
                :value="displayDate(pendingEnd)"
                placeholder="{{ __('Select') }}"
                class="w-full rounded-lg border border-gray-200 bg-white py-3 px-4 text-sm text-gray-800 cursor-pointer focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200"
            />
        </div>
    </div>

    <input type="hidden" name="effective_date" :value="pendingStart">
    <input type="hidden" name="expiration_date" :value="pendingEnd || pendingStart">

    <div
        x-show="open"
        x-transition
        style="display: none;"
        class="absolute z-40 end-0 mt-2 flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-lg rounded-xl overflow-hidden max-w-[calc(100vw-2rem)]"
    >
        <div class="grid grid-cols-5 items-center gap-x-3 mx-1.5 mt-3 pb-3">
            <div class="col-span-1">
                <button type="button" @click="prevMonth()" class="size-8 flex justify-center items-center text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-full focus:outline-hidden" aria-label="{{ __('Previous') }}">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            </div>

            <div class="col-span-3 flex justify-center items-center gap-x-2">
                <div class="relative" @click.outside="leftMonthOpen = false">
                    <button type="button" @click="leftMonthOpen = !leftMonthOpen; leftMonthOpen && $nextTick(() => scrollToSelected($refs.leftMonthList))" class="flex items-center gap-x-1 rounded-lg border border-gray-200 bg-white py-1.5 px-2 text-sm text-gray-800 cursor-pointer focus:outline-hidden focus:border-accent focus:ring-1 focus:ring-accent dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <span x-text="monthLabel(leftMonth)"></span>
                        <svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </button>
                    <div x-ref="leftMonthList" x-show="leftMonthOpen" style="display: none;" x-transition class="absolute z-50 mt-1 max-h-60 w-36 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:bg-neutral-800 dark:border-neutral-700">
                        @foreach($monthOptions as $index => $name)
                            <button type="button" @click="leftMonth = {{ $index }}; leftMonthOpen = false" :data-selected="leftMonth === {{ $index }}" class="block w-full px-3 py-2 text-start text-sm hover:bg-gray-100 dark:hover:bg-neutral-700" :class="leftMonth === {{ $index }} ? 'bg-accent text-accent-foreground' : 'text-gray-800 dark:text-neutral-200'">{{ $name }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="relative" @click.outside="leftYearOpen = false">
                    <button type="button" @click="leftYearOpen = !leftYearOpen; leftYearOpen && $nextTick(() => scrollToSelected($refs.leftYearList))" class="flex items-center gap-x-1 rounded-lg border border-gray-200 bg-white py-1.5 px-2 text-sm text-gray-800 cursor-pointer focus:outline-hidden focus:border-accent focus:ring-1 focus:ring-accent dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <span x-text="leftYear"></span>
                        <svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </button>
                    <div x-ref="leftYearList" x-show="leftYearOpen" style="display: none;" x-transition class="absolute z-50 mt-1 max-h-60 w-24 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:bg-neutral-800 dark:border-neutral-700">
                        @foreach($yearOptions as $year)
                            <button type="button" @click="leftYear = {{ $year }}; leftYearOpen = false" :data-selected="leftYear === {{ $year }}" class="block w-full px-3 py-2 text-start text-sm hover:bg-gray-100 dark:hover:bg-neutral-700" :class="leftYear === {{ $year }} ? 'bg-accent text-accent-foreground' : 'text-gray-800 dark:text-neutral-200'">{{ $year }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="relative" @click.outside="rightMonthOpen = false">
                    <button type="button" @click="rightMonthOpen = !rightMonthOpen; rightMonthOpen && $nextTick(() => scrollToSelected($refs.rightMonthList))" class="flex items-center gap-x-1 rounded-lg border border-gray-200 bg-white py-1.5 px-2 text-sm text-gray-800 cursor-pointer focus:outline-hidden focus:border-accent focus:ring-1 focus:ring-accent dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200">
                        <span x-text="monthLabel(rightMonth)"></span>
                        <svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </button>
                    <div x-ref="rightMonthList" x-show="rightMonthOpen" style="display: none;" x-transition class="absolute z-50 mt-1 max-h-60 w-36 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:bg-neutral-800 dark:border-neutral-700">
                        @foreach($monthOptions as $index => $name)
                            <button type="button" @click="rightMonth = {{ $index }}; rightMonthOpen = false" :data-selected="rightMonth === {{ $index }}" class="block w-full px-3 py-2 text-start text-sm hover:bg-gray-100 dark:hover:bg-neutral-700" :class="rightMonth === {{ $index }} ? 'bg-accent text-accent-foreground' : 'text-gray-800 dark:text-neutral-200'">{{ $name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-span-1 flex justify-end">
                <button type="button" @click="nextMonth()" class="size-8 flex justify-center items-center text-gray-800 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-full focus:outline-hidden" aria-label="{{ __('Next') }}">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>

        <div class="sm:flex">
            <!-- Left month -->
            <div class="p-3 pt-0 space-y-0.5">
                <div class="flex pb-1.5">
                    <template x-for="wd in weekDays" :key="wd">
                        <span class="m-px w-10 block text-center text-sm text-gray-500 dark:text-neutral-500" x-text="wd"></span>
                    </template>
                </div>

                <template x-for="(week, wIndex) in leftWeeks" :key="wIndex">
                    <div class="flex">
                        <template x-for="(cell, cIndex) in week" :key="cIndex">
                            <div :class="[
                                (isInRange(cell.iso) || isRangeStart(cell.iso) || isRangeEnd(cell.iso)) ? 'bg-gray-100 dark:bg-neutral-700' : '',
                                isRangeStart(cell.iso) ? 'rounded-s-full' : '',
                                isRangeEnd(cell.iso) ? 'rounded-e-full' : '',
                            ]">
                                <button
                                    type="button"
                                    :disabled="cell.outside"
                                    @click="selectDay(cell.iso)"
                                    class="m-px size-10 flex justify-center items-center border-[1.5px] border-transparent text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden"
                                    :class="(isRangeStart(cell.iso) || isRangeEnd(cell.iso)) ? 'bg-accent text-accent-foreground font-medium' : (isToday(cell.iso) ? 'text-gray-800 dark:text-neutral-200 font-semibold ring-2 ring-inset ring-accent hover:border-accent hover:text-accent' : 'text-gray-800 dark:text-neutral-200 hover:border-accent hover:text-accent')"
                                    x-text="cell.outside ? '' : cell.day"
                                ></button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Right month -->
            <div class="p-3 pt-0 space-y-0.5">
                <div class="flex pb-1.5">
                    <template x-for="wd in weekDays" :key="wd">
                        <span class="m-px w-10 block text-center text-sm text-gray-500 dark:text-neutral-500" x-text="wd"></span>
                    </template>
                </div>

                <template x-for="(week, wIndex) in rightWeeks" :key="wIndex">
                    <div class="flex">
                        <template x-for="(cell, cIndex) in week" :key="cIndex">
                            <div :class="[
                                (isInRange(cell.iso) || isRangeStart(cell.iso) || isRangeEnd(cell.iso)) ? 'bg-gray-100 dark:bg-neutral-700' : '',
                                isRangeStart(cell.iso) ? 'rounded-s-full' : '',
                                isRangeEnd(cell.iso) ? 'rounded-e-full' : '',
                            ]">
                                <button
                                    type="button"
                                    :disabled="cell.outside"
                                    @click="selectDay(cell.iso)"
                                    class="m-px size-10 flex justify-center items-center border-[1.5px] border-transparent text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden"
                                    :class="(isRangeStart(cell.iso) || isRangeEnd(cell.iso)) ? 'bg-accent text-accent-foreground font-medium' : (isToday(cell.iso) ? 'text-gray-800 dark:text-neutral-200 font-semibold ring-2 ring-inset ring-accent hover:border-accent hover:text-accent' : 'text-gray-800 dark:text-neutral-200 hover:border-accent hover:text-accent')"
                                    x-text="cell.outside ? '' : cell.day"
                                ></button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center py-3 px-4 justify-end border-t border-gray-200 dark:border-neutral-700 gap-x-2">
            <button type="button" @click="cancel()" class="py-2 px-3 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-neutral-700 text-gray-800 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-hidden">
                {{ __('Cancel') }}
            </button>
            <button type="button" @click="apply()" class="py-2 px-3 inline-flex justify-center items-center gap-x-2 text-xs font-medium rounded-lg bg-accent text-accent-foreground hover:opacity-90 focus:outline-hidden">
                {{ __('Apply') }}
            </button>
        </div>
    </div>
</div>
