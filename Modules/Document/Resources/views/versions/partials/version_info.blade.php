<div class="d-flex flex-column">
    <div>
        <span class="badge bg-info">v{{ number_format($version->version, 1) }}</span>
        @if($version->is_active)
            <span class="badge bg-success">{{ __('Current') }}</span>
        @endif
    </div>
    @if($version->change_notes)
        <small class="text-muted mt-1">{{ Str::limit($version->change_notes, 50) }}</small>
    @endif
</div>
