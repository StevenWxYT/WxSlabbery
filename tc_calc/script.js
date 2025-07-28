let calcMemory = 0;
let calcExpression = "";

// Append number/operator to display
function appendCalc(value) {
  calcExpression += value;
  updateCalcDisplay();
}

// Clear calculator
function clearCalc() {
  calcExpression = "";
  updateCalcDisplay();
}

// Update display
function updateCalcDisplay() {
  document.getElementById("calcDisplay").value = calcExpression;
}

// Evaluate calculator expression
function calculateBasic() {
  try {
    const result = eval(calcExpression);
    document.getElementById("calcDisplay").value = result;
    addToHistory(calcExpression + " = " + result);
    calcExpression = result.toString();
  } catch (error) {
    document.getElementById("calcDisplay").value = "Error";
    calcExpression = "";
  }
}

// Memory functions
function memoryAdd() {
  try {
    calcMemory += eval(calcExpression || "0");
    addToHistory("M+ " + calcExpression);
  } catch {}
}

function memorySubtract() {
  try {
    calcMemory -= eval(calcExpression || "0");
    addToHistory("M− " + calcExpression);
  } catch {}
}

function memoryRecall() {
  calcExpression += calcMemory;
  updateCalcDisplay();
  addToHistory("MR = " + calcMemory);
}

function memoryClear() {
  calcMemory = 0;
  addToHistory("MC (Memory Cleared)");
}

// Add to calculation history
function addToHistory(entry) {
  const log = document.getElementById("historyLog");
  const p = document.createElement("p");
  p.textContent = entry;
  log.prepend(p);
}

// Show formula dynamically
const formulaMap = {
  sshs: "Category = based on wind speed (in knots)",
  holland: "B = log((Pn − Pc)/(Pn − Pc × e⁻¹)) / log(r/Rm)",
  ckz: "V = A × (Pn − Pc)^B",
  rmw: "RMW ≈ 46.6 × e^(−0.0153 × Vmax)",
  surge: "Surge = a × P + b × W + c × S",
  pdeficit: "ΔP = Pn − Pc",
  chp: "CHP = ρ × Cp × ΔT × h",
  ace: "ACE = 10⁴ × Σ(Vmax² × Δt)"
};

document.getElementById("calcType").addEventListener("change", function () {
  const type = this.value;
  document.getElementById("formulaBox").innerText = formulaMap[type] || "";
  generateDynamicFields(type);
});

// Dynamic input fields per calculation type
function generateDynamicFields(type) {
  const container = document.getElementById("dynamicFields");
  container.innerHTML = "";

  const fieldConfigs = {
    sshs: [{ label: "Wind Speed (kt)", name: "wind" }],
    holland: [
      { label: "Pn (Ambient Pressure)", name: "pn" },
      { label: "Pc (Central Pressure)", name: "pc" },
      { label: "r (Distance from Center)", name: "r" },
      { label: "Rm (Radius of Max Wind)", name: "rm" }
    ],
    ckz: [
      { label: "Pn (Ambient Pressure)", name: "pn" },
      { label: "Pc (Central Pressure)", name: "pc" }
    ],
    rmw: [{ label: "Vmax (Maximum Wind Speed)", name: "vmax" }],
    surge: [
      { label: "Pressure (P)", name: "p" },
      { label: "Wind Speed (W)", name: "w" },
      { label: "Storm Size (S)", name: "s" }
    ],
    pdeficit: [
      { label: "Pn (Ambient Pressure)", name: "pn" },
      { label: "Pc (Central Pressure)", name: "pc" }
    ],
    chp: [
      { label: "ρ (Density)", name: "rho" },
      { label: "Cp (Specific Heat)", name: "cp" },
      { label: "ΔT (Temperature Diff)", name: "dT" },
      { label: "h (Depth)", name: "h" }
    ],
    ace: [
      { label: "Vmax (Wind Speed)", name: "vmax" },
      { label: "Δt (Time in 6-hourly intervals)", name: "dt" }
    ]
  };

  const fields = fieldConfigs[type] || [];

  fields.forEach(field => {
    const label = document.createElement("label");
    label.setAttribute("for", field.name);
    label.textContent = field.label;

    const input = document.createElement("input");
    input.type = "number";
    input.step = "any";
    input.name = field.name;
    input.required = true;

    container.appendChild(label);
    container.appendChild(input);
  });
}
