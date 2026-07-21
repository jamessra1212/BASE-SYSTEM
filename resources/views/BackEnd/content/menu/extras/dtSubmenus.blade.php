<div class="row row-cols-3">
    @forelse($data->submenus as $submenu)
        <div class="col">
            » {{ $submenu->name}}
        </div>
        @empty
    @endforelse
</div>
