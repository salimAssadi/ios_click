<style>
    /* Parent row styling */
    .parent-row {
        font-weight: bold !important;
        color: red !important ;
    }

    /* Child row styling */
    .child-row {
        font-weight: normal;
        color: black !important;
    }

    /* Intermediate child row styling */
    .intermediate-child-row {
        font-weight: bold;
        color: blue !important; 
    }
</style>
<div class="dt-responsive table-responsive">
    <table class="table table-hover advance-datatable">
        <thead>
            <tr>
                <th>{{ __('Item Number') }}</th>
                <th>{{ __('Inspection Question') }}</th>
                <th>{{ __('ISO System') }}</th>
                <th>{{ __('Note') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($specificationItems as $item)
                @php
                    $isParent = !$item->hasChild();
                    $hasDots = strpos($item->item_number, '.') !== false;
                    $questionStyle = '';
                    if (!$hasDots && $isParent) {
                        $questionStyle = 'parent-row'; // Parent with no dots
                    } elseif ($hasDots && $isParent) {
                        $questionStyle = 'intermediate-child-row '; // Parent with dots
                    } 
                    elseif ($hasDots && !$isParent) {
                        $questionStyle = 'child-row'; // Child item
                    } else {
                        $questionStyle = 'child-row'; // Child item
                    }
                @endphp
                <tr>
                    <td>
                        <span   class="{{$questionStyle }}">{{ $item->item_number }}</span>
                    </td>

                    <!-- Inspection Question -->
                    <td class="{{$questionStyle }}">
                        {{ $item->inspection_question }}
                    </td>


                    <!-- ISO System Name -->
                    <td>
                        {{ $item->isoSystem?->name_ar ?? __('N/A') }}
                    </td>

                    <!-- ISO System Name -->
                    <td>
                        {{ $item->additional_text ?? '' }}
                    </td>

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
                            data-bs-original-title="{{ __('Detete') }}" href="#"> <i data-feather="trash-2"></i>
                        </a>
                        {{ Form::close() }}

                    </td>
                </tr>
                @php  $questionStyle=null; @endphp
            @endforeach
        </tbody>
    </table>
</div>
