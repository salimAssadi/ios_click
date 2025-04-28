@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Notifications') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item" aria-current="page"> {{ __('Notifications') }}</li>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            // Mark notification as read when clicked
            $('.notification-item').on('click', function() {
                const notificationId = $(this).data('notification-id');
                markAsRead(notificationId);
            });

            // Mark all as read
            $('#mark-all-read').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.notification-unread').removeClass('notification-unread').addClass('notification-read');
                            $('.badge-unread').remove();
                            toastr.success('{{ __("All notifications marked as read") }}');
                        }
                    }
                });
            });

            // Function to mark notification as read
            function markAsRead(id) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`[data-notification-id="${id}"]`).removeClass('notification-unread').addClass('notification-read');
                            $(`[data-notification-id="${id}"] .badge-unread`).remove();
                        }
                    }
                });
            }
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Notifications') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if($notifications->where('read_at', null)->count() > 0)
                                <a href="#" id="mark-all-read" class="btn btn-sm btn-primary">
                                    {{ __('Mark All as Read') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group notification-list">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ is_null($notification->read_at) ? 'notification-unread' : 'notification-read' }}" 
                                     data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3">
                                            <div class="avatar {{ is_null($notification->read_at) ? 'bg-light-primary' : 'bg-light-secondary' }}">
                                                <i data-feather="{{ isset($notification->data['icon']) ? $notification->data['icon'] : 'bell' }}" 
                                                   class="{{ is_null($notification->read_at) ? 'text-primary' : 'text-secondary' }}"></i>
                                            </div>
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">
                                                    {{ isset($notification->data['title']) ? $notification->data['title'] : __('Notification') }}
                                                    @if(is_null($notification->read_at))
                                                        <span class="badge bg-primary badge-unread">{{ __('New') }}</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">{{ isset($notification->data['message']) ? $notification->data['message'] : $notification->data['body'] ?? '' }}</p>
                                            @if(isset($notification->data['document_title']) && isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    {{ $notification->data['document_title'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg mb-3">
                                <i data-feather="bell-off" class="text-muted"></i>
                            </div>
                            <h6>{{ __('No notifications found') }}</h6>
                            <p class="text-muted">{{ __('You do not have any notifications at the moment') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .notification-unread {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-left: 3px solid var(--bs-primary);
    }
    .notification-item {
        transition: all 0.3s ease;
    }
    .notification-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        cursor: pointer;
    }
    .notification-list {
        max-height: 600px;
        overflow-y: auto;
    }
</style>
