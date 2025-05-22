@props(['filters' => []])
{{-- <style>
    .filter-card {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        /* box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05); */
    }

    .filter-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .btn-filter {
        border-radius: 30px !important;
        min-width: 130px;
        font-weight: 500;
        padding: 0.4rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #dcdcdc;
        transition: all 0.2s ease-in-out;
    }

    .btn-filter:hover,
    .btn-filter.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }

    .input-group-custom-days {
        max-width: 180px;
        margin-left: 1rem;
    }

    @media (max-width: 600px) {
        .filter-card {
            padding: 1rem;
        }

        .btn-filter {
            min-width: 100px;
            font-size: 0.9rem;
        }
    }

    .modal-filter .modal-content {
        border-radius: 1rem;
        border: none;
        padding: 1.5rem;
        background: #fff;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-filter .modal-header {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 1rem;
    }

    .modal-filter .modal-title {
        font-weight: 600;
        color: #333;
    }

    .modal-filter .btn-close {
        background: none;
        font-size: 1.2rem;
    }

    .modal-filter .modal-footer {
        border-top: none;
        padding-top: 0;
        justify-content: flex-end;
    }

    .modal-filter .btn-secondary {
        background-color: #e9ecef;
        border: none;
        color: #333;
    }

    .modal-filter .btn-secondary:hover {
        background-color: #dcdcdc;
    }

    .modal-filter .btn-primary {
        background-color: #0d6efd;
        border: none;
    }

    .modal-filter .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-outline-primary {
        border-radius: 30px;
        font-weight: 500;
    }

    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style> --}}

@props(['filters' => []])
<style>
    .filter-bar {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .filter-bar .filter-label {
        font-weight: 600;
        color: #3a3a3a;
        margin-bottom: 0;
        margin-left: 0.5rem;
        white-space: nowrap;
    }
    .btn-group .btn {
        border-radius: 2rem !important;
        min-width: 110px;
        font-weight: 500;
        padding: 0.3rem 1rem;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .input-group-custom-days {
        max-width: 170px;
        margin-right: 0.5rem;
    }
    @media (max-width: 900px) {
        .filter-bar {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem 0.5rem;
        }
        .filter-bar .filter-group {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@if(!empty($filters))
{{-- <div class="filter-card"> --}}
<div class="filter-bar" id="dynamic-filters">
    @foreach($filters as $filter)
        <div class="filter-group">
            <span class="filter-label">{{ $filter['label'] ?? '' }}</span>
            @if($filter['type'] === 'radio_group')
                <div class="btn-group" role="group">
                    @foreach($filter['options'] as $key => $label)
                        <input type="radio" class="btn-check filter-input" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}_{{ $key }}" value="{{ $key }}" autocomplete="off">
                        <label class="btn btn-outline-primary" for="{{ $filter['name'] }}_{{ $key }}">{{ $label }}</label>
                    @endforeach
                </div>
                @if(isset($filter['custom_days']) && $filter['custom_days'])
                    <div class="input-group input-group-custom-days ms-2">
                        <span class="input-group-text"><i class="ti ti-calendar-event"></i></span>
                        <input type="number" min="1" class="form-control filter-input" name="custom_days" placeholder="{{ __('عدد الأيام') }}">
                    </div>
                @endif
            @elseif($filter['type'] === 'select')
                <select class="form-select filter-input" name="{{ $filter['name'] }}">
                    <option value="">{{ $filter['placeholder'] ?? __('الكل') }}</option>
                    @foreach($filter['options'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            @elseif($filter['type'] === 'text')
                <input type="text" class="form-control filter-input" name="{{ $filter['name'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
            @endif
        </div>
    @endforeach
</div>
{{-- </div> --}}
@endif
{{-- <script>
    // فلترة مباشرة عند تغيير أي قيمة
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#dynamic-filters .filter-input').forEach(function(input) {
            input.addEventListener('change', function() {
                if (window.table) { window.table.ajax.reload(); }
            });
        });
    });
</script> --}}
 
