import './bootstrap';
import Alpine from 'alpinejs';
import '../css/appointments.css';
import './appointments.js';

window.Alpine = Alpine;

// Global Notification System
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notificationList') || document.getElementById('notificationContainer');
    }

    show(message, type = 'info', duration = 5000) {
        if (!this.container) {

            alert(message);
            return;
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-times-circle',
            warning: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        const icon = icons[type] || icons.info;

        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">
                    <i class="${icon}"></i>
                </div>
                <div class="notification-message">${message}</div>
                <button class="notification-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="notification-progress">
                <div class="notification-progress-bar"></div>
            </div>
        `;

        this.container.appendChild(notification);

        // Trigger animation
        requestAnimationFrame(() => {
            notification.classList.add('show');
        });

        // Progress bar animation
        const progressBar = notification.querySelector('.notification-progress-bar');
        if (progressBar && duration > 0) {
            progressBar.style.width = '100%';
            progressBar.style.transition = `width ${duration}ms linear`;
            requestAnimationFrame(() => {
                progressBar.style.width = '0%';
            });
        }

        // Close button
        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close(notification));
        }

        // Auto close
        if (duration > 0) {
            setTimeout(() => this.close(notification), duration);
        }

        return notification;
    }

    close(notification) {
        if (!notification) return;
        notification.classList.remove('show');
        notification.addEventListener('transitionend', () => {
            if (notification.parentNode) notification.remove();
        }, { once: true });
    }
}

// Initialize global instances
window.notifications = new NotificationSystem();
window.showAlert = (message, type = 'info') => window.notifications.show(message, type);

Alpine.start();