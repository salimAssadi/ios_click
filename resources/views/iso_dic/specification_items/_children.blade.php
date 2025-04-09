<!-- Children Section -->
@if ($item->children->isNotEmpty())
    <tr class="child-row collapse" id="children-{{ $item->id }}">
        <td colspan="5">
            <table class="table child-table">
                <tbody>
                    @foreach ($item->children as $child)
                        <tr>
                            <td >
                                {{ $child->item_number }}
                            </td>
                            <td>{{ $child->inspection_question_ar }}</td>
                            {{-- <td>{{ $child->isoSystem?->name_ar ?? __('N/A') }}</td> --}}
                            <td>{{ $child->additional_text ?? '' }}</td>
                            <td>
                                <!-- Edit Button -->
                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.specification_items.destroy', $child->id]]) !!}
                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip"
                                    data-size="lg" data-bs-original-title="{{ __('Edit') }}" href="#"
                                    data-url="{{ route('iso_dic.specification_items.edit', $child->id) }}"
                                    data-title="{{ __('Edit item') }}"> <i data-feather="edit"></i></a>
                                <a class="avtar avtar-xs btn-link-warning text-warning" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Show Details') }}"
                                    href="{{ route('iso_dic.specification_items.show', \Illuminate\Support\Facades\Crypt::encrypt($child->id)) }}">
                                    <i data-feather="eye"></i></a>
                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Delete') }}" href="#"> <i data-feather="trash-2"></i>
                                </a>
                                 <!-- Nested Toggle Button -->
                                 @if ($child->children->isNotEmpty())
                                    <button type="button" class="avtar mb-3 avtar-xs btn btn-link-secondary toggle-children" data-bs-toggle="collapse"
                                        data-bs-target="#children-{{ $child->id }}">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                 @endif
                                {{ Form::close() }}

                               
                            </td>
                        </tr>

                        <!-- Recursively Include Child Rows -->
                        @include('iso_dic.specification_items._children', ['item' => $child, 'level' => ($level + 1)])
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif
