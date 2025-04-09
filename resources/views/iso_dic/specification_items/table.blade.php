
<div class="dt-responsive table-responsive">
    <table class="table table-responsive parent-table">
        <thead>
            <tr>
                <th>{{ __('Item Number') }}</th>
                <th>{{ __('Inspection Question') }}</th>
                {{-- <th >{{ __('ISO System') }}</th> --}}
                <th>{{ __('Note') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($specificationItems as $item)
                <!-- Parent Row -->
                <tr>
                    <td>
                        <span>{{ $item->item_number }}</span>
                    </td>

                    <!-- Inspection Question -->
                    <td>{{ $item->inspection_question_ar }}</td>
                
                    <!-- Note -->
                    <td>{{ $item->additional_text ?? '' }}</td>

                    <!-- Actions -->
                    <td>
                        <!-- Edit Button -->
                        {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.specification_items.destroy', $item->id]]) !!}
                        <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip"
                            data-size="lg" data-bs-original-title="{{ __('Edit') }}" href="#"
                            data-url="{{ route('iso_dic.specification_items.edit', $item->id) }}"
                            data-title="{{ __('Edit item') }}"> <i data-feather="edit"></i></a>
                        <a class="avtar avtar-xs btn-link-warning text-warning" data-bs-toggle="tooltip"
                            data-bs-original-title="{{ __('Show Details') }}"
                            href="{{ route('iso_dic.specification_items.show', \Illuminate\Support\Facades\Crypt::encrypt($item->id)) }}">
                            <i data-feather="eye"></i></a>
                        <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                            data-bs-original-title="{{ __('Delete') }}" href="#"> <i data-feather="trash-2"></i>
                        </a>
                        <!-- Toggle Button for Children -->
                        @if ($item->children->isNotEmpty())
                            <button type="button" class="avtar mb-3 avtar-xs btn btn-link-secondary toggle-children"
                                data-bs-toggle="collapse" data-bs-target="#children-{{ $item->id }}">
                                <i class="ti ti-plus"></i>
                            </button>
                        @endif
                        {{ Form::close() }}


                    </td>
                </tr>

                <!-- Include Child Rows -->
                @include('iso_dic.specification_items._children', ['item' => $item, 'level' => 0])
            @endforeach
        </tbody>
    </table>
</div>
