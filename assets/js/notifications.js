/**
 * Notification Handler
 * Manages real-time notifications for both students and admins
 */
"use strict";

class NotificationHandler {
    constructor() {
        this.isInitialized = false;
        this.notificationCount = 0;
        this.refreshInterval = 30000; // 30 seconds
        this.intervalId = null;
    }

    /**
     * Initialize notification system
     */
    init() {
        if (this.isInitialized) return;

        this.loadNotifications();
        this.loadNotificationCount();
        this.startAutoRefresh();
        this.bindEvents();

        this.isInitialized = true;
    }

    /**
     * Load notifications from API
     */
    async loadNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();

            if (data.success) {
                this.renderNotifications(data.data);
            } else {
                console.error('Failed to load notifications:', data.error);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    /**
     * Load notification count from API
     */
    async loadNotificationCount() {
        try {
            const response = await fetch('/api/notification-count');
            const data = await response.json();

            if (data.success) {
                this.updateNotificationBadge(data.count);
                this.notificationCount = data.count;
            } else {
                console.error('Failed to load notification count:', data.error);
            }
        } catch (error) {
            console.error('Error loading notification count:', error);
        }
    }

    /**
     * Render notifications in dropdown
     */
    renderNotifications(notifications) {
        const notificationsList = document.querySelector('.dropdown-notifications-list .list-group');

        if (!notificationsList) {
            console.warn('Notification list container not found');
            return;
        }

        // Clear existing notifications
        notificationsList.innerHTML = '';

        if (notifications.length === 0) {
            notificationsList.innerHTML = `
                <li class="list-group-item text-center text-muted py-4">
                    <i class="bx bx-bell-off icon-md mb-2"></i>
                    <div>No new notifications</div>
                </li>
            `;
            return;
        }

        // Render each notification
        notifications.forEach(notification => {
            const notificationHtml = this.createNotificationItem(notification);
            notificationsList.appendChild(notificationHtml);
        });
    }

    /**
     * Create single notification item HTML
     */
    createNotificationItem(notification) {
        const li = document.createElement('li');
        li.className = `list-group-item list-group-item-action dropdown-notifications-item ${notification.is_read ? 'marked-as-read' : ''}`;

        li.innerHTML = `
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <span class="${notification.badge_class} rounded-circle avatar-initial">
                            <i class="icon-base bx ${notification.icon}"></i>
                        </span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 small">${this.escapeHtml(notification.title)}</h6>
                    <small class="d-block mb-1 text-body">Priority: ${notification.priority}</small>
                    <small class="text-body-secondary">${notification.date}</small>
                </div>
                <div class="flex-shrink-0 dropdown-notifications-actions">
                    ${!notification.is_read ? '<span class="badge badge-dot"></span>' : ''}
                </div>
            </div>
        `;

        // Add click handler to navigate to appropriate page
        li.addEventListener('click', (e) => {
            e.preventDefault();
            this.handleNotificationClick(notification);
        });

        return li;
    }

    /**
     * Handle notification click
     */
    handleNotificationClick(notification) {
        // Navigate to the appropriate page
        window.location.href = notification.url;
    }

    /**
     * Update notification badge count
     */
    updateNotificationBadge(count) {
        const badges = document.querySelectorAll('.badge-notifications');

        badges.forEach(badge => {
            if (count > 0) {
                badge.style.display = 'block';
                badge.textContent = count > 99 ? '99+' : count;
            } else {
                badge.style.display = 'none';
            }
        });

        // Update header text
        const headerCountText = document.querySelector('.dropdown-menu-header .badge');
        if (headerCountText) {
            headerCountText.textContent = `${count} New`;
            headerCountText.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    /**
     * Start auto-refresh for notifications
     */
    startAutoRefresh() {
        this.intervalId = setInterval(() => {
            this.loadNotificationCount();
            // Only reload full notifications if dropdown is open
            const dropdown = document.querySelector('.dropdown-notifications');
            if (dropdown && dropdown.classList.contains('show')) {
                this.loadNotifications();
            }
        }, this.refreshInterval);
    }

    /**
     * Stop auto-refresh
     */
    stopAutoRefresh() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    /**
     * Bind event handlers
     */
    bindEvents() {
        // Reload notifications when dropdown is opened
        const notificationDropdown = document.querySelector('.dropdown-notifications .dropdown-toggle');
        if (notificationDropdown) {
            notificationDropdown.addEventListener('click', () => {
                this.loadNotifications();
            });
        }

        // Handle "mark all as read" button
        const markAllReadBtn = document.querySelector('.dropdown-notifications-all');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }

        // Handle page visibility change (pause/resume refresh)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoRefresh();
            } else {
                this.startAutoRefresh();
                this.loadNotificationCount();
            }
        });
    }

    /**
     * Mark all notifications as read (placeholder for future implementation)
     */
    async markAllAsRead() {
        // This would call an API to mark notifications as read
        // For now, just refresh the count
        this.loadNotificationCount();
        this.loadNotifications();
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Destroy notification handler
     */
    destroy() {
        this.stopAutoRefresh();
        this.isInitialized = false;
    }
}

// Global notification handler instance
window.notificationHandler = new NotificationHandler();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Small delay to ensure all elements are loaded
    setTimeout(() => {
        window.notificationHandler.init();
    }, 500);
});
