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

<div id="composers">
  
    <label class="pointer mb-3" onclick="ComposerToevoegen();"><i class="fas fa-solid fa-plus"></i> Composer toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="composerlist">
      <?php 
        $clsComposers = new clsComposers();
        $composers = $clsComposers->displayDataComposer();
        foreach ($composers as $c) {
            echo "<li class='listitem shadow' name='".$c['composer_id']."'>".$c['omschrijving']."</li>";
        }
      ?> 
      </ul>
    </div>
    <div class="col-sm-8" id="data-composer" name="DetailsComposer">

      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;" placeholder="Naam" required name="omschrijving">
          <label for="omschrijving">Naam</label>
      </div>
      <button type="button" onclick="OpslaanComposer();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="ComposerVerwijderen();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>
    </div>
  </div>
</div>
