function updateClock() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    const dateString = now.toLocaleDateString('id-ID', options);
    const timeString = now.toLocaleTimeString('id-ID', { hour12: false });

    document.getElementById('current-date').textContent = dateString;
    document.getElementById('current-time').textContent = timeString;
}

// Update clock immediately and then every second
updateClock();
setInterval(updateClock, 1000);
