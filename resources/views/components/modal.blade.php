@props(['name', 'title'])

<div x-data="{ show: false, name: '{{ $name }}' }" x-show="show" x-on:open-modal.window="show = ($event.detail.name === name)"
    x-on:close-modal.window="show = false" x-on:keydown.escape.window="show = false" style="display:none; z-index: 1050;"
    class="position-fixed top-0 start-0 w-100 h-100" x-transition.duration>

    {{-- Gray Background --}}
    <div x-on:click="show = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

    {{-- Modal Body --}}
    <div class="bg-white rounded shadow-lg m-auto position-fixed top-50 start-50 translate-middle  overflow-auto"
        style="max-height:80vh;width:60vw;">
        <div class="px-4 py-3 d-flex align-items-center justify-content-between border-bottom">
            @if (isset($title))
                <div class="card-title text-dark fw-semibold mb-4">{{ $title }}</div>
            @endif
            <button class="btn text-danger fw-medium fs-4 py-1  rounded-3" x-on:click="$dispatch('close-modal')">
                <i class="ti ti-x "></i>
            </button>
        </div>
        <div class="p-4">
            {{ $body }}
        </div>
    </div>
</div>
