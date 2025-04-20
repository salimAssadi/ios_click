@extends('layouts.admin-app')
@section('page-title')
    {{ $pageTitle }}
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#"> {{ $pageTitle }} </a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">

                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ $pageTitle }}
                            </h5>
                        </div>
                        <div class="col-auto">
                           
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="email-body">
                            <div class="card">
                                <div class="card-body">
                                    {{ Form::open(['route' => ['iso_dic.samples.saveConfigure', \Illuminate\Support\Facades\Crypt::encrypt($id)], 'method' => 'POST']) }}
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <div class="form-group col-12">
                                        <textarea class="summernote" name="content" >{{$sample->content ?? null}}</textarea>
                                        {{-- {{ Form::textarea('content', $sample->content ?? null, ['class' => 'summernote', 'required' => 'required' ]) }} --}}
                                    </div>
                                    <div class="form-group mt-20 text-end">
                                        {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

