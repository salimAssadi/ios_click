<div class="btn-group">
    <a href="{{ route('document.versions.show', $version->id) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
        <i class="fas fa-eye"></i>
    </a>
    @if($version->status === 'draft')
        <button type="button" class="btn btn-sm btn-primary submit-for-review" data-id="{{ $version->id }}" title="{{ __('Submit for Review') }}">
            <i class="fas fa-paper-plane"></i>
        </button>
    @endif
    @if($version->status === 'under_review' && auth()->user()->can('approve_documents'))
        <button type="button" class="btn btn-sm btn-success approve-version" data-id="{{ $version->id }}" title="{{ __('Approve') }}">
            <i class="fas fa-check"></i>
        </button>
    @endif
    <a href="{{ $version->download_url }}" class="btn btn-sm btn-secondary" title="{{ __('Download') }}" target="_blank">
        <i class="fas fa-download"></i>
    </a>
</div>
