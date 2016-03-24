<?php
require_once 'controller/include.php';
if(!isset($_SESSION["logged_in"]))
{
    header("Location: auth.php");
}
include 'view/header.php';

include 'view/main.php';
include 'view/footer.php';