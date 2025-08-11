// === IBTrACS Main JavaScript ===
let fullData = [];
let currentPage = 1;
let pageSize = 25;

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    initializePage();
    loadStatistics();
    loadFilterOptions();
});

// Initialize page based on current route
function initializePage() {
    const path = window.location.pathname;
    
    if (path === '/') {
        // Main page - load initial data
        fetchIBTracs();
    } else if (path === '/dashboard') {
        // Dashboard page - load dashboard data
        loadDashboardData();
    } else if (path === '/storm-tracker') {
        // Storm tracker page - initialize map
        initializeStormTracker();
    } else if (path === '/analytics') {
        // Analytics page - load analytics
        loadAnalytics();
    }
}

// Load statistics for the stats cards
async function loadStatistics() {
    try {
        const response = await fetch('/api/statistics');
        const data = await response.json();
        
        // Update stats cards
        const totalStormsEl = document.getElementById('total-storms');
        const totalSeasonsEl = document.getElementById('total-seasons');
        const totalBasinsEl = document.getElementById('total-basins');
        const maxWindEl = document.getElementById('max-wind');
        
        if (totalStormsEl) totalStormsEl.textContent = data.total_storms.toLocaleString();
        if (totalSeasonsEl) totalSeasonsEl.textContent = data.total_seasons;
        if (totalBasinsEl) totalBasinsEl.textContent = data.total_basins;
        if (maxWindEl) maxWindEl.textContent = data.intensity_stats.max_wind || 'N/A';
        
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Load filter options (years and basins)
async function loadFilterOptions() {
    try {
        // Load years
        const yearsResponse = await fetch('/api/seasons');
        const years = await yearsResponse.json();
        
        const yearSelects = document.querySelectorAll('#filter-year, #tracker-year, #analytics-year-start, #analytics-year-end');
        yearSelects.forEach(select => {
            if (select) {
                years.forEach(year => {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    select.appendChild(option);
                });
            }
        });

        // Load basins
        const basinsResponse = await fetch('/api/basins');
        const basins = await basinsResponse.json();
        
        const basinSelects = document.querySelectorAll('#filter-basin, #tracker-basin, #analytics-basin');
        basinSelects.forEach(select => {
            if (select) {
                basins.forEach(basin => {
                    const option = document.createElement('option');
                    option.value = basin;
                    option.textContent = basin;
                    select.appendChild(option);
                });
            }
        });
    } catch (error) {
        console.error('Error loading filter options:', error);
    }
}

// Main IBTrACS data fetching function
async function fetchIBTracs() {
    showProgress();
    
    const name = document.getElementById('filter-name')?.value || '';
    const year = document.getElementById('filter-year')?.value || '';
    const basin = document.getElementById('filter-basin')?.value || '';
    const sid = document.getElementById('filter-sid')?.value || '';
    const limit = document.getElementById('row-limit')?.value || 25;

    try {
        const response = await fetch(`/api/ibtracs?name=${name}&season=${year}&basin=${basin}&sid=${sid}&limit=${limit}`);
        const data = await response.json();
        
        fullData = data;
        currentPage = 1;
        displayPage();
        updateResultsCount();
        
    } catch (error) {
        console.error('Error fetching IBTrACS data:', error);
        document.getElementById('cyclone-data').innerHTML = '<p class="error">Error loading data. Please try again.</p>';
    } finally {
        hideProgress();
    }
}

// Display current page of data
function displayPage() {
    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    const pageData = fullData.slice(start, end);
    const container = document.getElementById('cyclone-data');
    
    if (!container) return;
    
    container.innerHTML = '';

    if (pageData.length === 0) {
        container.innerHTML = '<p class="no-data">No storms found matching your criteria.</p>';
        return;
    }

    pageData.forEach((storm) => {
        const stormCard = document.createElement('div');
        stormCard.className = 'cyclone-card';
        stormCard.innerHTML = `
            <h4>${storm.name || 'Unnamed'} (${storm.season})</h4>
            <p><strong>SID:</strong> ${storm.sid || 'N/A'}</p>
            <p><strong>Basin:</strong> ${storm.basin || 'N/A'}</p>
            <p><strong>Location:</strong> ${storm.latitude || 'N/A'}, ${storm.longitude || 'N/A'}</p>
            <p><strong>Wind Speed:</strong> ${storm.wind || 'N/A'} kt</p>
            <p><strong>Pressure:</strong> ${storm.pressure || 'N/A'} hPa</p>
            <p><strong>Nature:</strong> ${storm.nature || 'N/A'}</p>
        `;
        container.appendChild(stormCard);
    });

    updatePagination();
}

// Update pagination controls
function updatePagination() {
    const container = document.getElementById('pagination');
    if (!container) return;
    
    container.innerHTML = '';
    const totalPages = Math.ceil(fullData.length / pageSize);

    if (totalPages <= 1) return;

    // Previous button
    if (currentPage > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.textContent = '← Previous';
        prevBtn.onclick = () => {
            currentPage--;
            displayPage();
        };
        container.appendChild(prevBtn);
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = i === currentPage ? 'active' : '';
        btn.onclick = () => {
            currentPage = i;
            displayPage();
        };
        container.appendChild(btn);
    }

    // Next button
    if (currentPage < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next →';
        nextBtn.onclick = () => {
            currentPage++;
            displayPage();
        };
        container.appendChild(nextBtn);
    }
}

// Update results count display
function updateResultsCount() {
    const countEl = document.getElementById('results-count');
    if (countEl) {
        countEl.textContent = `${fullData.length} results`;
    }
}

// Clear all filters
function clearFilters() {
    const inputs = document.querySelectorAll('#filter-name, #filter-sid');
    const selects = document.querySelectorAll('#filter-year, #filter-basin');
    
    inputs.forEach(input => input.value = '');
    selects.forEach(select => select.value = '');
    
    fetchIBTracs();
}

// Show progress indicator
function showProgress() {
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar-fill');
    const progressText = document.getElementById('progress-text');
    
    if (progressContainer) {
        progressContainer.style.display = 'block';
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = 'Loading data...';
    }
}

// Hide progress indicator
function hideProgress() {
    const progressContainer = document.getElementById('progress-container');
    if (progressContainer) {
        progressContainer.style.display = 'none';
    }
}

// Update progress bar
function updateProgress(percent) {
    const progressBar = document.getElementById('progress-bar-fill');
    const progressText = document.getElementById('progress-text');
    
    if (progressBar) {
        progressBar.style.width = `${percent}%`;
    }
    if (progressText) {
        progressText.textContent = `Loading... ${Math.round(percent)}%`;
    }
}

// === Dashboard Functions ===
async function loadDashboardData() {
    try {
        const response = await fetch('/api/statistics');
        const data = await response.json();
        
        // Update stats
        document.getElementById('total-storms').textContent = data.total_storms.toLocaleString();
        document.getElementById('total-seasons').textContent = data.total_seasons;
        document.getElementById('total-basins').textContent = data.total_basins;
        document.getElementById('max-wind').textContent = data.intensity_stats.max_wind || 'N/A';

        // Create charts
        createBasinChart(data.basin_distribution);
        createSeasonChart(data.season_distribution);
        
        // Load recent storms
        loadRecentStorms();
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function createBasinChart(basinData) {
    const ctx = document.getElementById('basinChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: basinData.map(item => item.basin),
            datasets: [{
                data: basinData.map(item => item.count),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function createSeasonChart(seasonData) {
    const ctx = document.getElementById('seasonChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: seasonData.map(item => item.season),
            datasets: [{
                label: 'Number of Storms',
                data: seasonData.map(item => item.count),
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

async function loadRecentStorms() {
    try {
        const response = await fetch('/api/ibtracs?limit=10');
        const storms = await response.json();
        
        const container = document.getElementById('recent-storms-list');
        if (!container) return;
        
        container.innerHTML = '';
        
        storms.forEach(storm => {
            const stormCard = document.createElement('div');
            stormCard.className = 'storm-card';
            stormCard.innerHTML = `
                <h4>${storm.name || 'Unnamed'} (${storm.season})</h4>
                <p><strong>Basin:</strong> ${storm.basin || 'N/A'}</p>
                <p><strong>Wind:</strong> ${storm.wind || 'N/A'} kt</p>
                <p><strong>Pressure:</strong> ${storm.pressure || 'N/A'} hPa</p>
            `;
            container.appendChild(stormCard);
        });
        
    } catch (error) {
        console.error('Error loading recent storms:', error);
    }
}

// === Storm Tracker Functions ===
function initializeStormTracker() {
    // This will be handled by the storm tracker page's own JavaScript
    console.log('Storm tracker initialized');
}

// === Analytics Functions ===
function loadAnalytics() {
    // This will be handled by the analytics page's own JavaScript
    console.log('Analytics initialized');
}

// === Utility Functions ===
function formatNumber(num) {
    return num.toLocaleString();
}

function getStormCategory(wind) {
    if (!wind) return 'Unknown';
    if (wind >= 135) return 'Category 5';
    if (wind >= 113) return 'Category 4';
    if (wind >= 96) return 'Category 3';
    if (wind >= 83) return 'Category 2';
    if (wind >= 64) return 'Category 1';
    if (wind >= 34) return 'Tropical Storm';
    return 'Tropical Depression';
}

function getStormColor(wind) {
    if (!wind) return '#666';
    if (wind >= 135) return '#FF0000'; // Category 5
    if (wind >= 113) return '#FF6600'; // Category 4
    if (wind >= 96) return '#FF9900';  // Category 3
    if (wind >= 83) return '#FFFF00';  // Category 2
    if (wind >= 64) return '#00FF00';  // Category 1
    return '#00FFFF'; // Tropical Storm
}

// === Event Listeners ===
document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners for search inputs
    const searchInputs = document.querySelectorAll('#filter-name, #filter-year, #filter-basin, #filter-sid');
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(fetchIBTracs, 500));
    });

    // Add event listener for row limit changes
    const rowLimitSelect = document.getElementById('row-limit');
    if (rowLimitSelect) {
        rowLimitSelect.addEventListener('change', () => {
            pageSize = parseInt(rowLimitSelect.value);
            currentPage = 1;
            fetchIBTracs();
        });
    }
});

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
