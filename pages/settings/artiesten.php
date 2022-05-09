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

$clsArtiest = new clsArtiest();
?>
<div id="artiesten">
  <label class="pointer mb-3" onclick="artiestenArtiestToevoegen();"><i class="fas fa-solid fa-plus"></i> Artiest toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="artiestenlist">
      <?php 
        $artiesten = $clsArtiest->OphalenArtiesten();
        foreach ($artiesten as $a) {
          echo "<li class='listitem shadow' value='".$a['artiest_id']."'>".$a['omschrijving']."</li>";
        }
      ?> 
      </ul> </br>
    </div>
 
    
    <div class="col-sm-8" name="DetailsArtiest">
      <div class="row">
        <div class="col-sm-4 mb-3">
          <div class="form-floating mb-1">
            <input type="text" autocomplete="off" class="form-control" placeholder="Naam" name="omschrijving">
            <label for="omschrijving">Naam</label>
          </div>       
      <label for="Composer">Composers</label>
      <div class="form-floating mb-1">
        <select class="selectpicker multi" multiple data-selected-text-format="count > 3" data-live-search="true" name="Composer">
          <?php 
          $composers = ResultToArray("SELECT * FROM tbl_composers");
          foreach ($composers as $c){
            echo "<option value='" . $c['composer_id'] . "'>" . $c['omschrijving'] . "</option>";
          }?>
        </select>
        
      </div>
        </div>
        </div>
      <button type="button" onclick="artiestenOpslaanArtiest();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="artiestenVerwijderArtiest();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>
    </div>
  </div>
</div>