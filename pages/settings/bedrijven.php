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

$clsBedrijf = new clsBedrijf();
?>
<div id="bedrijven">
  <label class="pointer mb-3" onclick="bedrijvenBedrijfToevoegen();"><i class="fas fa-solid fa-plus"></i> Bedrijf toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="bedrijvenlist">
      <?php 
        $bedrijven = $clsBedrijf->OphalenBedrijven();
        foreach ($bedrijven as $b) {
            echo "<li class='listitem shadow' value='".$b['bedrijf_id']."'>".$b['weergavenaam']."</li>";
        }
      ?> 
      </ul>
    </div>
    <div class="col-sm-8" name="DetailsBedrijf">
      <div class="row">
        <div class="col-sm-4 mb-3">
          <div class="form-floating mb-1">
            <input type="text" autocomplete="off" class="form-control" placeholder="Weergavenaam" name="weergavenaam">
            <label >Weergavenaam</label>
          </div>
          <div class="form-floating mb-1">
            <input type="text" autocomplete="off" class="form-control"  placeholder="Catalog voorvoegsel" name="Catalog_voorvoegsel">
            <label>Catalog voorvoegsel</label>
          </div>
          <div class="form-floating mb-1">
            <input type="text" autocomplete="off" class="form-control"  placeholder="ISRC voorvoegsel" required name="ISRC_voorvoegsel">
            <label>ISRC voorvoegsel</label>
          </div>
        </div>
      </div>
      
      <button type="button" onclick="bedrijvenOpslaanBedrijf();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="bedrijvenVerwijderBedrijf();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>
    </div>
  </div>
</div>