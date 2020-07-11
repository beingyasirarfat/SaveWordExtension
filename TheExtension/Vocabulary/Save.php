<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>

<div class="container">

  <form action="index.php" method="POST" class="needs-validation" novalidate>

    <div class="form-group">
      <label for="uname">Word:</label>
      <input type="text" class="form-control" id="Word" placeholder="Enter Word To Save" name="Word" required>
      <div class="valid-feedback">Valid.</div>
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>

    <div class="form-group">
      <label for="Meaning">Meaning:</label>
      <input type="text" class="form-control" id="Meaning" placeholder="Meaing of the word" name="Meaning" required>
      <div class="valid-feedback">Valid.</div>
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <div class="form-group">
      <label for="Bengali">Bengali:</label>
      <input type="text" class="form-control" id="Bengali" placeholder="Bengali Meaning" name="Bengali" required>
      <div class="valid-feedback">Valid.</div>
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>
<script>
// Disable form submissions if there are invalid fields
// (function() {
//   'use strict';
//   window.addEventListener('load', function() {
//     // Get the forms we want to add validation styles to
//     var forms = document.getElementsByClassName('needs-validation');
//     // Loop over them and prevent submission
//     var validation = Array.prototype.filter.call(forms, function(form) {
//       form.addEventListener('submit', function(event) {
//         if (form.checkValidity() === false) {
//           event.preventDefault();
//           event.stopPropagation();
//         }
//         form.classList.add('was-validated');
//       }, false);
//     });
//   }, false);
// })();
</script>

</body>
</html>
