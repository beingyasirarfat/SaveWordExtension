<?php
if(session_id() == '' || !isset($_SESSION))
        session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Words Admin Panel</title>
        <link rel="stylesheet" href="CSS/bootstrap.min.css">
        <script src="js/vue.js"></script>
</head>
<!-- Meaw -->

<?php
// Word limit per page + How many words to skip + Sorting attribute
$Limit =  $_SESSION["Limit"] ?? 10;
$Offset = $_SESSION["Offset"] ?? 0;
$Sort =  $_SESSION["Sort"] ?? "";
?>

<body class=" bg-dark">
        <div class="container">
                <div class="row">
                        <div id="Vocabulary">
                                <Vocabulary></Vocabulary>
                                <Navigation></Navigation>
                        </div>
                </div>
        </div>
</body>

<!-- Here comes the master -->
<script src="js/script.js"></script>

</html>
<!-- You want More? -->