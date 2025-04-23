@extends('tenant::layouts.app')

@section('title')
    {{ __('Organization Structure') }}
@endsection

@push('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css">
<style>
    .org-container {
        height: 600px;
        width: 100%;
        position: relative;
        overflow: auto;
    }
    .node {
        padding: 10px;
        border-radius: 3px;
        width: 200px;
    }
    .node.department {
        background-color: #FF9B44;
        border: 2px solid #e88a3e;
    }
    .node.position {
        background-color: #4CAF50;
        border: 2px solid #3d8b40;
    }
    .node.employee {
        background-color: #2196F3;
        border: 2px solid #1976d2;
    }
    .node p {
        margin: 0;
        color: white;
        font-weight: bold;
    }
    .node .subtitle {
        font-size: 0.8em;
        opacity: 0.8;
    }
    .org-container {
        margin: 20px;
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
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
                <div class="card-header d-flex  justify-content-between align-items-center">
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
                            <label>{{ __('Name Arabic') }}</label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Name English') }}</label>
                            <input type="text" name="name_en" class="form-control" required>
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
                            <label>{{ __('Title Arabic') }}</label>
                            <input type="text" name="title_ar" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Title English') }}</label>
                            <input type="text" name="title_en" class="form-control" required>
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

@push('script-page')
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script>
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
                    notify.show('success', response.message);
                    $('#departmentModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notify.show('error', xhr.responseJSON.message);
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
                    notify.show('success', response.message);
                    $('#positionModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notify.show('error', xhr.responseJSON.message);
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
    $.get('{{ route("tenant.setting.organization.chart") }}', function(response) {
        $('#organization-chart').empty();
        
        var config = {
            container: "#organization-chart",
            levelSeparation: 50,
            siblingSeparation: 50,
            subTeeSeparation: 50,
            nodeAlign: "BOTTOM",
            scrollbar: "native",
            padding: 35,
            node: {
                HTMLclass: "node",
                drawLineThrough: false
            },
            connectors: {
                type: "step",
                style: {
                    "stroke-width": 2,
                    "stroke": "#ccc"
                }
            }
        };

        var chart_config = {
            chart: config,
            nodeStructure: transformData(response[0])
        };

        new Treant(chart_config);
    });
}

function transformData(data) {
    var node = {
        text: { 
            name: data.text,
            title: data.type.toUpperCase()
        },
        HTMLclass: data.type,
        innerHTML: `
            <p>${data.text}</p>
            <p class="subtitle">${data.type.toUpperCase()}</p>
        `
    };

    if (data.children && data.children.length > 0) {
        node.children = data.children.map(transformData);
    }

    return node;
}

function showDepartmentModal(id = null) {
    resetForm('department');
    if (id) {
        $('#departmentMethod').val('PUT');
        $('#departmentForm').attr('action', `{{ route("tenant.setting.organization.departments.update", "") }}/${id}`);
        // Load department data
        $.get(`{{ route("tenant.setting.organization.departments.show", "") }}/${id}`, function(data) {
            $('#departmentForm [name="name_ar"]').val(data.name_ar);
            $('#departmentForm [name="name_en"]').val(data.name_en);
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
            $('#positionForm [name="title_ar"]').val(data.title_ar);
            $('#positionForm [name="title_en"]').val(data.title_en);
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
@endpush
