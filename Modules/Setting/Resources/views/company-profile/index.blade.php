@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Company Profile') }}
@endsection

@section('content')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Company Profile') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.setting.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Company Name') }}</label>
                                        <input type="text" name="company_name" class="form-control" value="{{ $profile->company_name ?? old('company_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control" value="{{ $profile->email ?? old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Phone') }}</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $profile->phone ?? old('phone') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Website') }}</label>
                                        <input type="url" name="website" class="form-control" value="{{ $profile->website ?? old('website') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Address') }}</label>
                                        <input type="text" name="address" class="form-control" value="{{ $profile->address ?? old('address') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('City') }}</label>
                                        <input type="text" name="city" class="form-control" value="{{ $profile->city ?? old('city') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Country') }}</label>
                                        <input type="text" name="country" class="form-control" value="{{ $profile->country ?? old('country') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Postal Code') }}</label>
                                        <input type="text" name="postal_code" class="form-control" value="{{ $profile->postal_code ?? old('postal_code') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Tax Number') }}</label>
                                        <input type="text" name="tax_number" class="form-control" value="{{ $profile->tax_number ?? old('tax_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Registration Number') }}</label>
                                        <input type="text" name="registration_number" class="form-control" value="{{ $profile->registration_number ?? old('registration_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Description') }}</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $profile->description ?? old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Company Logo') }}</label>
                                        @if(isset($profile) && $profile->logo)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $profile->logo) }}" alt="Company Logo" class="img-fluid" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
