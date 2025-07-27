document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('search-form');
    const resultsContainer = document.getElementById('results');
    const paginationContainer = document.getElementById('pagination');
    let currentPage = 1;

    function fetchStorms(page = 1) {
        currentPage = page;
        const formData = new FormData(form);
        const params = new URLSearchParams();
        formData.forEach((value, key) => {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        });
        params.append('page', page);

        fetch(`../php/ibtracs_search.php?${params.toString()}`)
            .then(res => res.json())
            .then(json => {
                renderResults(json.data);
                renderPagination(json.total_pages, json.current_page);
            })
            .catch(err => {
                console.error('Fetch error:', err);
                resultsContainer.innerHTML = '<p>Error loading data.</p>';
            });
    }

    function renderResults(data) {
        if (data.length === 0) {
            resultsContainer.innerHTML = '<p>No results found.</p>';
            return;
        }

        let html = `<table border="1" cellspacing="0" cellpadding="6">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SID</th>
                    <th>Season</th>
                    <th>Basin</th>
                    <th>Wind (kts)</th>
                    <th>Pressure (mb)</th>
                    <th>Date</th>
                </tr>
            </thead><tbody>`;

        data.forEach(row => {
            html += `<tr>
                <td><a href="ibtracs_view.php?sid=${encodeURIComponent(row.sid)}">${row.name}</a></td>
                <td><a href="ibtracs_view.php?sid=${encodeURIComponent(row.sid)}">${row.sid}</a></td>
                <td>${row.season}</td>
                <td>${row.basin}</td>
                <td>${row.wind_kts}</td>
                <td>${row.pressure_mb}</td>
                <td>${row.timestamp}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        resultsContainer.innerHTML = html;
    }

    function renderPagination(totalPages, currentPage) {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '';
        const maxPagesToShow = 10;
        let start = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let end = Math.min(totalPages, start + maxPagesToShow - 1);

        if (start > 1) {
            html += `<button data-page="1">« First</button>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<button data-page="${i}" ${i === currentPage ? 'disabled' : ''}>${i}</button>`;
        }

        if (end < totalPages) {
            html += `<button data-page="${totalPages}">Last »</button>`;
        }

        paginationContainer.innerHTML = html;

        paginationContainer.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.getAttribute('data-page'));
                fetchStorms(page);
            });
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchStorms(1);
    });

    // Initial load
    fetchStorms(1);
});
