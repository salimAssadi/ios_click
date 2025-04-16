@forelse ($samples as $sample)
<tr>
    <td class="text-center">{{ $loop->iteration }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div>
                <h6 class="mb-0">{{ $sample->sample_name }}</h6>
            </div>
        </div>
    </td>
    <td>
        <span class="text-body-secondary">{{ $sample->procedure->procedure_name_ar }}</span>
    </td>
    <td>
        @if($sample->sampleAttachments->count() > 0)
            <div class="d-flex flex-column gap-1">
                @foreach ($sample->sampleAttachments as $attachment)
                    <div class="d-inline-flex align-items-center">
                        <a href="#" class="btn btn-sm btn-icon btn-light-secondary me-1">
                            <i class="ti ti-download"></i>
                        </a>
                        <small class="text-truncate" style="max-width: 150px;" title="{{ $attachment->original_name }}">
                            {{ $attachment->original_name }}
                        </small>
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-body-secondary">{{ __('No attachments') }}</span>
        @endif
    </td>
    <td>
        @if ($sample->status == 1)
            <span class="badge bg-success-subtle text-success">{{ __('publish') }}</span>
        @else
            <span class="badge bg-danger-subtle text-danger">{{ __('unpublish') }}</span>
        @endif
    </td>
    <td>
        <div class="d-flex justify-content-end gap-2">
            {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.samples.destroy', $sample->id], 'class' => 'd-inline']) !!}
            
            <a href="{{ route('iso_dic.samples.configure', \Illuminate\Support\Facades\Crypt::encrypt($sample->id)) }}"
                class="btn btn-sm btn-icon btn-light-primary"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('Configure') }}">
                <i class="ti ti-settings"></i>
            </a>

            <a href="{{ route('iso_dic.samples.edit', $sample->id) }}"
                class="btn btn-sm btn-icon btn-light-info"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('Edit') }}">
                <i class="ti ti-edit"></i>
            </a>

            <button type="button"
                class="btn btn-sm btn-icon btn-light-danger confirm_dialog"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('Delete') }}">
                <i class="ti ti-trash"></i>
            </button>

            <a href="{{ route('iso_dic.samples.sample.preview', \Illuminate\Support\Facades\Crypt::encrypt($sample->id)) }}"
                class="btn btn-sm btn-icon btn-light-warning"
                target="_blank"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ __('Preview') }}">
                <i class="ti ti-eye"></i>
            </a>
            {!! Form::close() !!}

            <a href="#"
                class="btn btn-sm btn-icon btn-light-secondary customModal"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-size="lg"
                data-url="{{ route('iso_dic.samples.showuploadview', $sample->id) }}"
                title="{{ __('Upload') }}">
                <i class="ti ti-upload"></i>
            </a>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4">
        <div class="d-flex flex-column align-items-center">
            <i class="ti ti-file-off text-secondary mb-2" style="font-size: 24px;"></i>
            <p class="text-body-secondary mb-0">{{ __('No samples found') }}</p>
        </div>
    </td>
</tr>
@endforelse