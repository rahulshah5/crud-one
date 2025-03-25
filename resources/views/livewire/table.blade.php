<div>
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <x-admin.breadcrumb title="{{ $title }}s" />
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-0">
                <form class="position-relative table-src-form me-0">
                    <div class="input-group input-group ">
                        <i class="ti ti-search input-group-text"></i>
                        <input type="text" id="search-input" wire:model.live.debounce.500="search" class="form-control"
                            placeholder="Search here">
                    </div>
                </form>
                @if ($add_route && !is_null($form))
                    <button class="btn btn-outline-primary py-1 px-2 px-sm-4 fs-14 fw-medium rounded-3 hover-bg" x-data
                        @click="$dispatch('open-modal',{name:'add{{ $title }}'})">
                        <span class="py-sm-1 d-block">
                            <i class="ti ti-plus"></i>
                            <span>Add New {{ $title }} </span>
                        </span>
                    </button>
                @endif
            </div>
            @if (empty($values[0]))
                <h4 class="mt-2 text-muted text-center">No data found!</h4>
            @else
                <div class="default-table-area style-two default-table-width">
                    <div class="table-responsive py-3">
                        <table class="table table-hover align-middle">
                            <thead class="header-item">
                                <th>
                                    <span class="d-flex align-items-center gap-1">
                                        SN
                                        <button class="d-flex flex-column align-items-center gap-1"
                                            style="font-size: 11px;background: none;border: none;padding: 0;margin: 0;"
                                            wire:click="sortTable('id','{{ $sortOrder == 'asc' ? 'desc' : 'asc' }}')">
                                            @if ($sortBy == 'id' && $sortOrder == 'asc')
                                                <span style="font-weight: bold;"><i class="ti ti-arrow-up"></i></span>
                                            @elseif($sortBy == 'id' && $sortOrder == 'desc')
                                                <span style="font-weight: bold;"><i class="ti ti-arrow-down"></i></span>
                                            @else
                                                <span style="font-weight: bold;"><i class="ti ti-arrow-up"></i></span>
                                            @endif
                                        </button>
                                    </span>
                                </th>
                                @if ($values[0]->image)
                                    <th scope="col">Image</th>
                                @endif
                                @foreach ($values[0]->getAttributes() as $key => $val)
                                    @if (in_array($key, array_keys($table_fields)))
                                        <th scope="col">
                                            <span class="d-flex align-items-center gap-1">
                                                {{ $table_fields[$key] }}
                                                @if (in_array($key, $sortable))
                                                    <button class="d-flex flex-column align-items-center gap-1"
                                                        style="font-size: 11px;background: none;border: none;padding: 0;margin: 0;"
                                                        wire:click="sortTable('{{ $key }}','{{ $sortOrder == 'asc' ? 'desc' : 'asc' }}')">
                                                        @if ($sortBy == $key && $sortOrder == 'asc')
                                                            <span style="font-weight: bold;"><i
                                                                    class="ti ti-arrow-up"></i></span>
                                                        @elseif($sortBy == $key && $sortOrder == 'desc')
                                                            <span style="font-weight: bold;"><i
                                                                    class="ti ti-arrow-down"></i></span>
                                                        @else
                                                            <span style="font-weight: bold;"><i
                                                                    class="ti ti-arrow-up"></i></span>
                                                        @endif

                                                    </button>
                                                @endif
                                            </span>
                                        </th>
                                    @endif
                                @endforeach
                                <th>Actions</th>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($values as $value)
                                    <tr class="search-items" wire:key='{{ $value->id }}'>
                                        <td style="padding: 12px !important;">{{ $loop->iteration }}</td>
                                        @isset($value->image)
                                            <td style="padding: 12px !important;">
                                                @isset($value->image[0]->image)
                                                    {{-- @dd($value->image[0]->image) --}}
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('storage/' . $value->image[0]->image) }}"
                                                            class=" rounded-3 " style="width:60px;height:auto;object-fit: cover"
                                                            alt="td item image">
                                                    </div>
                                                @endisset
                                            </td>
                                        @endisset
                                        @foreach ($value->getAttributes() as $key => $val)
                                            @if (in_array($key, array_keys($table_fields)))
                                                <td class="text-dark " style="padding: 12px !important;">
                                                    @if ($key == 'description')
                                                        {!! Str::limit(strip_tags($val), '30', '...') !!}
                                                    @elseif ($key == 'status')
                                                        <button type='button'
                                                            wire:click="switchVisibility({{ $value->id }})"
                                                            class="btn @if ($val) bg-success text-success @else bg-secondary text-link @endif bg-opacity-10 fw-medium py-2 px-4">
                                                            @if ($val == true)
                                                                On
                                                            @else
                                                                Off
                                                            @endif
                                                        </button>
                                                    @elseif (in_array($key, array_keys($foreignKeys)))
                                                        @php

                                                            $foreignVal = $this->getForeignValue(
                                                                $val,
                                                                $foreignKeys[$key]['model'],
                                                                $foreignKeys[$key]['column'],
                                                            );
                                                        @endphp
                                                        {{ $foreignVal }}
                                                    @else
                                                        {{ $val }}
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach

                                        {{-- actions --}}
                                        <td class="action-item">
                                            <button type="button" class="btn p-0 mx-1 lh-1" x-data
                                                @click="$dispatch('open-modal',{name:'viewItem{{ $value->id }}'})">
                                                <i class="ti ti-eye fs-5"></i>

                                            </button>
                                            <!-- Modal for view -->
                                            <x-admin.modal name="viewItem{{ $value->id }}"
                                                title="View {{ $title }}">
                                                <x-slot:body>
                                                    @if ($detail_page)
                                                        <x-dynamic-component :component="$detail_page" :data="$value" />
                                                    @else
                                                        <x-admin.table-view :values="$value" />
                                                    @endif
                                                </x-slot:body>
                                            </x-admin.modal>

                                            {{-- Edit --}}
                                            @if ($edit_route || !is_null($form))
                                                <button type="button" class="btn p-0 mx-1 lh-1 text-info" x-data
                                                    @click="$dispatch('open-modal',{name:'editItem{{ $value->id }}'})">
                                                    <i class="ti ti-edit fs-5"></i>

                                                </button>
                                                <x-admin.modal name="editItem{{ $value->id }}"
                                                    title="Edit {{ $title }}">
                                                    <x-slot:body>
                                                        <form class="editForm" data-id="{{ $value->id }}">
                                                            <x-dynamic-component :component="$form" :data="$value"
                                                                :foreignData="$foreignData" />

                                                            <div class="row" class="m-0 p-0">
                                                                @isset($value->image)
                                                                    @forelse ($value->image as $item)
                                                                        <div class="col-4 col-md-3 mt-3">

                                                                            <div class="card  border-0 rounded-3 mb-4">
                                                                                <div class="card-body p-1">
                                                                                    <div class=" float-end">
                                                                                        @php
                                                                                            $modelClassName = addslashes(
                                                                                                get_class($value),
                                                                                            );
                                                                                        @endphp
                                                                                        <button type="button"
                                                                                            x-on:click="$dispatch('detachImage', { modelType: '{{ $modelClassName }}', modelId: {{ $value->id }}, image: {{ $item->id }} })"
                                                                                            class="btn bg-danger bg-opacity-10 fw-small text-danger p-2">
                                                                                            <i
                                                                                                class="material-symbols-outlined fs-12 text-danger">delete</i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <img class=""
                                                                                        style="object-fit:contain;height:200px;width:200px;"
                                                                                        src="{{ asset('storage/' . $item->image) }}"
                                                                                        alt="Preview">

                                                                                </div>
                                                                            </div>



                                                                        </div>

                                                                    @empty
                                                                    @endforelse
                                                                @endisset
                                                            </div>
                                                            <div class="d-flex justify-content-end mt-2 gap-2">
                                                                <button type="button" class="btn btn-secondary"
                                                                    x-on:click="$dispatch('close-modal')">Cancel</button>
                                                                <input type="submit" value="Submit"
                                                                    class="btn btn-success text-white">
                                                            </div>
                                                        </form>
                                                    </x-slot:body>
                                                </x-admin.modal>
                                            @endif


                                            {{-- Delete --}}
                                            @if ($delete)
                                                <button type="button" class="btn p-0 mx-1 lh-1 text-danger" x-data
                                                    @click="$dispatch('open-modal',{name:'removeItem{{ $value->id }}'})">
                                                    <i class="ti ti-trash fs-5"></i>

                                                </button>
                                                <x-admin.deleteModal name="removeItem{{ $value->id }}"
                                                    title="Delete {{ $title }}">
                                                    <x-slot:body>

                                                        <h5 class="px-2 mb-3">Are you sure you want to delete?</h5>

                                                        <div class="mt-3 d-flex justify-content-end">
                                                            <button wire:click="deleteItem({{ $value->id }})"
                                                                class="btn btn-danger text-white btn-small">Delete</button>

                                                            <button type="button" class="btn btn-small btn-seondary"
                                                                x-on:click="$dispatch('close-modal')">Close</button>
                                                        </div>
                                                    </x-slot:body>
                                                </x-admin.deleteModal>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 pt-lg-4">
                    <div class="d-flex justify-content-between text-center flex-wrap gap-2 showing-wrap">
                        {{ $values->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if ($add_route && !is_null($form))
        <x-admin.modal name="add{{ $title }}" title="Add {{ $title }}">
            <x-slot:body>
                <form id="addForm" class="d-inline">
                    <x-dynamic-component :component="$form" :data="$formData" :foreignData="$foreignData" />
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}">
                    @endif
                    <div class="row" class="m-0 p-0">
                        @forelse ($images as $item)
                            <div class="col-md-4">

                                <img class="p-3" style="height: 300px;object-fit:contain;width:auto;max-width:80%;"
                                    src="{{ $item->temporaryUrl() }}" alt="Preview">
                            </div>

                        @empty
                        @endforelse
                    </div>
                    <div class="d-flex justify-content-end mt-2 gap-2">
                        <button type="button" class="btn btn-secondary py-2 px-4"
                            x-on:click="$dispatch('close-modal')">Cancel</button>
                        <input type="submit" value="Submit" class="btn btn-success fw-medium text-white py-2 px-4">
                    </div>
                </form>

            </x-slot:body>
        </x-admin.modal>
    @endif


</div>
@script
    <script>
        const addForm = document.getElementById("addForm");
        if (addForm) {

            addForm.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent form submission to handle data

                const form = event.target;
                const formData = new FormData(form);


                formData.forEach((value, key) => {
                    if (value !== '') {
                        $wire.dispatchSelf('collectData', [key, value]);
                    }
                });
                $wire.dispatchSelf('storeForm');
            });
        }

        document.querySelectorAll(".editForm").forEach((form) => {
            if (form) {

                form.addEventListener("submit", function(event) {
                    event.preventDefault();
                    const formId = form.getAttribute("data-id");
                    const formData = new FormData(form);
                    console.log(formId);
                    formData.forEach((value, key) => {
                        $wire.dispatchSelf('collectData', [key, value, formId]);
                    });
                    $wire.dispatchSelf('updateForm');
                });
            }
        });

        const toastColors = {
            success: 'bg-success',
            danger: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info',
            primary: 'bg-primary',
            secondary: 'bg-secondary',
            light: 'bg-light',
            dark: 'bg-dark'
        };

        function showToast(message, type) {
            const template = document.getElementById('toast-template');

            const toastClone = template.cloneNode(true);

            // Set the toast message
            toastClone.querySelector('.toast-body').textContent = message;
            // Remove any existing background classes from the template
            toastClone.classList.remove(...Object.values(toastColors));


            const backgroundClass = toastColors[type] || toastColors['danger']; // Default to 'danger' if type is invalid
            toastClone.classList.add(backgroundClass);

            // Make the toast unique for Bootstrap
            toastClone.id = `toast-${Date.now()}`;

            const container = document.getElementById('toast-container');
            container.appendChild(toastClone);

            const bootstrapToast = new bootstrap.Toast(toastClone);
            bootstrapToast.show();

            setTimeout(() => {
                bootstrapToast.hide();
            }, 7000);
            // Remove the toast from the DOM after it hides
            toastClone.addEventListener('hidden.bs.toast', () => {
                toastClone.remove();
            });
        }


        $wire.on('show-toast', (event) => {
            showToast(event.message, event.type)
            console.log(event.message)
        });
    </script>
@endscript
