<x-layouts.app :title="__('Project')">
    @include('partials.tittle', [
        'title' => __('Projects'),
        'subheading' => __('Management of registered :name',['name'=> __('projects')])
    ])

    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                    <div class="py-3 px-4 flex justify-between items-center gap-4">
                        <div class="relative max-w-xs">
                            <label class="sr-only">{{__('Search')}}</label>
                            <flux:input name="hs-table-with-pagination-search" id="hs-table-with-pagination-search"  icon="magnifying-glass" placeholder="{{__('Search')}}"/>
                        </div>

                        <a href="{{ route('projects.create') }}">
                            <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                        </a>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="projectsTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>

                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Name')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Subproject')}}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($projects as $project)
                                <tr >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $project->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $project->name_sub_project }}</td>
                                    <td class="px-6 py-4 flex justify-center gap-2">
                                        <!-- Botón Editar -->
                                        <flux:modal.trigger name="edit-project">
                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                icon="pencil-square"
                                                onclick='openEditModal({{$project}})'>
                                            </flux:button>

                                        </flux:modal.trigger>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ __('There are no registered :name.', ['name'=>__('projects')]) }}
                                    </td>
                                    <td></td>
                                </tr>
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="py-1 px-4">
                        {{ $projects->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- Modal Único para Editar -->
    <flux:modal name="edit-project" variant="flyout">
        <form id="editProjectForm" method="POST" action="" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <flux:heading size="lg">{{ __("Edit :name", ['name' => __('Project')]) }}</flux:heading>
                <flux:text class="mt-2">{{__('Update this :name\'s details.',['name'=>__('project')])}}</flux:text>
            </div>

            <flux:input id="name" label="{{__('Name')}}" name="name"  />
            <label   class="block text-base">
                {{ __('Subprojects') }}
            </label>
            <!-- Input Group -->
            <div id="hs-destroy-and-reinitialize-wrapper-for-copy" class="space-y-3">
                <div id="hs-destroy-and-reinitialize-content-for-copy">
                    <div class="relative">

                        <flux:input placeholder="{{__('Subproject')}}" name="subprojects[]">
                            <x-slot name="iconTrailing">
                                <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1 remove-subproject"  data-hs-copy-markup-delete-item="" />
                            </x-slot>
                        </flux:input>

                    </div>
                </div>
            </div>

            <p class="mt-3 text-end">
                <button id="hs-copy-markup-to-destroy" type="button" data-hs-copy-markup='{
                    "targetSelector": "#hs-destroy-and-reinitialize-content-for-copy",
                    "wrapperSelector": "#hs-destroy-and-reinitialize-wrapper-for-copy"
                    }'
                        class="py-1.5 px-2 inline-flex items-center gap-x-1 text-xs font-medium rounded-full border border-dashed border-gray-200 bg-white text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    {{__('Add')}}
                </button>
            </p>
            <!-- End Input Group -->


            <div id="formErrors" class="text-red-500 text-sm"></div>
            <div class="flex gap-3">

                <flux:spacer />
                <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>

                <flux:modal.trigger name="confirm-delete">
                    <flux:button
                        type="button"
                        icon="archive-box-x-mark"
                        variant="danger">
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </form>
    </flux:modal>

    <!-- Modal de confirmación -->
    <flux:modal name="confirm-delete" class="md:w-96" :dismissible="true">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('Project')]) }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this :name.", ['name' => __('project')]) }}</p>
                    <p>{{ __('This action cannot be reversed.') }}</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <!-- Botón cancelar -->
                <flux:modal.close >
                    <flux:button variant="ghost" x-on:click="$flux.modal('confirm-delete').close()">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <!-- Botón confirmar -->
                <form id="deleteProjectForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger" icon="archive-box-x-mark">
                        {{ __('Delete') }}
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:modal>


    <!-- Script para llenar modal dinámico -->
    <script>
        function openEditModal(project) {
            document.getElementById('name').value = project.name;
            document.getElementById('editProjectForm').action = `/projects/${project.id}`;
            document.getElementById('deleteProjectForm').action = `/projects/${project.id}` ;


            // Referencias
            const wrapper = document.getElementById('hs-destroy-and-reinitialize-wrapper-for-copy');
            const contentTemplate = document.getElementById('hs-destroy-and-reinitialize-content-for-copy');

            // Limpia los subproyectos actuales
            wrapper.innerHTML = '';

            // Si tiene subproyectos en el backend
            if (project.subprojects && project.subprojects.length > 0) {
                project.subprojects.forEach(sub => {
                    // Clona el contenido base
                    const clone = contentTemplate.cloneNode(true);
                    const input = clone.querySelector('input[name="subprojects[]"]');
                    input.value = sub; // Asigna el valor desde el array
                    wrapper.appendChild(clone);
                });
            } else {

                const clone = contentTemplate.cloneNode(true);
                const input = clone.querySelector('input[name="subprojects[]"]');
                input.value = '';
                wrapper.appendChild(clone);
            }

            // Re-inicializa el componente de Preline (importante)
            if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
                window.HSStaticMethods.autoInit();
            }

        }

        document.addEventListener('DOMContentLoaded', () => {
            const wrapper = document.getElementById('hs-destroy-and-reinitialize-wrapper-for-copy');

            wrapper.addEventListener('click', (event) => {
                if (event.target.closest('.remove-subproject')) {
                    const item = event.target.closest('[id^="hs-destroy-and-reinitialize-content-for-copy"]');
                    if (item) item.remove();
                }
            });
        });

    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#projectsTable').DataTable({
                    dom: '',
                    language: {
                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
                $("#editProjectForm").on("submit", function (e) {
                    e.preventDefault(); // evita reload

                    let form = $(this);
                    let action = form.attr("action");
                    let data = form.serialize();

                    $("#formErrors").html("");
                    $.ajax({
                        url: action,
                        method: "POST", // 👈 en vez de PUT
                        data: data + "&_method=PUT",
                        success: function (response) {
                            Flux.modal('edit-project').close();
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Project')])}}"
                                }
                            }));
                        },
                        error: function (xhr) {

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let errorMessages = Object.values(errors)
                                    .map(e => e.join("<br>"))
                                    .join("<br>");
                                $("#formErrors").html(errorMessages);
                                Flux.modal('edit-project').show();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>

