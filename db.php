<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "school_enquiry_db"
);

if (!$conn) {
    die("Connection Failed");
}

?>