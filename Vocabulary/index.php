<?php
//First thing first
session_start();

/**
 * update the variables below with your credentials
 * or just send me your (probably transection) credentials at:
 * Name : Yasir Arfat
 * Email: mohammadyasirarfatchowdhury@gmail.com
 * Visit: https://yasirarfat.com
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vocabulary";


// If POSTED
if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        //Serial is AutoIncrement primary Key
        //If Sent with Serial then it's update request
        //Otherwise New Insert request
        if(isset($_POST['Serial']) && $_POST['Serial'] != ""){

                $Serial = $_POST['Serial'];

                if(isset($_POST['Word']) && $_POST['Word'] != ""){
                        $Word = $_POST['Word'];
                        $sql = "UPDATE Words SET Word='$Word' WHERE Serial='$Serial'";
                        mysqli_query($conn, $sql);
                }
                if(isset($_POST['Definition']) && $_POST['Definition'] != ""){
                        $Definition = $_POST['Definition'];
                        $sql = "UPDATE Words SET Definition='$Definition' WHERE Serial='$Serial'";
                        mysqli_query($conn, $sql);
                }
                if(isset($_POST['Translation']) && $_POST['Translation'] != ""){
                        $Translation = $_POST['Translation'];
                        $sql = "UPDATE Words SET Translation='$Translation' WHERE Serial='$Serial'";
                        mysqli_query($conn, $sql);
                }
                
        }
        else if (isset($_POST['Word']) && $_POST['Word'] != "") {

                //Definition and Translation are Nullable
                $Word = $_POST['Word'];
                $Definition = $_POST['Definition'];
                $Translation = $_POST['Translation'];

                $Word = filter_var($Word, FILTER_SANITIZE_STRING);
                $Definition = filter_var($Definition, FILTER_SANITIZE_STRING);
                $Translation = filter_var($Translation, FILTER_SANITIZE_STRING);

                /**
                 * english alphabetic characters aren't filtered
                 * Could be achieved by
                 * filter_var($Word, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                 * or,
                 * preg_replace("/[^a-zA-Z]/", "", $Word);
                 */

                /**
                 * Most of the time I use google dictionary to find the meaning of words
                 * there-> · <-is used for seperating syllables, so.. 
                 */
                $Word = preg_replace("/[·]/", "", $Word);

                $Word = ucfirst(strtolower($Word)); 

                if (!$conn) {
                        //[the chrome extension dosn't care for error responses]
                        die("Connection failed: " . mysqli_connect_error());
                }
                /**
                 * We are now inserting just the word
                 * one could try retriving the definition and
                 * maybe the translation via APIs
                 */

                $sql = "INSERT INTO Words (Word, Definition, Translation) VALUES ('$Word','$Definition','$Translation')";

                if (mysqli_query($conn, $sql)) {
                        echo "New record created successfully";
                } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
        }
        mysqli_close($conn);
}
 /**
 * Done with POST Requests
 * Get Request receives frontend
 * Retrive request gets json data
 */
else if( isset($_GET["Navigate"]) || isset($_GET["Limit"]) || isset($_GET["Sort"]) || isset($_GET["Content"])) {
        include 'retrive.php';      
}
else{
        include_once 'frontend.php';
}
