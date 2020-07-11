<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Words Admin Panel</title>
        <link rel="stylesheet" href="http://localhost/cdn/CSS/bootstrap.min.css">
        <script src="http://localhost/cdn/js/vue.js"></script>
        <?php
        /**
         * Opened a php tag just to remind you that
         * I'm too generous to give you the authority
         * to change anything in the header to adjust your needs.
         * Maybe the Internal styles below
         */
        ?>
</head>
<!-- Meaw -->
<?php
//Session started in index page and this is an inplace included file
// Word limit per page + How many words to skip + Sorting attribute
$Limit =  isset($_SESSION["Limit"]) ? $_SESSION["Limit"] : 10;
$Offset = isset($_SESSION["Offset"]) ? $_SESSION["Offset"] : 0;
$Sort = isset($_SESSION["Sort"]) ? $_SESSION["Sort"] : "";
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
<script src="script.js"></script>

</html>
<!-- You want More? -->