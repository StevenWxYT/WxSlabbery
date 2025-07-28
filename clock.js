function updateClock() {
    const clockElement = document.getElementById('clock');
    const dateElement = document.getElementById('date');
    
    const now = new Date();
    
    // Get hours, minutes, and seconds, formatted with leading zeros
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    // Display time in 24-hour format
    clockElement.textContent = `${hours}:${minutes}:${seconds}`;
    
    // Get the date in a human-readable format
    const dateString = now.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Update the date element
    dateElement.textContent = dateString;
    
    // Change background color based on the time of day
    const hour = now.getHours();
    if (hour >= 6 && hour < 12) {
        document.body.style.background = 'linear-gradient(to right, #f8c291, #f38caa)'; // Morning
    } else if (hour >= 12 && hour < 18) {
        document.body.style.background = 'linear-gradient(to right, #f39c12, #e74c3c)'; // Afternoon
    } else if (hour >= 18 && hour < 21) {
        document.body.style.background = 'linear-gradient(to right, #2c3e50, #34495e)'; // Evening
    } else {
        document.body.style.background = 'linear-gradient(to right, #2c3e50, #000000)'; // Night
    }
}

// Update the clock every second
setInterval(updateClock, 1000);

// Call updateClock to display the time immediately on load
updateClock();
