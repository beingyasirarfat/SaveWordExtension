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

/**
 * Don't freakin touch the code below
 * Unless you know how things work
 */

if (isset($_POST['Word']) && $_POST['Word'] != "") {

        $Txt = $_POST['Word'];
        $Meaning = $_POST['Meaning'];
        $Bengali = $_POST['Bengali'];
        /**
         * if it was sent with unstripped HTML's in some weird way
         * The mystery of which Scientists couldn't solve yet.
         */
        $Txt = filter_var($Txt, FILTER_SANITIZE_STRING);
        $Meaning = filter_var($Meaning, FILTER_SANITIZE_STRING);
        $Bengali = filter_var($Bengali, FILTER_SANITIZE_STRING);

        /**
         * Didn't filter Out non english alphabetic characters
         * Which sometimes filters out non English alphabet
         * Could be used if wanted with either of these
         * filter_var($Txt, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
         * or,
         * preg_replace("/[^a-zA-Z]/", "", $Txt);
         */

        /**
         * Most of the time I use google dictionary to find the meaning of words
         * there-> · <-is used for seperating syllables
         * the next line removes the character if it was sent with that char. 
         */

        $Txt = preg_replace("/[·]/", "", $Txt);


        // Create connection for God's sake
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        // Check connection which sometime helps
        if (!$conn) {

                //the chrome extension dosn't care for responses tho
                die("Connection failed: " . mysqli_connect_error());
        }


        /**
         * We are now inserting just the word
         * one could try retriving the definition and
         * maybe the translation via php and store it
         * alongside the word in the database
         */
        $sql = "INSERT INTO Words (Word, Definition, Translation) VALUES ('$Txt','$Meaning','$Bengali')";

        if (mysqli_query($conn, $sql)) {
                //Not thread safe but works every time
                echo "New record created successfully";
        } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
        /**
         * If you are only posting the word you don't need to keep the connection
         * or visit the admin(I'm serious!) panel.
         * If it's get request it includes the page in the line below
         */
} else {
        include_once("unindex.php");
}
