document.addEventListener('DOMContentLoaded', () => {
    const msg = document.getElementById('flash-message');
    if (msg) {
        setTimeout(() => {
            msg.style.display = 'none';
        }, 1500);
    }
});