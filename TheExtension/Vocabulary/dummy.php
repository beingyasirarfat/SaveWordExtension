<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Words Admin Panel</title>
        <link rel="stylesheet" href="http://localhost/cdn/CSS/bootstrap.min.css">
        <!-- <link rel="stylesheet" href="http://localhost/cdn/CSS/fontawesome.css"> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

<body class=" bg-dark">

        <div class="container">
                <div class="row" id="Vocabulary">

                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "vocabulary";
                        // Word limit per page + How many words to skip + Sorting attribute
                        $Limit =  isset($_SESSION["Limit"]) ? $_SESSION["Limit"] : 10;
                        $Offset = isset($_SESSION["Offset"]) ? $_SESSION["Offset"] : 0;
                        $Sort = isset($_SESSION["Sort"]) ? $_SESSION["Sort"] : "";

                        $conn = mysqli_connect($servername, $username, $password, $dbname);

                        if (!$conn) {

                                die("Connection failed: " . mysqli_connect_error());
                                //It's not my fault if connection fails
                        }


                        /**
                         * Get request's are to be handled before the database search
                         * to provide what is requested and expected
                         */

                        if (isset($_GET["Navigate"])) {

                                if ($_GET["Navigate"] == "Next") {

                                        //There's limit in everything
                                        //Don't let anyone cross the limit
                                        //Here I mean why would anyone go beyond the database stocks!
                                        //if Next request index is more than database index just skip increasing the offset pointer
                                        $index = mysqli_query($conn, "SELECT COUNT(Count) AS Count FROM Words");
                                        $index = mysqli_fetch_assoc($index);

                                        if ($index["Count"] && ($Offset + $Limit < $index["Count"])) {
                                                $Offset += $Limit;
                                                $_SESSION["Offset"] = $Offset;
                                        }
                                } elseif ($_GET["Navigate"] == "Previous") {
                                        //And obviously negative existence is always theorytical 
                                        //so skip if request's beyond zero
                                        if ($Offset - $Limit >= 0) {
                                                $Offset -= $Limit;
                                                $_SESSION["Offset"] = $Offset;
                                        } else {
                                                $_SESSION["Offset"] = $Offset = 0;
                                        }
                                }
                        }


                        if (isset($_GET["Limit"]) && is_numeric($_GET["Limit"])) {
                                //limit the limit within limit of 100 and 0
                                if ($_GET["Limit"] >= 0 && $_GET["Limit"] < 101) {
                                        $_SESSION["Limit"] = $Limit = $_GET["Limit"] ? $_GET["Limit"] : 10;
                                }
                        }

                        if (isset($_GET["Sort"])) {


                                //for assigning only valid Attributes
                                switch ($_GET["Sort"]) {
                                        case "Ascending":
                                        case "Descending":
                                        case "Serial":
                                        case "Time":
                                                $_SESSION["Sort"] = $_GET["Sort"];
                                }
                                //Shouldn't new sort start from beginning ?
                                $_SESSION["Offset"] = $Offset = 0;
                        }


                        //Should be before the query, just for making sure it's properly set
                        if (isset($_SESSION["Sort"])) {

                                $Sort = $_SESSION["Sort"] == "Ascending" ? "ORDER BY Word" : ($_SESSION["Sort"] == "Descending" ? "ORDER BY Word DESC" : "");
                        }

                        /**
                         * The Legend
                         */
                        $sql = "SELECT * FROM Words $Sort LIMIT $Limit OFFSET $Offset";
                        /**
                         * End of the Legend
                         */

                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) :

                                echo '<table class="table table-bordered table-striped table-dark table-hover">';
                                echo <<<STRING
                        <thead>
                                <tr>
                                <th scope="col" class="text-center">Serial</th>
                                <th scope="col" class="text-center">Word</th>
                                <th scope="col" class="text-center">Meaning</th>
                                <th scope="col" class="text-center">Translation</th>
                                <th scope="col" class="text-center">Saving Time</th>
                                </tr>
                        </thead>
                        <tbody>
                STRING;

                                while ($row = mysqli_fetch_assoc($result)) :
                        ?>

                                        <tr <?php echo 'id="' . $row['Word'] . '"'; ?>>

                                                <td scope="row"> <?php echo $row["Count"]; ?> </td>

                                                <th class="Word"> <?php echo ucfirst(strtolower($row["Word"])); ?> </th>

                                                <td class="Meaning">
                                                        <?php
                                                        if (empty($row["Meaning"])) {
                                                                echo '<a href="#" onclick="return define(\'' . $row['Word'] . '\')"> Define ' . strtolower($row["Word"]) . ' </a>';
                                                        } else {
                                                                echo $row["Meaning"];
                                                        }

                                                        ?>
                                                </td>

                                                <td class="Translation">
                                                        <div class="float-left">
                                                                <?php
                                                                if (empty($row["Bengali"])) {
                                                                        echo '<a href="#" onclick="return traducir(\'' . $row['Word'] . '\')"> Translate ' . ucfirst($row["Word"]) . ' </a>';
                                                                } else {
                                                                        echo $row["Bengali"];
                                                                }


                                                                ?>
                                                        </div>
                                                        <div class="text-success float-right">
                                                                <button class="btn">Edit</button>
                                                        </div>

                                                </td>

                                                <td>
                                                        <?php
                                                        echo $row["Save Time"];
                                                        ?>
                                                </td>
                                        </tr>
                                <?php
                                endwhile;


                                echo " </tbody>\n</table>";

                                ?>
                                <div class="d-flex justify-content-between">

                                        <div>
                                                <form method="get">
                                                        <button class="btn" name="Navigate" type="submit" value="Previous">
                                                                << Previous</button> <button class="btn" name="Navigate" type="submit" value="Next"> Next >>
                                                        </button>
                                                </form>
                                        </div>

                                        <div class="float-right">
                                                <form method="get">
                                                        <div class="form-group">
                                                                <select class="form-control" name="Sort" onchange="this.form.submit()">
                                                                        <option <?php if (!isset($_SESSION["Sort"])) echo "selected"; ?> disabled>Sort Word</option>
                                                                        <option <?php if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "Serial") echo "selected"; ?> value="Serial">Serial</option>
                                                                        <option <?php if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "Ascending") echo "selected"; ?> value="Ascending">Alphabetically Ascending</option>
                                                                        <option <?php if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "Descending") echo "selected"; ?> value="Descending">Alphabetically Descending</option>
                                                                        <option <?php if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "Time") echo "selected"; ?> value="Time">Save Time</option>
                                                                </select>
                                                        </div>
                                                </form>
                                        </div>

                                        <div class="float-right">
                                                <form method="get">
                                                        <div class="form-group">
                                                                <select class="form-control" name="Limit" onchange="this.form.submit()">
                                                                        <option <?php if (!isset($_SESSION["Limit"])) echo "selected"; ?> disabled>Set Display Limit</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "10") echo "selected"; ?> value="10">10</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "20") echo "selected"; ?> value="20">20</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "30") echo "selected"; ?> value="30">30</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "40") echo "selected"; ?> value="40">40</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "50") echo "selected"; ?> value="50">50</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "60") echo "selected"; ?> value="60">60</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "70") echo "selected"; ?> value="70">70</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "80") echo "selected"; ?> value="80">80</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "90") echo "selected"; ?> value="90">90</option>
                                                                        <option <?php if (isset($_SESSION["Limit"]) && $_SESSION["Limit"] == "100") echo "selected"; ?> value="100">100</option>
                                                                </select>
                                                        </div>
                                                </form>
                                        </div>
                                </div>
                        <?php


                                /**
                                 * Yeah, If the database query didn't bring any results
                                 */

                        else :
                                //Though I hate getting error messages, I don't hate showing it to others
                                echo <<<TAG
                <div class="container">
                        <div class="row">
                                <div id="Failed" class="col-12">
                                        <h1 id="Oops" class="text-danger text-center">Oops!<br>Either there's no data On database!<br>Or Something really bad happened!</h1>'
                                </div>
                        </div>
                </div>
                TAG;

                                //just for properly styling it;
                                echo <<<SCRIPT
                
                <script>
                        (function(){
                                var style = window.getComputedStyle(document.getElementById("Failed"), null);
                                var x = parseInt (style.getPropertyValue("height") );
                                document.getElementById("Oops").style.paddingTop = Math.floor( (window.innerHeight/2) - (x/2) ) +"px";
                        })();
                </script>

                SCRIPT;
                        endif;

                        mysqli_close($conn);
                        //each time open and close connecton? because it's not object oriented
                        //Though it doesn't affect that much anything
                        ?>
                </div>
        </div>
</body>


<script>
        // Here are some mocking functions as I couldn't integrate Google's translation API,
        // As they demand money for using their services
        // What would I do if I was with Google?

        function define(data) {
                var x = document.getElementById(data).getElementsByClassName("Meaning")[0];
                var dictionary = [
                        "দয়া করে গুগল অভিধান ব্যবহার করুন",
                        "Utilice el diccionario de Google en su lugar",
                        "Use Google Dictionary Instead",
                        "Add Google Dictionary",
                        "Google disctionary is easy to use",
                        "Google dictionary has this functionality",
                        "Why not give Dictionary a shot",
                        "agregar diccionario de google",
                        "El diccionario de Google tiene esta funcionalidad",
                        "El diccionario de Google es fácil de usar"
                ];
                var link = "<a target='_blank' href='https://chrome.google.com/webstore/detail/google-dictionary-by-goog/mgijmajocgfcbeboacabfgobmjgjcoja'>" + dictionary[Math.floor(Math.random() * 10)] + "</a>"
                x.innerHTML = link;
                return false;
        }

        function traducir(data) {

                var excuses = [
                        "Sorry!",
                        "No se pudo traducir el texto",
                        "Oops!", "lo siento",
                        "Meh!",
                        "Try Later",
                        "Perdon",
                        "intente nuevamente más tarde",
                        "Duh!",
                        "I'm Thinking",
                        "Gimme Some time",
                        "Can't do that now",
                        "Oh! sorry",
                        "Can't now",
                        "Wanna Hear a joke instead?",
                        "Ummm wait",
                        "You mean right now?",
                        "Only if was omniscient",
                        "Maap kor baap",
                        "Ei bar chere de!"
                ];

                var x = document.getElementById(data).getElementsByClassName("Translation")[0];
                x.innerHTML = excuses[Math.floor(Math.random() * 20)];
                return false;
        }
</script>

</html>
<!-- You want More? -->