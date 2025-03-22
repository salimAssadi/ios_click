@forelse ($forms as $form)
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
                    {{ $form->form->sample_name }}

                </h5>
                <p>{{ $form->form->description }} </p>

            </div>
        </td>



        <td>
            <div class="flex-grow-1 ms-3">
                <h5 class="mb-1">
                    {{ $form->procedure->procedure_name }}
                </h5>
            </div>
        </td>

        <td>
            <div class="flex-grow-1 ms-3">
                <h5 class="mb-1">
                    {{ getCompanySymbol() }}{{ $form->form_coding }}

                </h5>
            </div>
        </td>


        <td>
            <div class="flex-grow-1 ms-3">
                @if ($form->is_optional == 1)
                    <span class="d-inline badge  text-bg-danger px-4">{{ __('NO') }}</span>
                @else
                    <span class="d-inline badge text-bg-success px-3">{{ __('Yes') }}</span>
                @endif
            </div>
        </td>

        <td>
            <div class="flex-grow-1 ms-3">
                @if ($form->status == 1)
                    <span class="d-inline badge text-bg-success">{{ __('publish') }}</span>
                @else
                    <span class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
                @endif
            </div>
        </td>
        <td>
            <div class="cart-action align-items-center">
                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.procedures.destroy', $form->id]]) !!}


                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('Detete') }}" href="#"><i class="ti ti-trash fs-2"></i>

                </a>
                <a class="avtar avtar-xs btn-link-secondary text-secondary" data-bs-toggle="tooltip" data-size="lg"
                    data-bs-original-title="{{ __('Download') }}"
                    href="{{ route('iso_dic.iso_systems.procedure.download', \Illuminate\Support\Facades\Crypt::encrypt($form->id)) }}"
                    data-title="{{ __('Download') }}">
                    <i data-feather="download"> </i>
                </a>
                <a class="avtar avtar-xs btn-link-warning text-warning" data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('preview') }}" target="blank"
                    href="{{ route('iso_dic.iso_systems.procedure.preview', \Illuminate\Support\Facades\Crypt::encrypt($form->id)) }}"
                    data-title="{{ __('preview') }}">
                    <i class="ti ti-eye fs-2"></i>
                </a>
                {!! Form::close() !!}
            </div>

        </td>
    </tr>
@empty
@endforelse
