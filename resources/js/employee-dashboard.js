import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Pusher
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    forceTLS: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_token'),
        },
    },
});

// Listen for BookingCreated event on the private employee.bookings channel
window.Echo.private('employee.bookings')
    .listen('BookingCreated', (e) => {


        // Optionally, refresh the appointments list or update the UI dynamically
        // For example, reload the page or fetch new appointments via AJAX
        // Here, we simply reload the page to reflect new bookings
        location.reload();
    });
