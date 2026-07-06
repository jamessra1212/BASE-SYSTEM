@if($is_activated == 1)
    <span class="badge bg-success px-3 py-2" style="min-width: 100px; border-radius: 30px;">
        <i class="fas fa-check-circle me-1"></i> ACTIVE
    </span>
@else
    <span class="badge bg-danger px-3 py-2" style="min-width: 100px; border-radius: 30px;">
        <i class="fas fa-times-circle me-1"></i> DEACTIVATED
    </span>
@endif
