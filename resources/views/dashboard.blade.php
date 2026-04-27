<x-layouts.app :title="__('Dashboard')">
    @include('partials.tittle', [
        'title' => __('Projects'),
        'subheading' => __('Registered projects overview')
    ])

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        @if ($projects->isEmpty())
            <section class="flex min-h-[320px] items-center justify-center rounded-lg border border-gray-200 bg-white p-10 text-center dark:border-neutral-700 dark:bg-neutral-800">
                <div class="max-w-md space-y-3">
                    <img
                        src="{{ asset('images/projects/project-01.svg') }}"
                        alt="{{ __('Default project illustration') }}"
                        class="mx-auto h-32 w-auto object-contain"
                    >
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ __('There are no registered projects yet.') }}</h2>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        {{ __('When you create a project, it will appear here with its assigned SVG or the default image.') }}
                    </p>
                </div>
            </section>
        @else
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                @foreach ($projects as $project)
                    @php
                        $svgNumber = str_pad((string) (($loop->index % 10) + 1), 2, '0', STR_PAD_LEFT);
                        $svgPath = "images/projects/project-{$svgNumber}.svg";
                        $imagePath = asset($svgPath);
                        $subprojects = is_array($project->subprojects ?? null) ? $project->subprojects : [];
                        $selectedProject = session('selected_project');
                        $isSelected = (string) data_get($selectedProject, 'id') === (string) $project->id;
                    @endphp

                    <form action="{{ route('dashboard.select-project') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <input type="hidden" name="project_name" value="{{ $project->name }}">

                        <button
                            type="submit"
                            class="w-full overflow-hidden rounded-lg border bg-white text-left transition hover:border-gray-300 dark:bg-neutral-800 dark:hover:border-neutral-600 {{ $isSelected ? 'border-gray-800 dark:border-white' : 'border-gray-200 dark:border-neutral-700' }}"
                        >
                            <div class="flex h-56 items-center justify-center border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-900">
                                <img
                                    src="{{ $imagePath }}"
                                    alt="{{ $project->name }}"
                                    class="h-[100%] w-full object-contain"
                                >
                            </div>

                            <div class="space-y-3 p-4">
                                <div class="space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                            {{ $project->name }}
                                        </h2>
                                        <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-neutral-700 dark:text-neutral-200">
                                            {{ trans_choice(':count subproject|:count subprojects', count($subprojects), ['count' => count($subprojects)]) }}
                                        </span>
                                    </div>
                                </div>

                                @if (count($subprojects) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($subprojects as $subproject)
                                            <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-neutral-900 dark:text-neutral-200">
                                                {{ $subproject }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('This project has no subprojects registered.') }}
                                    </p>
                                @endif
                            </div>
                        </button>
                    </form>
                @endforeach
            </section>
        @endif
    </div>
</x-layouts.app>
