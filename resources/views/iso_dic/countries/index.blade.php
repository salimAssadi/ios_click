@extends('layouts.admin-app')
@section('page-title')
    {{ __('Countries') }} & {{ __('Cities') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Countries') }} & {{ __('Cities') }}</a>
        </li>
    </ul>
@endsection
@section('card-action-btn')
    {{-- @if (Gate::check('create tag')) --}}
    <a class="btn btn-secondary btn-sm ml-20 customModal" href="#" data-size="md"
        data-url="{{ route('iso_dic.countries.create') }}" data-title="{{ __('Create Tag') }}"> <i
            class="ti-plus mr-5"></i>{{ __('Create Tag') }}</a>
    {{-- @endif --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ __('Countries') }} & {{ __('Cities') }}
                            </h5>
                        </div>
                        {{-- @if (Gate::check('create tag'))
                            <div class="col-auto">
                                <a href="#" class="btn btn-secondary customModal" data-size="md"
                                    data-url="{{ route('tag.create') }}" data-title="{{ __('Create Tag') }} ">
                                    <i class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Tag') }}
                                </a>
                            </div>
                        @endif --}}
                    </div>
                </div>
                <div class="card-body pt-0 row">
                    <div class="dt-responsive table-responsive col-6">
                        <table class="table table-hover basic-datatable">
                            <thead>
                                <tr>
                                    <th style="width:20%;">#</th>
                                    <th style="width:40%;">اسم الدولة</th>
                                    <th style="width:40%;">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($countries as $country)
                                    <tr>
                                        <td>{{ $country->id }}</td>
                                        <td>
                                            {{ $country->name_ar .'/'}}  {{ $country->code }}
                                           
                                        </td>
                                        <td>
                                            <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip"
                                            data-bs-original-title="{{ __('Edit') }}" href="#"
                                            data-url="{{ route('iso_dic.countries.edit', $country->id) }}"
                                            data-title="{{ __('Edit Country') }}"> <i
                                                data-feather="edit"></i>
                                            </a>
                                            <button class="avtar avtar-xs btn-link-info mb-3 view-cities"
                                                data-id="{{ $country->id }}"><i  data-feather="eye"></i></button>

                                            <form action="" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a class=" avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Detete') }}" href="#"> <i
                                                                data-feather="trash-2"></i></a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="dt-responsive table-responsive col-6" id="capitals-table">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.view-cities').on('click', function() {
            let countryId = $(this).data('id');
            loadCapitals(countryId);
        });

        function loadCapitals(countryId) {
            $.ajax({
                url: "{{ route('iso_dic.country.getstates') }}",
                type: 'POST',
                data: {
                    country_id: countryId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#capitals-table').empty();
                    $('.basic-datatable').DataTable().destroy();
                    $('#capitals-table').html(response);
                    datatable();
                },
                error: function(xhr) {
                    console.error('Error occurred:', xhr);
                }
            });
        }

        const firstCountryId = $('.view-cities').first().data('id');
        if (firstCountryId) {
            loadCapitals(firstCountryId);
        }
    });
</script>
