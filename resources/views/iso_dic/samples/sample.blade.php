@forelse ($samples as $sample)
<tr>
    <td>
        <div class="flex-grow-1 ms-3">
            <h5 class="mb-1">
                {{ $loop->iteration }}
            </h5>
        </div>
    </td> 
    <td>
        <div class="flex-grow-1 ms-3">
            <h5 class="mb-1">
                {{ $sample->sample_name }}

            </h5>
        </div>
    </td>



    <td>
        <div class="flex-grow-1 ms-3">
            <h5 class="mb-1">
                {{ $sample->procedure->procedure_name_ar}}

            </h5>
        </div>
    </td>

    <td>
        <div class="flex-grow-1 ms-3">
            @foreach ($sample->sampleAttachments as $attachment)
                <div class="d-flex align-items-center mb-1">
                    <a href=""
                        class="btn btn-sm btn-light me-2">
                        <i class="ti ti-download"></i>
                    </a>
                    <span>{{ $attachment->original_name }}</span>
                </div>
            @endforeach
        </div>
    </td>

    <td>
        <div class="flex-grow-1 ms-3">
            @if ($sample->status == 1)
                <span class="d-inline badge text-bg-success">{{ __('publish') }}</span>
            @else
                <span
                    class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
            @endif
        </div>
    </td>
    <td>
        <div class="cart-action align-items-center">
            {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.samples.destroy', $sample->id]]) !!}

            <a class="avtar avtar-xs btn-link-primary text-primary"
                data-bs-toggle="tooltip"
                target="blank"
                data-bs-original-title="{{ __('Attachments') }}"
                href="{{ route('iso_dic.samples.configure', \Illuminate\Support\Facades\Crypt::encrypt($sample->id)) }}"
                data-title="{{ __('Edit User') }}">
                <i class="ti ti-settings fs-2"></i>
            </a>
            <a class="avtar avtar-xs btn-link-secondary text-secondary"
                href="{{ route('iso_dic.samples.edit', $sample->id) }}">
                <i class="ti ti-edit fs-2"></i>
            </a>
            <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                href="#"><i class="ti ti-trash fs-2"></i>

            </a>
            <a class="avtar avtar-xs btn-link-warning text-warning" target="blank"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('preview') }}"
                href="{{ route('iso_dic.samples.sample.preview', \Illuminate\Support\Facades\Crypt::encrypt($sample->id)) }}"
                data-title="{{ __('preview') }}">
                <i class="ti ti-eye fs-2"></i>
            </a>
            {!! Form::close() !!}
            <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
            data-bs-toggle="tooltip" data-size="lg"
            data-bs-original-title="{{ __('Show Upload') }}" href="#"
            data-url="{{ route('iso_dic.samples.showuploadview', $sample->id) }}"
            data-title="{{ __('Show Upkoad') }}">
            <i class="ti ti-download fs-2"></i>
        </a>
        </div>

    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">لاتوجد بيانات</td>
</tr>
@endforelse