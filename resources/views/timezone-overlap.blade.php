{{--
  Time Zone Overlap — fully client-side Alpine.js tool.
  Uses the browser's Intl API for DST-correct UTC offset calculation.
  No server round-trip. No external libraries.
--}}
<div x-data="timezoneOverlap()" x-init="init()" class="space-y-8">

    {{-- ── City selectors ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">

        {{-- City A --}}
        <div class="relative">
            <label class="block text-sm font-semibold text-gray-700 mb-2">City / Country A</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchA"
                    @focus="showDropdownA = true"
                    @input="showDropdownA = true"
                    @keydown.escape="showDropdownA = false"
                    @keydown.arrow-down.prevent="focusDropdownItem('a', 0)"
                    placeholder="Search city or country…"
                    autocomplete="off"
                    class="block w-full border border-gray-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm"
                />
                <span class="absolute right-3 top-3.5 text-gray-400 pointer-events-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
            </div>
            <div
                x-show="showDropdownA && filteredCitiesA.length > 0"
                x-cloak
                @click.outside="showDropdownA = false"
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto"
            >
                <template x-for="(city, idx) in filteredCitiesA" :key="city.name + city.tz">
                    <button
                        type="button"
                        :id="'dropdown-a-' + idx"
                        @click="selectCity('a', city)"
                        @keydown.arrow-down.prevent="focusDropdownItem('a', idx + 1)"
                        @keydown.arrow-up.prevent="focusDropdownItem('a', idx - 1)"
                        @keydown.escape="showDropdownA = false"
                        class="w-full text-left px-4 py-3 text-sm hover:bg-gray-50 flex items-center justify-between"
                        :class="cityA && cityA.tz === city.tz && cityA.name === city.name ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700'"
                    >
                        <span x-text="city.name + ', ' + city.country"></span>
                        <span class="text-xs text-gray-400 ml-2 shrink-0" x-text="city.tz"></span>
                    </button>
                </template>
            </div>
            <div x-show="cityA" class="mt-2 text-xs text-gray-500 font-medium" x-text="cityA ? getOffsetLabel(cityA.tz) : ''"></div>
        </div>

        {{-- City B (swap button sits between the two on sm+) --}}
        <div class="relative">
            <div class="hidden sm:flex absolute -left-7 top-9 z-10">
                <button
                    type="button"
                    @click="swapCities()"
                    title="Swap cities"
                    class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-full shadow hover:bg-gray-50 transition-colors text-gray-500"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </button>
            </div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">City / Country B</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchB"
                    @focus="showDropdownB = true"
                    @input="showDropdownB = true"
                    @keydown.escape="showDropdownB = false"
                    @keydown.arrow-down.prevent="focusDropdownItem('b', 0)"
                    placeholder="Search city or country…"
                    autocomplete="off"
                    class="block w-full border border-gray-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm"
                />
                <span class="absolute right-3 top-3.5 text-gray-400 pointer-events-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
            </div>
            <div
                x-show="showDropdownB && filteredCitiesB.length > 0"
                x-cloak
                @click.outside="showDropdownB = false"
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto"
            >
                <template x-for="(city, idx) in filteredCitiesB" :key="city.name + city.tz">
                    <button
                        type="button"
                        :id="'dropdown-b-' + idx"
                        @click="selectCity('b', city)"
                        @keydown.arrow-down.prevent="focusDropdownItem('b', idx + 1)"
                        @keydown.arrow-up.prevent="focusDropdownItem('b', idx - 1)"
                        @keydown.escape="showDropdownB = false"
                        class="w-full text-left px-4 py-3 text-sm hover:bg-gray-50 flex items-center justify-between"
                        :class="cityB && cityB.tz === city.tz && cityB.name === city.name ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700'"
                    >
                        <span x-text="city.name + ', ' + city.country"></span>
                        <span class="text-xs text-gray-400 ml-2 shrink-0" x-text="city.tz"></span>
                    </button>
                </template>
            </div>
            <div x-show="cityB" class="mt-2 text-xs text-gray-500 font-medium" x-text="cityB ? getOffsetLabel(cityB.tz) : ''"></div>
        </div>
    </div>

    {{-- Mobile swap button --}}
    <div class="flex sm:hidden justify-center -mt-4">
        <button
            type="button"
            @click="swapCities()"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
        >
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Swap cities
        </button>
    </div>

    {{-- ── Date row + Quick presets ── --}}
    <div class="flex flex-wrap items-start gap-6">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date</label>
            <input
                type="date"
                x-model="dateStr"
                class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm"
            />
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Quick pairs</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="preset in presets" :key="preset.label">
                    <button
                        type="button"
                        @click="applyPreset(preset)"
                        class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        x-text="preset.label"
                    ></button>
                </template>
            </div>
        </div>
    </div>

    {{-- ── Advanced options foldout ── --}}
    <div class="border border-gray-200 rounded-xl overflow-hidden">
        <button
            type="button"
            @click="showAdvanced = !showAdvanced"
            class="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
        >
            <span class="flex items-center gap-2">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                Advanced options
                <span class="text-xs font-normal text-gray-400" x-show="!showAdvanced">
                    — working hours currently <span x-text="pad(workStart) + ':00–' + pad(workEnd) + ':00'"></span>
                </span>
            </span>
            <svg
                class="h-4 w-4 text-gray-400 transition-transform duration-200"
                :class="showAdvanced ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div
            x-show="showAdvanced"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="px-5 pb-5 pt-1 border-t border-gray-100"
        >
            <p class="text-xs text-gray-500 mb-4 mt-3">Set your team's working hours. Both cities will use the same window.</p>
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Start time</label>
                    <select x-model.number="workStart" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-primary-500 shadow-sm">
                        <template x-for="opt in hourOptions" :key="opt.value">
                            <option :value="opt.value" x-text="opt.label" :selected="opt.value === workStart"></option>
                        </template>
                    </select>
                </div>
                <div class="text-gray-400 text-sm mt-5">to</div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">End time</label>
                    <select x-model.number="workEnd" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-primary-500 shadow-sm">
                        <template x-for="opt in hourOptions" :key="opt.value">
                            <option :value="opt.value" x-text="opt.label" :selected="opt.value === workEnd"></option>
                        </template>
                    </select>
                </div>
                <div class="text-xs text-gray-400 mt-5" x-show="workEnd <= workStart">
                    ⚠ End time must be after start time
                </div>
            </div>
        </div>
    </div>

    {{-- ── Timeline (shown when both cities selected) ── --}}
    <template x-if="cityA && cityB && workEnd > workStart">
        <div class="space-y-6">

            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-5 text-xs text-gray-600">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-blue-200 border border-blue-300"></div>
                    <span x-text="cityA?.name + ' only'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-emerald-200 border border-emerald-300"></div>
                    <span x-text="cityB?.name + ' only'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-primary-500"></div>
                    <span class="font-medium">Overlap</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-gray-100 border border-gray-200"></div>
                    <span>Off hours</span>
                </div>
            </div>

            {{-- Grid --}}
            <div class="overflow-x-auto -mx-1 px-1">
                <div style="min-width: 480px;">

                    {{-- City A row --}}
                    <div class="flex items-stretch gap-2 mb-1.5">
                        <div class="w-28 shrink-0 flex items-center text-xs font-semibold text-gray-600 truncate" x-text="cityA?.name"></div>
                        <div class="flex flex-1 gap-px">
                            <template x-for="cell in hours" :key="'a-' + cell.h">
                                <div
                                    class="flex-1 h-10 rounded-sm transition-colors relative group cursor-default"
                                    :class="{
                                        'bg-primary-500': cell.overlap,
                                        'bg-blue-200 border border-blue-300': cell.aWorking && !cell.overlap,
                                        'bg-gray-100 border border-gray-200': !cell.aWorking,
                                    }"
                                >
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                        <span x-text="pad(cell.h) + ':00'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- City B row --}}
                    <div class="flex items-stretch gap-2 mb-2">
                        <div class="w-28 shrink-0 flex items-center text-xs font-semibold text-gray-600 truncate" x-text="cityB?.name"></div>
                        <div class="flex flex-1 gap-px">
                            <template x-for="cell in hours" :key="'b-' + cell.h">
                                <div
                                    class="flex-1 h-10 rounded-sm transition-colors relative group cursor-default"
                                    :class="{
                                        'bg-primary-500': cell.overlap,
                                        'bg-emerald-200 border border-emerald-300': cell.bWorking && !cell.overlap,
                                        'bg-gray-100 border border-gray-200': !cell.bWorking,
                                    }"
                                >
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                        <span x-text="pad(cell.bHour) + ':' + pad(cell.bMin)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Hour labels --}}
                    <div class="flex items-start gap-2 mt-1">
                        <div class="w-28 shrink-0 text-xs text-gray-400 leading-tight">
                            <div x-text="cityA?.name?.split(',')[0]"></div>
                            <div x-text="cityB?.name?.split(',')[0]"></div>
                        </div>
                        <div class="flex-1 relative" style="min-height: 2.5rem;">
                            <template x-for="cell in hours" :key="'labels-' + cell.h">
                                <div
                                    class="absolute top-0 text-center"
                                    x-show="cell.h % 6 === 0"
                                    :style="'left: calc(' + (cell.h / 24 * 100) + '% )'"
                                >
                                    <div class="text-xs text-gray-400 leading-tight" x-text="pad(cell.h)"></div>
                                    <div class="text-xs text-gray-300 leading-tight" x-text="pad(cell.bHour)"></div>
                                </div>
                            </template>
                            {{-- 24:00 / end label --}}
                            <div class="absolute top-0 right-0 text-center">
                                <div class="text-xs text-gray-400 leading-tight">24</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div
                class="rounded-xl p-5"
                :class="overlapCount > 0 ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-amber-200'"
            >
                <template x-if="overlapCount > 0">
                    <div>
                        <p class="font-bold text-green-800">
                            <span x-text="overlapCount"></span> hour<span x-show="overlapCount !== 1">s</span> of overlap
                        </p>
                        <p class="text-green-700 text-sm mt-1" x-text="overlapSummary"></p>
                    </div>
                </template>
                <template x-if="overlapCount === 0">
                    <div>
                        <p class="font-bold text-amber-800">No overlap in working hours</p>
                        <p class="text-amber-700 text-sm mt-1">
                            The <span x-text="pad(workStart) + ':00–' + pad(workEnd) + ':00'"></span> working window in <span x-text="cityA?.name"></span> and <span x-text="cityB?.name"></span> don't overlap on this date.
                            Try expanding your working hours in Advanced options, or pick cities closer together.
                        </p>
                    </div>
                </template>
            </div>

            {{-- Current times --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-2" x-text="cityA?.name?.toUpperCase() + ', ' + cityA?.country?.toUpperCase()"></p>
                    <p class="text-3xl font-bold text-gray-900 tabular-nums" x-text="getCurrentTime(cityA?.tz)"></p>
                    <p class="text-xs text-gray-500 mt-1.5" x-text="getOffsetLabel(cityA?.tz)"></p>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-2" x-text="cityB?.name?.toUpperCase() + ', ' + cityB?.country?.toUpperCase()"></p>
                    <p class="text-3xl font-bold text-gray-900 tabular-nums" x-text="getCurrentTime(cityB?.tz)"></p>
                    <p class="text-xs text-gray-500 mt-1.5" x-text="getOffsetLabel(cityB?.tz)"></p>
                </div>
            </div>

            {{-- DST notice --}}
            <template x-if="isDstDate">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-sm text-blue-800">
                    <p class="font-semibold mb-1">DST is active for one or both cities on this date</p>
                    <p class="text-blue-700 text-xs leading-relaxed">
                        The offsets shown above are correct for <span x-text="dateStr"></span> — the tool automatically uses the right summer or winter offset via the browser's built-in timezone database.
                        Change the date to see how clock changes affect the overlap window.
                    </p>
                </div>
            </template>

        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!cityA || !cityB">
        <div class="text-center py-12 text-gray-400">
            <svg class="h-10 w-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
            <p class="text-sm">Select two cities above to see the working-hours overlap.</p>
        </div>
    </template>

</div>

{{-- ── SEO content ── --}}
<div class="space-y-8 mt-10">

    {{-- How it works --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-5">How it works</h2>
        <ol class="space-y-4">
            <li class="flex gap-4">
                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center">1</span>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Search for two cities</p>
                    <p class="text-sm text-gray-500 mt-0.5">Type any city name or country into the search boxes. Results update as you type — over 85 cities are included, spanning every major timezone.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center">2</span>
                <div>
                    <p class="font-medium text-gray-800 text-sm">See the overlap chart instantly</p>
                    <p class="text-sm text-gray-500 mt-0.5">A colour-coded 24-hour timeline appears immediately. Blue shows when only your first city is working, green shows when only the second city is working, and the primary colour highlights the overlap window.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center">3</span>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Change the date to test DST transitions</p>
                    <p class="text-sm text-gray-500 mt-0.5">Use the date picker to check overlap on a specific day — useful for planning calls around daylight saving time changes. The chart recalculates immediately with the correct summer or winter offset.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-sm font-bold flex items-center justify-center">4</span>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Adjust working hours if needed</p>
                    <p class="text-sm text-gray-500 mt-0.5">Not everyone works 9–5. Open Advanced options to set custom start and end times — for example, 08:00–16:00 for early-bird teams or 10:00–18:00 for late-start offices.</p>
                </div>
            </li>
        </ol>
    </div>

    {{-- Examples --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Real-world examples</h2>
        <div class="space-y-6">

            <div class="border-l-4 border-primary-400 pl-5">
                <h3 class="font-semibold text-gray-800 text-sm mb-1">London ↔ New York (typical agency pair)</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    In summer (BST/EDT): London is UTC+1 and New York is UTC−4 — a 5-hour gap.
                    London's 14:00–17:00 aligns with New York's 09:00–12:00, giving <strong>3 hours of overlap</strong>.
                    In winter (GMT/EST): London is UTC+0 and New York UTC−5, the same 5-hour gap applies.
                    Because both cities observe summer time, the overlap window stays at 3 hours year-round.
                </p>
            </div>

            <div class="border-l-4 border-emerald-400 pl-5">
                <h3 class="font-semibold text-gray-800 text-sm mb-1">London ↔ Singapore (remote engineering team)</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Singapore is UTC+8 and does not observe DST. In summer, London (BST, UTC+1) is 7 hours behind Singapore — meaning London's 9am is Singapore's 4pm, and London's 5pm is Singapore's midnight.
                    The overlap is just 1 hour (London 16:00–17:00 = Singapore 23:00–00:00).
                    For distributed teams in these cities, staggered hours or an async-first culture is usually the practical answer.
                </p>
            </div>

        </div>
    </div>

    {{-- FAQ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Frequently asked questions</h2>
        <div class="space-y-5">

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">Does the tool account for daylight saving time?</h3>
                <p class="text-sm text-gray-600 mt-1 leading-relaxed">Yes — fully. The tool uses the browser's built-in <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">Intl.DateTimeFormat</code> API with IANA timezone identifiers, which is automatically updated by your browser and operating system. This means it handles every country's DST rules correctly, including unusual cases like Israel's irregular DST dates and Morocco's reversed clock change.</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">Can I use different working hours for each city?</h3>
                <p class="text-sm text-gray-600 mt-1 leading-relaxed">The current version uses the same working-hours window for both cities — the assumption being you want to find time when both teams are available simultaneously. Open "Advanced options" to change the window from the default 09:00–17:00 to whatever suits your team. A future version may allow independent hours per city.</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">Why does London ↔ Sydney show no overlap?</h3>
                <p class="text-sm text-gray-600 mt-1 leading-relaxed">Sydney (AEDT in summer) is UTC+11 — 11 hours ahead of London (GMT). Sydney's 09:00 is London's 22:00 the previous evening, and Sydney's 17:00 is London's 06:00. There's no overlap within standard 9–5 hours. For these pairs, teams typically use an early call from the Sydney side or a late call from the London side, or adopt async communication tools as the primary workflow.</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">How are half-hour timezone offsets handled?</h3>
                <p class="text-sm text-gray-600 mt-1 leading-relaxed">Some countries use non-integer UTC offsets: India is UTC+5:30, Nepal is UTC+5:45, and Sri Lanka is UTC+5:30. The tool handles these correctly — when computing City B's local time for each hour on the chart, it uses the precise offset in minutes, so the displayed time for Mumbai will correctly show 14:30 when London is at 09:00.</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 text-sm">Does any data leave my device?</h3>
                <p class="text-sm text-gray-600 mt-1 leading-relaxed">No. The entire tool runs in your browser using JavaScript. No city selections, dates, or results are sent to any server. The timezone calculations are performed locally using the <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">Intl</code> API built into every modern browser.</p>
            </div>

        </div>
    </div>

</div>

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "SoftwareApplication",
            "name": "Time Zone Overlap Checker",
            "description": "Free browser-based tool that shows the working-hours overlap between any two cities, with full DST support.",
            "url": "{{ route('tools.show', 'timezone-overlap') }}",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Any",
            "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" }
        },
        {
            "@@type": "FAQPage",
            "mainEntity": [
                {
                    "@@type": "Question",
                    "name": "Does the tool account for daylight saving time?",
                    "acceptedAnswer": { "@@type": "Answer", "text": "Yes. The tool uses the browser's built-in Intl.DateTimeFormat API with IANA timezone identifiers, which handles every country's DST rules correctly." }
                },
                {
                    "@@type": "Question",
                    "name": "Can I use different working hours for each city?",
                    "acceptedAnswer": { "@@type": "Answer", "text": "The current version uses the same working-hours window for both cities. Open Advanced options to change the window from the default 09:00-17:00." }
                },
                {
                    "@@type": "Question",
                    "name": "Why does London to Sydney show no overlap?",
                    "acceptedAnswer": { "@@type": "Answer", "text": "Sydney is up to 11 hours ahead of London. Within standard 9-5 hours there is no overlap. Teams in these cities typically use staggered hours or async workflows." }
                },
                {
                    "@@type": "Question",
                    "name": "How are half-hour timezone offsets like India handled?",
                    "acceptedAnswer": { "@@type": "Answer", "text": "The tool uses the precise UTC offset in minutes, so India's UTC+5:30 and Nepal's UTC+5:45 are handled correctly in the visual chart." }
                },
                {
                    "@@type": "Question",
                    "name": "Does any data leave my device?",
                    "acceptedAnswer": { "@@type": "Answer", "text": "No. The entire tool runs in your browser. No city selections or results are sent to any server." }
                }
            ]
        }
    ]
}
</script>
@endpush

@push('scripts')
<script>
function timezoneOverlap() {
    return {
        cityA: null,
        cityB: null,
        searchA: '',
        searchB: '',
        showDropdownA: false,
        showDropdownB: false,
        showAdvanced: false,
        workStart: 9,
        workEnd: 17,
        dateStr: '',
        now: null,

        // Pre-built hour options so selects render correctly on init
        hourOptions: Array.from({ length: 25 }, (_, h) => ({
            value: h,
            label: String(h).padStart(2, '0') + ':00',
        })),

        // ── City data ───────────────────────────────────────────────
        cities: [
            // Americas
            { name: 'Honolulu',           country: 'USA',             tz: 'Pacific/Honolulu' },
            { name: 'Anchorage',          country: 'USA',             tz: 'America/Anchorage' },
            { name: 'Los Angeles',        country: 'USA',             tz: 'America/Los_Angeles' },
            { name: 'San Francisco',      country: 'USA',             tz: 'America/Los_Angeles' },
            { name: 'Seattle',            country: 'USA',             tz: 'America/Los_Angeles' },
            { name: 'Vancouver',          country: 'Canada',          tz: 'America/Vancouver' },
            { name: 'Denver',             country: 'USA',             tz: 'America/Denver' },
            { name: 'Phoenix',            country: 'USA',             tz: 'America/Phoenix' },
            { name: 'Chicago',            country: 'USA',             tz: 'America/Chicago' },
            { name: 'Dallas',             country: 'USA',             tz: 'America/Chicago' },
            { name: 'Houston',            country: 'USA',             tz: 'America/Chicago' },
            { name: 'Mexico City',        country: 'Mexico',          tz: 'America/Mexico_City' },
            { name: 'New York',           country: 'USA',             tz: 'America/New_York' },
            { name: 'Toronto',            country: 'Canada',          tz: 'America/Toronto' },
            { name: 'Boston',             country: 'USA',             tz: 'America/New_York' },
            { name: 'Miami',              country: 'USA',             tz: 'America/New_York' },
            { name: 'Atlanta',            country: 'USA',             tz: 'America/New_York' },
            { name: 'Washington DC',      country: 'USA',             tz: 'America/New_York' },
            { name: 'Bogota',             country: 'Colombia',        tz: 'America/Bogota' },
            { name: 'Lima',               country: 'Peru',            tz: 'America/Lima' },
            { name: 'Halifax',            country: 'Canada',          tz: 'America/Halifax' },
            { name: 'Santiago',           country: 'Chile',           tz: 'America/Santiago' },
            { name: 'Caracas',            country: 'Venezuela',       tz: 'America/Caracas' },
            { name: 'São Paulo',          country: 'Brazil',          tz: 'America/Sao_Paulo' },
            { name: 'Rio de Janeiro',     country: 'Brazil',          tz: 'America/Sao_Paulo' },
            { name: 'Buenos Aires',       country: 'Argentina',       tz: 'America/Argentina/Buenos_Aires' },
            // Europe
            { name: 'Reykjavik',          country: 'Iceland',         tz: 'Atlantic/Reykjavik' },
            { name: 'London',             country: 'UK',              tz: 'Europe/London' },
            { name: 'Dublin',             country: 'Ireland',         tz: 'Europe/Dublin' },
            { name: 'Lisbon',             country: 'Portugal',        tz: 'Europe/Lisbon' },
            { name: 'Madrid',             country: 'Spain',           tz: 'Europe/Madrid' },
            { name: 'Barcelona',          country: 'Spain',           tz: 'Europe/Madrid' },
            { name: 'Paris',              country: 'France',          tz: 'Europe/Paris' },
            { name: 'Brussels',           country: 'Belgium',         tz: 'Europe/Brussels' },
            { name: 'Amsterdam',          country: 'Netherlands',     tz: 'Europe/Amsterdam' },
            { name: 'Zurich',             country: 'Switzerland',     tz: 'Europe/Zurich' },
            { name: 'Berlin',             country: 'Germany',         tz: 'Europe/Berlin' },
            { name: 'Frankfurt',          country: 'Germany',         tz: 'Europe/Berlin' },
            { name: 'Vienna',             country: 'Austria',         tz: 'Europe/Vienna' },
            { name: 'Prague',             country: 'Czech Republic',  tz: 'Europe/Prague' },
            { name: 'Warsaw',             country: 'Poland',          tz: 'Europe/Warsaw' },
            { name: 'Budapest',           country: 'Hungary',         tz: 'Europe/Budapest' },
            { name: 'Rome',               country: 'Italy',           tz: 'Europe/Rome' },
            { name: 'Milan',              country: 'Italy',           tz: 'Europe/Rome' },
            { name: 'Stockholm',          country: 'Sweden',          tz: 'Europe/Stockholm' },
            { name: 'Oslo',               country: 'Norway',          tz: 'Europe/Oslo' },
            { name: 'Copenhagen',         country: 'Denmark',         tz: 'Europe/Copenhagen' },
            { name: 'Helsinki',           country: 'Finland',         tz: 'Europe/Helsinki' },
            { name: 'Bucharest',          country: 'Romania',         tz: 'Europe/Bucharest' },
            { name: 'Athens',             country: 'Greece',          tz: 'Europe/Athens' },
            { name: 'Istanbul',           country: 'Turkey',          tz: 'Europe/Istanbul' },
            { name: 'Moscow',             country: 'Russia',          tz: 'Europe/Moscow' },
            // Africa & Middle East
            { name: 'Cairo',              country: 'Egypt',           tz: 'Africa/Cairo' },
            { name: 'Casablanca',         country: 'Morocco',         tz: 'Africa/Casablanca' },
            { name: 'Lagos',              country: 'Nigeria',         tz: 'Africa/Lagos' },
            { name: 'Nairobi',            country: 'Kenya',           tz: 'Africa/Nairobi' },
            { name: 'Johannesburg',       country: 'South Africa',    tz: 'Africa/Johannesburg' },
            { name: 'Riyadh',             country: 'Saudi Arabia',    tz: 'Asia/Riyadh' },
            { name: 'Dubai',              country: 'UAE',             tz: 'Asia/Dubai' },
            { name: 'Tel Aviv',           country: 'Israel',          tz: 'Asia/Jerusalem' },
            // Asia
            { name: 'Kabul',              country: 'Afghanistan',     tz: 'Asia/Kabul' },
            { name: 'Karachi',            country: 'Pakistan',        tz: 'Asia/Karachi' },
            { name: 'Mumbai',             country: 'India',           tz: 'Asia/Kolkata' },
            { name: 'Delhi',              country: 'India',           tz: 'Asia/Kolkata' },
            { name: 'Kolkata',            country: 'India',           tz: 'Asia/Kolkata' },
            { name: 'Kathmandu',          country: 'Nepal',           tz: 'Asia/Kathmandu' },
            { name: 'Dhaka',              country: 'Bangladesh',      tz: 'Asia/Dhaka' },
            { name: 'Colombo',            country: 'Sri Lanka',       tz: 'Asia/Colombo' },
            { name: 'Yangon',             country: 'Myanmar',         tz: 'Asia/Yangon' },
            { name: 'Bangkok',            country: 'Thailand',        tz: 'Asia/Bangkok' },
            { name: 'Ho Chi Minh City',   country: 'Vietnam',         tz: 'Asia/Ho_Chi_Minh' },
            { name: 'Jakarta',            country: 'Indonesia',       tz: 'Asia/Jakarta' },
            { name: 'Kuala Lumpur',       country: 'Malaysia',        tz: 'Asia/Kuala_Lumpur' },
            { name: 'Singapore',          country: 'Singapore',       tz: 'Asia/Singapore' },
            { name: 'Manila',             country: 'Philippines',     tz: 'Asia/Manila' },
            { name: 'Hong Kong',          country: 'China',           tz: 'Asia/Hong_Kong' },
            { name: 'Shanghai',           country: 'China',           tz: 'Asia/Shanghai' },
            { name: 'Beijing',            country: 'China',           tz: 'Asia/Shanghai' },
            { name: 'Taipei',             country: 'Taiwan',          tz: 'Asia/Taipei' },
            { name: 'Seoul',              country: 'South Korea',     tz: 'Asia/Seoul' },
            { name: 'Tokyo',              country: 'Japan',           tz: 'Asia/Tokyo' },
            { name: 'Osaka',              country: 'Japan',           tz: 'Asia/Tokyo' },
            // Pacific & Oceania
            { name: 'Perth',              country: 'Australia',       tz: 'Australia/Perth' },
            { name: 'Adelaide',           country: 'Australia',       tz: 'Australia/Adelaide' },
            { name: 'Brisbane',           country: 'Australia',       tz: 'Australia/Brisbane' },
            { name: 'Sydney',             country: 'Australia',       tz: 'Australia/Sydney' },
            { name: 'Melbourne',          country: 'Australia',       tz: 'Australia/Melbourne' },
            { name: 'Auckland',           country: 'New Zealand',     tz: 'Pacific/Auckland' },
        ],

        presets: [
            { label: 'London ↔ New York',    a: 'London',    b: 'New York' },
            { label: 'London ↔ Singapore',   a: 'London',    b: 'Singapore' },
            { label: 'London ↔ Sydney',      a: 'London',    b: 'Sydney' },
            { label: 'New York ↔ Tokyo',     a: 'New York',  b: 'Tokyo' },
            { label: 'Berlin ↔ Mumbai',      a: 'Berlin',    b: 'Mumbai' },
            { label: 'New York ↔ London',    a: 'New York',  b: 'London' },
        ],

        // ── Init ────────────────────────────────────────────────────
        init() {
            this.dateStr = new Date().toISOString().slice(0, 10);
            this.now = new Date();
            const london  = this.cities.find(c => c.name === 'London');
            const newYork = this.cities.find(c => c.name === 'New York');
            if (london)  this.selectCity('a', london);
            if (newYork) this.selectCity('b', newYork);
            setInterval(() => { this.now = new Date(); }, 30000);
        },

        // ── Filtered lists ──────────────────────────────────────────
        get filteredCitiesA() {
            const q = this.searchA.toLowerCase();
            if (!q) return this.cities;
            return this.cities.filter(c =>
                c.name.toLowerCase().includes(q) ||
                c.country.toLowerCase().includes(q) ||
                c.tz.toLowerCase().includes(q)
            );
        },
        get filteredCitiesB() {
            const q = this.searchB.toLowerCase();
            if (!q) return this.cities;
            return this.cities.filter(c =>
                c.name.toLowerCase().includes(q) ||
                c.country.toLowerCase().includes(q) ||
                c.tz.toLowerCase().includes(q)
            );
        },

        selectCity(side, city) {
            if (side === 'a') {
                this.cityA = city;
                this.searchA = city.name + ', ' + city.country;
                this.showDropdownA = false;
            } else {
                this.cityB = city;
                this.searchB = city.name + ', ' + city.country;
                this.showDropdownB = false;
            }
        },

        swapCities() {
            [this.cityA, this.cityB]     = [this.cityB, this.cityA];
            [this.searchA, this.searchB] = [this.searchB, this.searchA];
        },

        applyPreset(preset) {
            const a = this.cities.find(c => c.name === preset.a);
            const b = this.cities.find(c => c.name === preset.b);
            if (a) this.selectCity('a', a);
            if (b) this.selectCity('b', b);
        },

        focusDropdownItem(side, idx) {
            const el = document.getElementById(`dropdown-${side}-${idx}`);
            if (el) el.focus();
        },

        // ── UTC offset calculation (DST-aware via Intl API) ─────────
        getOffsetMinutes(tz) {
            if (!tz) return 0;
            try {
                const refDate = new Date(this.dateStr + 'T12:00:00Z');
                const date = isNaN(refDate.getTime()) ? new Date() : refDate;
                const f = new Intl.DateTimeFormat('en-US', {
                    timeZone: tz, hour12: false,
                    year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit', minute: '2-digit', second: '2-digit',
                });
                const parts = f.formatToParts(date);
                const get = t => parseInt(parts.find(p => p.type === t)?.value || '0', 10);
                const h = get('hour');
                const local = Date.UTC(get('year'), get('month') - 1, get('day'), h === 24 ? 0 : h, get('minute'), get('second'));
                return Math.round((local - date.getTime()) / 60000);
            } catch { return 0; }
        },

        // ── 24-hour grid ────────────────────────────────────────────
        get hours() {
            if (!this.cityA || !this.cityB) return [];
            const diffMinutes = this.getOffsetMinutes(this.cityB.tz) - this.getOffsetMinutes(this.cityA.tz);
            return Array.from({ length: 24 }, (_, h) => {
                const aWorking = h >= this.workStart && h < this.workEnd;
                const bTotal   = ((h * 60 + diffMinutes) % 1440 + 1440) % 1440;
                const bHour    = Math.floor(bTotal / 60);
                const bMin     = bTotal % 60;
                const bWorking = bHour >= this.workStart && bHour < this.workEnd;
                return { h, bHour, bMin, aWorking, bWorking, overlap: aWorking && bWorking };
            });
        },

        get overlapCount() {
            return this.hours.filter(c => c.overlap).length;
        },

        get overlapSummary() {
            const cells = this.hours.filter(c => c.overlap);
            if (!cells.length) return '';
            const blocks = [];
            let start = cells[0], prev = cells[0];
            for (let i = 1; i <= cells.length; i++) {
                const cur = cells[i];
                if (!cur || cur.h !== prev.h + 1) {
                    blocks.push({ start, end: prev });
                    if (cur) { start = cur; prev = cur; }
                } else { prev = cur; }
            }
            return blocks.map(b => {
                const aS = this.pad(b.start.h) + ':00';
                const aE = this.pad(b.end.h + 1) + ':00';
                const bS = this.pad(b.start.bHour) + ':' + this.pad(b.start.bMin);
                const bE = this.pad(b.end.bHour + 1) + ':' + this.pad(b.end.bMin);
                return `${this.cityA.name} ${aS}–${aE}  ↔  ${this.cityB.name} ${bS}–${bE}`;
            }).join('  |  ');
        },

        get isDstDate() {
            return [this.cityA?.tz, this.cityB?.tz].filter(Boolean).some(tz => {
                try {
                    const year = this.dateStr.slice(0, 4);
                    return this._rawOffset(tz, new Date(year + '-01-15T12:00:00Z')) !== this._rawOffset(tz, new Date(this.dateStr + 'T12:00:00Z'));
                } catch { return false; }
            });
        },

        _rawOffset(tz, date) {
            const f = new Intl.DateTimeFormat('en-US', {
                timeZone: tz, hour12: false,
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
            });
            const parts = f.formatToParts(date);
            const get = t => parseInt(parts.find(p => p.type === t)?.value || '0', 10);
            const h = get('hour');
            return Math.round((Date.UTC(get('year'), get('month')-1, get('day'), h===24?0:h, get('minute'), get('second')) - date.getTime()) / 60000);
        },

        // ── Display helpers ─────────────────────────────────────────
        getCurrentTime(tz) {
            if (!tz) return '--:--';
            try {
                return new Intl.DateTimeFormat('en-GB', {
                    timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false,
                }).format(this.now || new Date());
            } catch { return '--:--'; }
        },

        getOffsetLabel(tz) {
            if (!tz) return '';
            try {
                const mins = this.getOffsetMinutes(tz);
                const sign = mins >= 0 ? '+' : '-';
                const abs  = Math.abs(mins);
                const h    = Math.floor(abs / 60);
                const m    = abs % 60;
                const offset = `UTC${sign}${this.pad(h)}${m ? ':' + this.pad(m) : ''}`;
                const parts  = new Intl.DateTimeFormat('en-GB', {
                    timeZone: tz, timeZoneName: 'short', year: 'numeric',
                }).formatToParts(new Date(this.dateStr + 'T12:00:00Z'));
                const tzName = parts.find(p => p.type === 'timeZoneName')?.value || '';
                return tzName ? `${tzName} · ${offset}` : offset;
            } catch { return tz; }
        },

        pad(n) { return String(n).padStart(2, '0'); },
    };
}
</script>
@endpush
