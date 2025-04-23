@extends('tenant::layouts.app')

@section('title')
    {{ __('Organization Structure') }}
@endsection

@push('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css">
<style>
    .org-container {
        height: calc(100vh - 250px);
        min-height: 500px;
        overflow: auto;
        padding: 20px;
    }
     /* Connecting Lines */
     .jstree-default .jstree-children {
        position: relative;
        margin: 0;
        padding: 0 0 0 20px;
    }

    .jstree-default .jstree-children::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 12px;
        width: 2px;
        background: #e2e8f0;
    }

    .jstree-default .jstree-node::before {
        content: '';
        position: absolute;
        top: 15px;
        width: 20px;
        height: 2px;
        background: #e2e8f0;
    }

    .jstree-default .jstree-anchor {
        /* padding: 8px 12px; */
        margin: 0;
        height: auto;
        line-height: 1.4;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .jstree-anchor[data-jstree='{"type":"department"}'] {
    font-weight: bold;
    color: #0d6efd;
}
.jstree-anchor[data-jstree='{"type":"position"}'] {
    color: #198754;
}
.jstree-anchor[data-jstree='{"type":"employee"}'] {
    color: #6c757d;
    font-style: italic;
}

</style>
@endpush

@section('content')
<div class="row">
    <!-- Organization Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>{{ __('Organization Chart') }}</h4>
                <div class="search-box">
                    <input type="text" id="org-search" class="form-control" placeholder="{{ __('Search organization...') }}">
                </div>
            </div>
            <div class="card-body p-4">
                <div id="organization-chart" class="org-container"></div>
            </div>
        </div>
    </div>

    <!-- Management Panel -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('Management') }}</h4>
            </div>
            <div class="card-body">
                <div class="btn-group-vertical w-100">
                    <button type="button" class="btn btn-primary mb-2" onclick="showDepartmentModal()">
                        <i class="ti ti-building"></i> {{ __('Add Department') }}
                    </button>
                    <button type="button" class="btn btn-success mb-2" onclick="showPositionModal()">
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
                    <div class="form-group mb-3">
                        <label>{{ __('Name Arabic') }}</label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Name English') }}</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Parent Department') }}</label>
                        <select name="parent_id" class="form-control">
                            <option value="">{{ __('None') }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                            @endforeach
                            <option value="0">{{ __('Original Department') }}</option>
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
                    <div class="form-group mb-3">
                        <label>{{ __('Department') }}</label>
                        <select name="department_id" class="form-control" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Title Arabic') }}</label>
                        <input type="text" name="title_ar" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Title English') }}</label>
                        <input type="text" name="title_en" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Reports To') }}</label>
                        <select name="reports_to_id" class="form-control">
                            <option value="">{{ __('None') }}</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->title_ar }} ({{ $pos->department->name_ar }})</option>
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
                    <div class="form-group mb-3">
                        <label>{{ __('Position') }}</label>
                        <select name="position_id" class="form-control" required>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->title_ar }} ({{ $pos->department->name_ar }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
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
                    notifier.show('Success!', response.message, 'success',
                    successImg, 4000);
                    $('#departmentModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notifier.show('Error!', xhr.responseJSON.message, 'error',
                    errorImg, 4000);
                $('#departmentModal').modal('hide');
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
                    notifier.show('Success!', response.message, 'success',
                    successImg, 4000);
                    $('#positionModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notifier.show('Error!', xhr.responseJSON.message, 'error',
                    errorImg, 4000);
                $('#positionModal').modal('hide');
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
                    notifier.show('Success!', response.message, 'success',
                    successImg, 4000);
                    $('#employeeModal').modal('hide');
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notifier.show('Error!', xhr.responseJSON.message, 'error',
                    errorImg, 4000);
                $('#employeeModal').modal('hide');
            }
        });
    });

    // Initialize search functionality
    let searchTimeout = null;
    $('#org-search').on('keyup', function() {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        const searchString = $(this).val();
        searchTimeout = setTimeout(function() {
            $('#organization-chart').jstree('search', searchString);
        }, 250);
    });
});

function showDepartmentModal(id = null) {
    resetForm('department');
    
    // إعداد القائمة الافتراضية للأقسام الأصلية
    $('#departmentForm [name="parent_id"]').empty();
    $('#departmentForm [name="parent_id"]').append('<option value="">{{ __("None") }}</option>');
    @foreach($departments as $dept)
        $('#departmentForm [name="parent_id"]').append('<option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>');
    @endforeach
    
    if (id) {
        $('#departmentMethod').val('PUT');
        $('#departmentForm').attr('action', `{{ route("tenant.setting.organization.departments.update", "") }}/${id}`);
        // Load department data
        $.get(`{{ route("tenant.setting.organization.departments.show", "") }}/${id}`, function(data) {
            $('#departmentForm [name="name_ar"]').val(data.name_ar);
            $('#departmentForm [name="name_en"]').val(data.name_en);
            $('#departmentForm [name="description"]').val(data.description);
            
            // تحديث قائمة الأقسام الأصلية من البيانات المرجعة
            if (data.departments) {
                $('#departmentForm [name="parent_id"]').empty();
                $('#departmentForm [name="parent_id"]').append('<option value="">{{ __("None") }}</option>');
                $.each(data.departments, function(index, dept) {
                    $('#departmentForm [name="parent_id"]').append(`<option value="${dept.id}">${dept.name_ar}</option>`);
                });
            }
            
            $('#departmentForm [name="parent_id"]').val(data.parent_id);
        });
    }
    $('#departmentModal').modal('show');
}

function showPositionModal(id = null) {
    resetForm('position');
    
    // إعداد القوائم الافتراضية
    $('#positionForm [name="department_id"]').empty();
    @foreach($departments as $dept)
        $('#positionForm [name="department_id"]').append('<option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>');
    @endforeach
    
    $('#positionForm [name="reports_to_id"]').empty();
    $('#positionForm [name="reports_to_id"]').append('<option value="">{{ __("None") }}</option>');
    @foreach($positions as $pos)
        $('#positionForm [name="reports_to_id"]').append('<option value="{{ $pos->id }}">{{ $pos->title_ar }} ({{ $pos->department->name_ar }})</option>');
    @endforeach
    
    if (id) {
        $('#positionMethod').val('PUT');
        $('#positionForm').attr('action', `{{ route("tenant.setting.organization.positions.update", "") }}/${id}`);
        // Load position data
        $.get(`{{ route("tenant.setting.organization.positions.show", "") }}/${id}`, function(data) {
            $('#positionForm [name="title_ar"]').val(data.title_ar);
            $('#positionForm [name="title_en"]').val(data.title_en);
            $('#positionForm [name="description"]').val(data.description);
            
            // تحديث قائمة الأقسام من البيانات المرجعة
            if (data.departments) {
                $('#positionForm [name="department_id"]').empty();
                $.each(data.departments, function(index, dept) {
                    $('#positionForm [name="department_id"]').append(`<option value="${dept.id}">${dept.name_ar}</option>`);
                });
            }
            
            // تحديث قائمة المناصب التي يمكن الإبلاغ لها من البيانات المرجعة
            if (data.positions) {
                $('#positionForm [name="reports_to_id"]').empty();
                $('#positionForm [name="reports_to_id"]').append('<option value="">{{ __("None") }}</option>');
                $.each(data.positions, function(index, pos) {
                    if (pos.id != id) { // لا يمكن للمنصب أن يبلغ لنفسه
                        $('#positionForm [name="reports_to_id"]').append(`<option value="${pos.id}">${pos.title_ar} (${pos.department.name_ar})</option>`);
                    }
                });
            }
            
            $('#positionForm [name="department_id"]').val(data.department_id);
            $('#positionForm [name="reports_to_id"]').val(data.reports_to_id);
        });
    }
    $('#positionModal').modal('show');
}

function showEmployeeModal(id = null) {
    resetForm('employee');
    
    // إعداد القائمة الافتراضية للمناصب
    $('#employeeForm [name="position_id"]').empty();
    @foreach($positions as $pos)
        $('#employeeForm [name="position_id"]').append('<option value="{{ $pos->id }}">{{ $pos->title_ar }} ({{ $pos->department->name_ar }})</option>');
    @endforeach
    
    if (id) {
        $('#employeeMethod').val('PUT');
        $('#employeeForm').attr('action', `{{ route("tenant.setting.organization.employees.update", "") }}/${id}`);
        // Load employee data
        $.get(`{{ route("tenant.setting.organization.employees.show", "") }}/${id}`, function(data) {
            $('#employeeForm [name="name"]').val(data.name);
            $('#employeeForm [name="email"]').val(data.email);
            $('#employeeForm [name="phone"]').val(data.phone);
            $('#employeeForm [name="status"]').val(data.status);
            
            // تحديث قائمة المناصب من البيانات المرجعة
            if (data.positions) {
                $('#employeeForm [name="position_id"]').empty();
                $.each(data.positions, function(index, pos) {
                    $('#employeeForm [name="position_id"]').append(`<option value="${pos.id}">${pos.title_ar} (${pos.department.name_ar})</option>`);
                });
            }
            
            $('#employeeForm [name="position_id"]').val(data.position_id);
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
                    notifier.show('Success!', response.message, 'success',
                    successImg, 4000);
                    loadOrganizationChart();
                }
            },
            error: function(xhr) {
                notifier.show('Error!', xhr.responseJSON.message, 'error',
                    errorImg, 4000);
            }
        });
    }
}

function loadOrganizationChart() {
    $('#organization-chart').addClass('loading');
    
    $.get('{{ route("tenant.setting.organization.chart") }}', function(data) {
        $('#organization-chart').jstree('destroy');
        $('#organization-chart').jstree({
            'core': {
                'data': data,
                'themes': {
                    'name': 'default',
                    'responsive': true,
                    'variant': 'large',
                    'dots': true, 
                    'icons': true,
                },
                'check_callback': true,
                'multiple': false,
                'animation': 150,
                'expand_selected_onload': true,
                'dblclick_toggle': false
            },
            'plugins': ['search', 'wholerow', 'state'],
            'search': {
                'show_only_matches': true,
                'show_only_matches_children': true,
                'close_opened_onclear': false
            },
            'state': {
                'key': 'organization-chart-state',
                'filter': function(state) {
                    delete state.core.selected;
                    return state;
                }
            }
        }).on('ready.jstree', function() {
        }).on('search.jstree', function(e, data) {
            if (data.res.length) {
                $(this).jstree('open_all');
            }
        }).on('select_node.jstree', function(e, data) {
            const node = data.node;
            const id = node.id.split('_')[1];

            switch (node.original.type) {
                case 'department': showDepartmentModal(id); break;
                case 'position': showPositionModal(id); break;
                case 'employee': showEmployeeModal(id); break;
            }
        });
        
        $('#organization-chart').removeClass('loading');
    });
}
</script>
@endpush
