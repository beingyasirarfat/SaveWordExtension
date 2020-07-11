<?php
if(session_id() == '' || !isset($_SESSION)) session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vocabulary";
//Word limit per page + How many words to skip + Sorting attribute
$Limit =  $_SESSION["Limit"] ?? 10;
$Offset = $_SESSION["Offset"] ?? 0;
$Sort =  $_SESSION["Sort"] ?? "";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) die("Connection failed: " . mysqli_connect_error());

/**
 * Understanding and manipulating request
 * before database query
 */

if (isset($_GET["Navigate"])) {

        if ($_GET["Navigate"] == "Next") {
                //if Next request index is more than database index just skip increasing the offset pointer
                $index = mysqli_query($conn, "SELECT COUNT(Serial) AS Serial FROM Words");
                $index = mysqli_fetch_assoc($index);

                if ($index["Serial"] && ($Offset + $Limit < $index["Serial"])) {
                        $Offset += $Limit;
                        $_SESSION["Offset"] = $Offset;
                }

        } elseif ($_GET["Navigate"] == "Previous") {
                //skip if request's beyond zero
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
                $_SESSION["Limit"] = $Limit = $_GET["Limit"] ?? 10;
        }
}

if (isset($_GET["Sort"])) {
        //for assigning only valid Attributes
        switch ($_GET["Sort"]) {
                case "Ascending":
                        $_SESSION["Sort"] = $_GET["Sort"];
                        $Sort =  "ORDER BY Word";
                        break;

                case "Descending":
                        $_SESSION["Sort"] = $_GET["Sort"];
                        $Sort =  "ORDER BY Word DESC";
                        break;

                case "Serial":
                case "Time":
                        $_SESSION["Sort"] = $_GET["Sort"];
                        $Sort =  "ORDER BY Serial DESC";
                        break;

                case "SerialDesc":
                case "TimeDesc":
                        $_SESSION["Sort"] = $_GET["Sort"];
                        $Sort =  "ORDER BY Serial DESC";
                        break;
                        
                default: $Sort = "";
        }
        
        //Should new sort start from beginning ?
        $_SESSION["Offset"] = $Offset = 0;
}

/**
 * The Legend
 */
$sql = "SELECT * FROM Words $Sort LIMIT $Limit OFFSET $Offset";
/**
 * End of the Legend
 */

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

        $rows = array();

        while ($r = mysqli_fetch_assoc($result)) {
                $rows[] = $r;
        }
        echo json_encode($rows);
}

mysqli_close($conn);
