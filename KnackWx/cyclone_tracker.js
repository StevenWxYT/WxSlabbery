const apiUrl = "https://api.knackwx.com/atcf/v2";

// Fetch cyclone data
async function fetchCycloneData() {
  try {
    const response = await fetch(apiUrl, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        // Uncomment and add your API key here if required
        // "Authorization": "Bearer YOUR_API_KEY"
      },
    });

    if (!response.ok) {
      throw new Error("Failed to fetch cyclone data.");
    }

    const cycloneData = await response.json();
    displayCycloneData(cycloneData);
  } catch (error) {
    document.getElementById("cyclone-data").innerHTML = `<p>Error: ${error.message}</p>`;
  }
}

// Display cyclone data in HTML
function displayCycloneData(cycloneData) {
  const dataContainer = document.getElementById("cyclone-data");
  dataContainer.innerHTML = ""; // Clear previous content

  cycloneData.forEach((cyclone) => {
    const cycloneInfo = document.createElement("div");
    cycloneInfo.className = "cyclone-info";
    cycloneInfo.innerHTML = `
      <h2>${cyclone.storm_name}</h2>
      <p><strong>ATCF ID:</strong> ${cyclone.atcf_id}</p>
      <p><strong>Latitude:</strong> ${cyclone.latitude}</p>
      <p><strong>Longitude:</strong> ${cyclone.longitude}</p>
      <p><strong>Wind Speed:</strong> ${cyclone.winds} knots</p>
      <p><strong>Pressure:</strong> ${cyclone.pressure} hPa</p>
      <p><strong>Nature:</strong> ${cyclone.cyclone_nature}</p>
      <p><strong>Last Updated:</strong> ${new Date(cyclone.last_updated).toLocaleString()}</p>
    `;
    dataContainer.appendChild(cycloneInfo);
  });
}
