<?php
/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _| 

 Geschreven door: Michel Raeven
 © ZO Groep - 04-03-2022
*/
$clsLine = new clsLine();

?>

<div id="lines">
  
    <label class="pointer mb-3" onclick="LineToevoegen();"><i class="fas fa-solid fa-plus"></i> Line toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="linelist">

        <?php
          $lines = $clsLine->GetLines();
          foreach ($lines as $l) {
          echo "<li class='listitem shadow' value='".$l['line_id']."'>".$l['naam']."</li>";
          }
        ?>

      </ul>
    </div>
    <div class="col-sm-8" id="data-lyricist" name="DetailsLine">

      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;" placeholder="Naam" required name="naam">
          <label for="omschrijving">Naam</label>
      </div>
      <button type="button" onclick="lineOpslaanLine();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="lineVerwijderLine();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>

    </div>
  </div>
</div>