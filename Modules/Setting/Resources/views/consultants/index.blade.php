@extends('tenant::layouts.app')

@section('title')
    {{ __('Consultants') }}
@endsection

@section('content')
    <div class="pc-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4>{{ __('Consultants') }}</h4>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" onclick="showAddModal()">
                                    <i class="ti ti-plus"></i> {{ __('Add Consultant') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="consultants-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Specialization') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consultants as $consultant)
                                        <tr>
                                            <td>{{ $consultant->name }}</td>
                                            <td>{{ $consultant->email }}</td>
                                            <td>{{ $consultant->phone }}</td>
                                            <td>{{ $consultant->specialization }}</td>
                                            <td>
                                                <span class="badge bg-{{ $consultant->status === 'active' ? 'success' : 'danger' }}">
                                                    {{ __($consultant->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="showEditModal({{ $consultant->id }})">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteConsultant({{ $consultant->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="consultantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">{{ __('Add Consultant') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="consultantForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Specialization') }}</label>
                            <input type="text" name="specialization" id="specialization" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Expertise') }}</label>
                            <textarea name="expertise" id="expertise" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Bio') }}</label>
                            <textarea name="bio" id="bio" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#consultants-table').DataTable();

    // Form submission
    $('#consultantForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let method = $('#formMethod').val();

        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#consultantModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    });
});

function showAddModal() {
    resetForm();
    $('#modalTitle').text('{{ __("Add Consultant") }}');
    $('#consultantForm').attr('action', '{{ route("settings.consultants.store") }}');
    $('#formMethod').val('POST');
    $('#consultantModal').modal('show');
}

function showEditModal(id) {
    resetForm();
    $('#modalTitle').text('{{ __("Edit Consultant") }}');
    $('#consultantForm').attr('action', '{{ route("settings.consultants.update", "") }}/' + id);
    $('#formMethod').val('PUT');

    // Fetch consultant data
    $.get('{{ route("settings.consultants.show", "") }}/' + id, function(response) {
        $('#name').val(response.name);
        $('#email').val(response.email);
        $('#phone').val(response.phone);
        $('#specialization').val(response.specialization);
        $('#expertise').val(response.expertise);
        $('#bio').val(response.bio);
        $('#status').val(response.status);
        $('#consultantModal').modal('show');
    });
}

function deleteConsultant(id) {
    if (confirm('{{ __("Are you sure you want to delete this consultant?") }}')) {
        $.ajax({
            url: '{{ route("settings.consultants.destroy", "") }}/' + id,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    }
}

function resetForm() {
    $('#consultantForm')[0].reset();
}
</script>
@endsection
