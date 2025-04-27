// Initialize Echo instance
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.REVERB_APP_KEY,
    wsHost: process.env.REVERB_HOST || '127.0.0.1',
    wsPort: process.env.REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

// Initialize notifications
function initNotifications() {
    updateNotificationCount();
    loadLatestNotifications();
    initReverbListeners();
}

// Update notification count
function updateNotificationCount() {
    $.get('/tenant/notifications/count', function(response) {
        if (response.count > 0) {
            $('.notifications-count').text(response.count).show();
        } else {
            $('.notifications-count').hide();
        }
    });
}

// Load latest notifications
function loadLatestNotifications() {
    $.get('/tenant/notifications/latest', function(response) {
        const notificationsList = $('.notifications-list');
        notificationsList.empty();

        response.notifications.forEach(notification => {
            notificationsList.append(createNotificationItem(notification));
        });
    });
}

// Create notification item HTML
function createNotificationItem(notification) {
    const data = typeof notification.data === 'string' ? JSON.parse(notification.data) : notification.data;
    return `
        <a href="${data.url || '#'}" class="dropdown-item" data-id="${notification.id}">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${data.title}</h6>
                    <p class="text-muted mb-0">${data.message}</p>
                    <small class="text-muted">${moment(notification.created_at).fromNow()}</small>
                </div>
            </div>
        </a>
    `;
}

// Initialize Reverb listeners
function initReverbListeners() {
    // Listen for private notifications
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            // Add new notification to the list
            $('.notifications-list').prepend(createNotificationItem(notification));
            
            // Update count
            const count = parseInt($('.notifications-count').text() || '0') + 1;
            $('.notifications-count').text(count).show();
            
            // Show toast notification
            toastr.info(notification.data.message, notification.data.title);
        });
}

// Mark notification as read
function markAsRead(notificationId) {
    $.post(`/tenant/notifications/${notificationId}/read`, function() {
        updateNotificationCount();
    });
}

// Mark all notifications as read
function markAllAsRead() {
    $.post('/tenant/notifications/mark-all-read', function() {
        $('.notifications-count').hide();
        loadLatestNotifications();
    });
}

// Document ready
$(document).ready(function() {
    initNotifications();

    // Handle notification click
    $(document).on('click', '.notifications-list .dropdown-item', function(e) {
        e.preventDefault();
        const notificationId = $(this).data('id');
        markAsRead(notificationId);
        window.location.href = $(this).attr('href');
    });

    // Handle mark all as read
    $('.mark-all-read').click(function(e) {
        e.preventDefault();
        markAllAsRead();
    });
});
