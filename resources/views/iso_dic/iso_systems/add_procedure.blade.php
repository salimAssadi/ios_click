@extends('layouts.admin-app')
@section('page-title')
    {{ $pageTitle }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item" aria-current="page">
    {{ __('Procedures') }}
</li>
<li class="breadcrumb-item" aria-current="page">
    {{ $pageTitle }}
</li>
@endsection

@section('content')
    @php
        // $systemModules = \App\Models\User::$systemModules;
    @endphp

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5> {{ $pageTitle }}</h5>
                            
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' =>   'iso_dic.iso_systems.procedure.save','method' => 'post', 'enctype' => 'multipart/form-data'])}}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-check ">
                                {{ Form::checkbox('select_all', null, false, ['class' => 'form-check-input', 'id' => 'select_all']) }}
                                {{ Form::label('select_all', __('Select All'), ['class' => 'form-check-label']) }}
                            </div>
                            <hr>
                            <div class="col-xl-12 col-md-12">
                                <input type="hidden" value="{{$targetIsoSystemId}}" name="isoSystemId" id="isoSystemId">
                                @foreach($procedures as $procedure)
                                    <div class="form-check custom-check  form-check-inline col-md-4 py-2 ">
                                        {{ Form::checkbox('procedures[]', $procedure->id, $isoSystemProcedures->contains('procedure_id',$procedure->id), ['class' => 'form-check-input  procedure-checkbox', 'id' => 'procedure_' . $procedure->id]) }}
                                        {{ Form::label('procedure_' . $procedure->id, ucfirst($procedure->procedure_name), ['class' => 'form-check-label']) }}
                                    </div>
                                 @endforeach
                            </div>
                        </div>
                    </div>

                    
                   
                    <div class="form-group mt-20 ">
                        <div class="form-check custom-check text-start form-check-inline col-5  ">
                            {{ Form::checkbox('includeSample',null,false , ['class' => 'form-check-input']) }}
                            {{ Form::label('includeSample', __('Add Procedure Sample Before Save'), ['class' => 'form-check-label text-danger']) }}
                        </div>
                        <div>
                            {{ Form::submit(__('Add Procedures'), ['class' => 'btn btn-secondary  btn-rounded']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#select_all').change(function() {
            $('.procedure-checkbox').prop('checked', this.checked);
        });
    });
</script>