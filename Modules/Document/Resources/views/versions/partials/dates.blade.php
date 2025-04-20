<div class="d-flex flex-column">
    <small><strong>{{ __('Issue') }}:</strong> {{ $version->issue_date ? $version->issue_date->format('Y-m-d') : '-' }}</small>
    <small><strong>{{ __('Review') }}:</strong> {{ $version->review_due_date ? $version->review_due_date->format('Y-m-d') : '-' }}</small>
    <small><strong>{{ __('Expiry') }}:</strong> {{ $version->expiry_date ? $version->expiry_date->format('Y-m-d') : '-' }}</small>
</div>
