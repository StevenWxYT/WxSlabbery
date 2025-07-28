const unitOptions = {
    length: ['m', 'km', 'cm', 'mm', 'mi', 'yd', 'ft', 'in'],
    area: ['sqm', 'sqkm', 'sqmi', 'sqyd', 'sqft', 'sqin', 'acre', 'hectare'],
    volume: ['m3', 'liter', 'ml', 'gallon', 'quart', 'pint', 'cup', 'oz'],
    mass: ['kg', 'g', 'mg', 'lb', 'oz', 'tonne'],
    speed: ['mps', 'kmh', 'mph', 'knot'],
    temperature: ['c', 'f', 'k'],
    time: ['sec', 'min', 'hr', 'day', 'week'],
    pressure: ['pa', 'kpa', 'bar', 'psi', 'atm'],
    angle: ['deg', 'rad', 'grad'],
    power: ['watt', 'kw', 'hp'],
    data: ['bit', 'byte', 'kb', 'mb', 'gb'],
    energy: ['j', 'kj', 'cal', 'kcal', 'wh', 'kwh'],
};

function updateUnits() {
    const category = document.getElementById('category').value;
    const fromSelect = document.getElementById('from_unit');
    const toSelect = document.getElementById('to_unit');
    const units = unitOptions[category];

    fromSelect.innerHTML = '';
    toSelect.innerHTML = '';

    units.forEach(unit => {
        const fromOption = new Option(unit, unit);
        const toOption = new Option(unit, unit);
        fromSelect.add(fromOption);
        toSelect.add(toOption);
    });
}

document.addEventListener('DOMContentLoaded', updateUnits);
