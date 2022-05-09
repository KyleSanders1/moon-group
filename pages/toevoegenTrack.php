<?php

/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _|

 Geschreven door: Michel Raeven
 © ZO Groep - 22-02-2022
*/

// Initialize the session
session_start();

?>
<div id="toevoegenTrack">
  
  <label class="pointer mb-3" onclick="$('#toevoegenTrack').hide();$('#home').show(600);"><i class="fas fa-arrow-left"></i> Terug</label>
 
  <?php include "pages/details/detailsTrack.php";?>
  <button type="button" onclick="trackToevoegen();" class="btn btn-primary"><i class="fas fa-solid fa-plus"></i> Track toevoegen</button>
</div>