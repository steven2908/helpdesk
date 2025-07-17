import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    if (window.Laravel?.userIsStaffOrAdmin) {
        console.log('✅ User is staff or admin, subscribing to ticket events...');

        window.Echo.channel('staff.ticket')
            .listen('.ticket.created', (e) => {
                console.log('📩 Event diterima:', e);
                alert('Ticket baru dibuat: ' + e.subject + '\nDari user: ' + e.user);
            });
    } else {
        console.log('🚫 User is NOT staff or admin, not subscribing to ticket events.');
    }
});
