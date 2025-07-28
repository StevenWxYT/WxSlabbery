document.getElementById('cycloneForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('calculate.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      document.getElementById('result').innerHTML = data;
    })
    .catch(err => {
      document.getElementById('result').innerHTML = "Error: " + err;
    });
});
