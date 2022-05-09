<?php

/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _|

 Geschreven door: Michel Raeven
 © ZO Groep - 20-02-2022
*/

// Initialize the session
session_start();

$clsLabel = new clsLabel();

?>
<div id="toevoegenAlbum">
  
  <label class="pointer mb-3" onclick="$('#toevoegenAlbum').hide();$('#home').show(600);"><i class="fas fa-arrow-left" style="color:black"></i> Terug</label>

  <?php include "pages/details/detailsAlbum.php";?>
  <button type="button" onclick="AlbumToevoegen();" class="btn btn-primary"><i class="fas fa-solid fa-plus"></i> Album toevoegen</button>

</div>