document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("filterForm");
  const tableDiv = document.getElementById("resultsTable");

  function fetchStorms(params = {}) {
    const query = new URLSearchParams(params).toString();
    fetch("ibtracs_search.php?" + query)
      .then((res) => res.json())
      .then((data) => {
        if (!data || data.length === 0) {
          tableDiv.innerHTML = "<p>No storms found.</p>";
          return;
        }

        let table = `<table class="styled-table">
          <thead>
            <tr>
              <th>SID</th>
              <th>Name</th>
              <th>Year</th>
              <th>Basin</th>
              <th>Intensity</th>
              <th>Latitude</th>
              <th>Longitude</th>
            </tr>
          </thead>
          <tbody>`;

        data.forEach(storm => {
          table += `<tr>
            <td><a href="ibtracs_view.php?sid=${encodeURIComponent(storm.sid)}">${storm.sid}</a></td>
            <td>${storm.name}</td>
            <td>${storm.season}</td>
            <td>${storm.basin}</td>
            <td>${storm.intensity}</td>
            <td>${storm.latitude}</td>
            <td>${storm.longitude}</td>
          </tr>`;
        });

        table += "</tbody></table>";
        tableDiv.innerHTML = table;
      })
      .catch(err => {
        console.error(err);
        tableDiv.innerHTML = "<p>Error loading data.</p>";
      });
  }

  // Initial load
  fetchStorms();

  // Filter on submit
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    const params = {};
    for (const [key, value] of formData.entries()) {
      if (value.trim() !== "") {
        params[key] = value;
      }
    }
    fetchStorms(params);
  });
});
