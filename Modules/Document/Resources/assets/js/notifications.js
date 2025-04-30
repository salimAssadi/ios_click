
// Notification sound
const notificationSound = new Audio('/assets/sounds/notification.mp3');

// Initialize notifications
function initNotifications() {
    updateNotificationCount();
    loadLatestNotifications();
    // initReverbListeners();
}


// Update notification count
function updateNotificationCount() {
    $.get('/tenant/notifications/get-unread', function(response) {
        if (response.count > 0) {
            $('.notifications-count').text(response.count).show();
            $('.notification-empty').addClass('d-none');
        } else {
            $('.notifications-count').hide();
            $('.notification-empty').removeClass('d-none');
        }
    });
}

// Load latest notifications
function loadLatestNotifications() {
    $.get('/tenant/notifications/get-unread', function(response) {
        const notificationsList = $('.notifications-list');
        // Keep the empty notification message element
        const emptyState = notificationsList.find('.notification-empty');
        notificationsList.empty();
        
        if (emptyState.length) {
            notificationsList.append(emptyState);
        }

        if (response.notifications.length === 0) {
            $('.notification-empty').removeClass('d-none');
        } else {
            $('.notification-empty').addClass('d-none');
            response.notifications.forEach(notification => {
                notificationsList.append(createNotificationItem(notification));
            });
            
            // Add event listeners to mark as read buttons
            $('.mark-read').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                markAsRead($(this).data('id'));
            });
        }
    });
}

// Create notification item HTML with improved design
function createNotificationItem(notification) {
    const data = typeof notification.data === 'string' ? JSON.parse(notification.data) : notification.data;
    
    // Determine icon and style based on notification type
    let iconClass = 'fa-bell';
    let bgClass = 'bg-primary';
    let textClass = 'text-white';
    
    if (data.type === 'success') {
        iconClass = data.icon || 'fa-check-circle';
        bgClass = 'bg-success';
    } else if (data.type === 'danger') {
        iconClass = data.icon || 'fa-times-circle';
        bgClass = 'bg-danger';
    } else if (data.type === 'warning') {
        iconClass = data.icon || 'fa-exclamation-triangle';
        bgClass = 'bg-warning';
    } else if (data.type === 'info') {
        iconClass = data.icon || 'fa-info-circle';
        bgClass = 'bg-info';
    }
    
    return `
        <a href="${data.url || '#'}" class="dropdown-item notification-item unread" data-id="${notification.id}">
            <div class="d-flex align-items-center">
                <div class="notification-icon ${bgClass} ${textClass} me-3">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-medium">${data.title || 'إشعار'}</h6>
                    <p class="text-sm mb-1">${data.message}</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="notification-time">${moment(notification.created_at).fromNow()}</small>
                        <button class="btn btn-sm text-primary mark-read" data-id="${notification.id}">
                            <i class="ti ti-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </a>
    `;
}

// Play notification sound
function playNotificationSound() {
    try {
        notificationSound.currentTime = 0;
        notificationSound.play().catch(error => {
            console.error('Error playing notification sound:', error);
        });
    } catch (error) {
        console.error('Error playing notification sound:', error);
    }
}

// // Initialize Reverb listeners
// function initReverbListeners() {
//     // Listen for private notifications on user channel
//     window.Echo.private(`notifications.${userId}`)
//         .listen('NotificationReceived', (data) => {
//             // Play notification sound
//             playNotificationSound();
            
//             // Add new notification to the list
//             $('.notifications-list').prepend(createNotificationItem(data.notification));
//             $('.notification-empty').addClass('d-none');
            
//             // Update count
//             const count = parseInt($('.notifications-count').text() || '0') + 1;
//             $('.notifications-count').text(count).show();
            
//             // Show toast notification if toastr is available
//             if (typeof toastr !== 'undefined') {
//                 const notificationData = data.notification.data;
//                 toastr.info(notificationData.message, notificationData.title);
//             }
//         });
// }


// Mark notification as read
function markAsRead(notificationId) {
    $.ajax({
        url: `/tenant/notifications/mark-as-read/${notificationId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Update the notification appearance
                $(`.notification-item[data-id="${notificationId}"]`).removeClass('unread');
                
                // Update counter
                const currentCount = parseInt($('.notifications-count').text() || '0');
                const newCount = Math.max(0, currentCount - 1);
                
                if (newCount > 0) {
                    $('.notifications-count').text(newCount);
                } else {
                    $('.notifications-count').hide();
                    if ($('.notifications-list .notification-item.unread').length === 0) {
                        $('.notification-empty').removeClass('d-none');
                    }
                }
            }
        }
    });
}


// Mark all notifications as read
function markAllAsRead() {
    $.ajax({
        url: '/tenant/notifications/mark-all-as-read',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Update all notifications to read state
                $('.notification-item').removeClass('unread');
                
                // Update counter to zero
                $('.notifications-count').hide();
                
                // Show empty state if needed
                if ($('.notifications-list .notification-item').length === 0) {
                    $('.notification-empty').removeClass('d-none');
                }
            }
        }
    });
}

// Document ready
$(document).ready(function() {
    // Initialize everything
    initNotifications();

    // Handle notification click
    $(document).on('click', '.notifications-list .dropdown-item', function(e) {
        if (!$(e.target).hasClass('mark-read') && !$(e.target).parent().hasClass('mark-read')) {
            e.preventDefault();
            const notificationId = $(this).data('id');
            markAsRead(notificationId);
            window.location.href = $(this).attr('href');
        }
    });

    // Handle mark all as read
    $('.mark-all-read').click(function(e) {
        e.preventDefault();
        markAllAsRead();
    });
});
