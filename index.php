<?php
include 'controller/include.php';
if(isset($_SESSION["logged_in"]))
{
    header("Location: main.php");
}
else
{
    header("Location: auth.php");
}