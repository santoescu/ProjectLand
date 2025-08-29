<x-layouts.app :title="__('Users')">
    @include('partials.tittle', [
        'title' => __('Users'),
        'subheading' => __('Management of registered :name',['name'=> __('users')])
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

                        <a href="{{ route('users.create') }}">
                            <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                        </a>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="usersTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>

                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Name')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Email')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Role')}}</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse ($users as $user)
                                <tr >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $user->role }}</td>
                                    <td class="px-6 py-4 flex justify-center gap-2">
                                        <!-- Botón Editar -->
                                        <flux:modal.trigger name="edit-user">
                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                icon="pencil-square"
                                                onclick='openEditModal({{$user}})'>
                                            </flux:button>

                                        </flux:modal.trigger>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                                        {{__('There are no registered :name.',['name'=>__('users')])}}
                                    </td>
                                    <td></td>
                                </tr>
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="py-1 px-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- Modal Único para Editar -->
    <flux:modal name="edit-user" variant="flyout">
        <form id="editUserForm" method="POST" action="" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <flux:heading size="lg">{{ __("Edit :name", ['name' => __('User')]) }}</flux:heading>
                <flux:text class="mt-2">{{__('Update this :name\'s details.',['name'=>__('user')])}}</flux:text>
            </div>

            <flux:input id="name" label="{{__('Name')}}" name="name"  />
            <flux:input id="email" label="{{__('Email')}}" name="email"  />

            <flux:select id="role" label="{{__('Role')}}" name="role" >
                <option value="accounting_assistant">{{ __('Accounting Assistant') }}</option>
                <option value="project_manager">{{ __('Project Manager') }}</option>
                <option value="director">{{ __('Director') }}</option>
            </flux:select>

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
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('User')]) }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this :name.", ['name' => __('user')]) }}</p>
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
                <form id="deleteUserForm" action="" method="POST">
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
        function openEditModal(user) {


            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('editUserForm').action = `/users/${user.id}`;
            document.getElementById('deleteUserForm').action = `/users/${user.id}` ;
        }

    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#usersTable').DataTable({
                    dom: '',
                    language: {

                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
                $("#editUserForm").on("submit", function (e) {
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
                            Flux.modal('edit-user').close();
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{__("Updated :name", ['name' => __('User')])}}"
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
                                Flux.modal('edit-user').show();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>

