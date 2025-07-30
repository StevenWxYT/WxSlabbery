let fullData = [];
let currentPage = 1;
let pageSize = 20;

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("page-size").addEventListener("change", () => {
    pageSize = parseInt(document.getElementById("page-size").value);
    currentPage = 1;
    displayPage();
  });

  document.getElementById("filter-name").addEventListener("input", fetchIBTracs);
  document.getElementById("filter-year").addEventListener("input", fetchIBTracs);
  document.getElementById("filter-basin").addEventListener("change", fetchIBTracs);
  document.getElementById("filter-sid").addEventListener("input", fetchIBTracs);
});

async function fetchIBTracs() {
  document.getElementById("loading").style.display = "block";
  document.getElementById("progress-container").style.display = "block";
  document.getElementById("cyclone-data").innerHTML = "";
  document.getElementById("pagination").innerHTML = "";
  updateProgressBar(0);

  const name = document.getElementById("filter-name").value.trim();
  const year = document.getElementById("filter-year").value.trim();
  const basin = document.getElementById("filter-basin").value.trim();
  const sid = document.getElementById("filter-sid").value.trim();

  const url = `/api/ibtracs?name=${name}&year=${year}&basin=${basin}&sid=${sid}`;

  try {
    const response = await fetch(url);
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let result = "";
    let receivedLength = 0;
    let contentLength = +response.headers.get("Content-Length") || 1000000;

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      receivedLength += value.length;
      result += decoder.decode(value);
      updateProgressBar((receivedLength / contentLength) * 100);
    }

    fullData = JSON.parse(result);
    currentPage = 1;
    displayPage();
  } catch (error) {
    document.getElementById("cyclone-data").innerHTML = `<p>Error loading data</p>`;
  } finally {
    document.getElementById("loading").style.display = "none";
    document.getElementById("progress-container").style.display = "none";
  }
}

function updateProgressBar(percent) {
  const bar = document.getElementById("progress-bar");
  bar.style.width = `${percent.toFixed(1)}%`;
  bar.innerText = `${percent.toFixed(0)}%`;
}

function displayPage() {
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const pageData = fullData.slice(start, end);
  const container = document.getElementById("cyclone-data");
  container.innerHTML = "";

  pageData.forEach((storm) => {
    const div = document.createElement("div");
    div.className = "cyclone-info";
    div.innerHTML = `
      <h2>${storm.name || "Unnamed"} (${storm.season})</h2>
      <p><strong>SID:</strong> ${storm.sid}</p>
      <p><strong>Basin:</strong> ${storm.basin}</p>
      <p><strong>Time:</strong> ${storm.time}</p>
      <p><strong>Lat:</strong> ${storm.latitude}, <strong>Lon:</strong> ${storm.longitude}</p>
      <p><strong>Wind:</strong> ${storm.winds} kt, <strong>Pressure:</strong> ${storm.pressure} hPa</p>
    `;
    container.appendChild(div);
  });

  updatePagination();
}

function updatePagination() {
  const totalPages = Math.ceil(fullData.length / pageSize);
  const container = document.getElementById("pagination");
  container.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    btn.className = i === currentPage ? "active-page" : "";
    btn.onclick = () => {
      currentPage = i;
      displayPage();
    };
    container.appendChild(btn);
  }
}

function updateProgress(percent) {
  const progressContainer = document.getElementById('progress-container');
  const progressBar = document.getElementById('progress-bar');
  progressContainer.style.display = 'block';
  progressBar.style.width = percent + '%';
}
