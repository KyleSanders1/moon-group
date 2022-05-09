<?php
/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _| 

 Geschreven door: Michel Raeven
 © ZO Groep - 31-01-2022
*/

// Initialize the session
session_set_cookie_params(0, '/', 'zogroep.nl');
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
        <meta charset="UTF-8">
        <title>MOON Group portal</title>
        <script src="/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="/jquery/general.js"></script>
        <script type="text/javascript" src="/jquery/login.js"></script>
        <script src="https://kit.fontawesome.com/391251f29b.js" crossorigin="anonymous"></script>
        <link href="/css/moongroup.css" rel="stylesheet"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="images/icoon.ico" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <?php
  //CHECK IF THE USER IS LOGGED IN
    if(isset($_SESSION['User']['user_id'])){
      include "pages/main.php";
    } else {
      include "pages/login.php";
    }
  ?>
</html>
