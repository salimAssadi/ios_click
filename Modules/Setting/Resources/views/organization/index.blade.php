@extends('tenant::layouts.app')

@section('title')
    {{ __('Organization Structure') }}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/plugins/jstree.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/plugins/orgchart.min.css') }}">
<style>
    .org-container {
        margin: 20px;
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .node-actions {
        margin-left: 10px;
    }
    .orgchart {
        background: #fff;
    }
    .orgchart .node {
        width: 180px;
    }
    .orgchart .node.department {
        background-color: #FF9B44;
    }
    .orgchart .node.position {
        background-color: #4CAF50;
    }
    .orgchart .node.employee {
        background-color: #2196F3;
    }
</style>
@endsection

@section('content')
    <div class="pc-content">
        <div class="row">
            <!-- Organization Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Organization Chart') }}</h4>
                    </div>
                    <div class="card-body">
                        <div id="organization-chart" class="org-container"></div>
                    </div>
                </div>
            </div>

            <!-- Management Panel -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>{{ __('Management') }}</h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" onclick="showDepartmentModal()">
                                <i class="ti ti-building"></i> {{ __('Add Department') }}
                            </button>
                            <button type="button" class="btn btn-success" onclick="showPositionModal()">
                                <i class="ti ti-briefcase"></i> {{ __('Add Position') }}
                            </button>
                            <button type="button" class="btn btn-info" onclick="showEmployeeModal()">
                                <i class="ti ti-user"></i> {{ __('Add Employee') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Department') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="departmentForm">
                    @csrf
                    <input type="hidden" name="_method" id="departmentMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Parent Department') }}</label>
                            <select name="parent_id" class="form-control">
                                <option value="">{{ __('None') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
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

    <!-- Position Modal -->
    <div class="modal fade" id="positionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Position') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="positionForm">
                    @csrf
                    <input type="hidden" name="_method" id="positionMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('Department') }}</label>
                            <select name="department_id" class="form-control" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Reports To') }}</label>
                            <select name="reports_to_id" class="form-control">
                                <option value="">{{ __('None') }}</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->title }} ({{ $pos->department->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
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

    <!-- Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Employee') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="employeeForm">
                    @csrf
                    <input type="hidden" name="_method" id="employeeMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('Position') }}</label>
                            <select name="position_id" class="form-control" required>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->title }} ({{ $pos->department->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
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
<script src="{{ asset('assets/js/plugins/jstree.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/orgchart.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Load organization chart
    loadOrganizationChart();

    // Department form submission
    $('#departmentForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let method = $('#departmentMethod').val();
        let url = method === 'POST' 
            ? '{{ route("tenant.setting.organization.departments.store") }}'
            : form.attr('action');

        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#departmentModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    });

    // Position form submission
    $('#positionForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let method = $('#positionMethod').val();
        let url = method === 'POST'
            ? '{{ route("tenant.setting.organization.positions.store") }}'
            : form.attr('action');

        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#positionModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    });

    // Employee form submission
    $('#employeeForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let method = $('#employeeMethod').val();
        let url = method === 'POST'
            ? '{{ route("tenant.setting.organization.employees.store") }}'
            : form.attr('action');

        $.ajax({
            url: url,
            method: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#employeeModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    });
});

function loadOrganizationChart() {
    $.get('{{ route("tenant.setting.organization.chart") }}', function(data) {
        $('#organization-chart').empty();
        new OrgChart({
            'chartContainer': '#organization-chart',
            'data': data,
            'nodeContent': function(data) {
                return `
                    <div class="title">${data.text}</div>
                    ${data.type === 'employee' ? `<div class="content">${data.position}</div>` : ''}
                `;
            },
            'nodeTemplate': function(data) {
                return `node ${data.type}`;
            }
        });
    });
}

function showDepartmentModal(id = null) {
    resetForm('department');
    if (id) {
        $('#departmentMethod').val('PUT');
        $('#departmentForm').attr('action', `{{ route("tenant.setting.organization.departments.update", "") }}/${id}`);
        // Load department data
        $.get(`{{ route("tenant.setting.organization.departments.show", "") }}/${id}`, function(data) {
            $('#departmentForm [name="name"]').val(data.name);
            $('#departmentForm [name="parent_id"]').val(data.parent_id);
            $('#departmentForm [name="description"]').val(data.description);
        });
    }
    $('#departmentModal').modal('show');
}

function showPositionModal(id = null) {
    resetForm('position');
    if (id) {
        $('#positionMethod').val('PUT');
        $('#positionForm').attr('action', `{{ route("tenant.setting.organization.positions.update", "") }}/${id}`);
        // Load position data
        $.get(`{{ route("tenant.setting.organization.positions.show", "") }}/${id}`, function(data) {
            $('#positionForm [name="department_id"]').val(data.department_id);
            $('#positionForm [name="title"]').val(data.title);
            $('#positionForm [name="reports_to_id"]').val(data.reports_to_id);
            $('#positionForm [name="description"]').val(data.description);
        });
    }
    $('#positionModal').modal('show');
}

function showEmployeeModal(id = null) {
    resetForm('employee');
    if (id) {
        $('#employeeMethod').val('PUT');
        $('#employeeForm').attr('action', `{{ route("tenant.setting.organization.employees.update", "") }}/${id}`);
        // Load employee data
        $.get(`{{ route("tenant.setting.organization.employees.show", "") }}/${id}`, function(data) {
            $('#employeeForm [name="position_id"]').val(data.position_id);
            $('#employeeForm [name="name"]').val(data.name);
            $('#employeeForm [name="email"]').val(data.email);
            $('#employeeForm [name="phone"]').val(data.phone);
            $('#employeeForm [name="status"]').val(data.status);
        });
    }
    $('#employeeModal').modal('show');
}

function resetForm(type) {
    $(`#${type}Form`)[0].reset();
    $(`#${type}Method`).val('POST');
    $(`#${type}Form`).attr('action', '');
}

function deleteNode(type, id) {
    if (confirm('{{ __("Are you sure you want to delete this item?") }}')) {
        $.ajax({
            url: `{{ route("tenant.setting.organization.destroy", ["", ""]) }}/${type}s/${id}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    }
}
</script>
@endsection
