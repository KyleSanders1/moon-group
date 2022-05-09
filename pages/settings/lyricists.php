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
?>

<div id="lyricists">
  
    <label class="pointer mb-3" onclick="LyricistToevoegen();"><i class="fas fa-solid fa-plus"></i> Lyricist toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="lyricistlist">
      <?php 
    $clsLyricist = new clsLyricists();
    $lyricists = $clsLyricist->displayDataLyricist();
    foreach ($lyricists as $l) {
        echo "<li class='listitem shadow' name='".$l['lyricists_id']."'>".$l['omschrijving']."</li>";
    }
    ?> 
      </ul>
    </div>
    <div class="col-sm-8" id="data-lyricist" name="DetailsLyricist">

      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;" placeholder="Naam" required name="omschrijving">
          <label for="omschrijving">Naam</label>
      </div>
      <button type="button" onclick="OpslaanLyricist();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="LyricistVerwijderen();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>
    </div>
  </div>
</div>