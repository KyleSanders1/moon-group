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
?>

<div id="labels">
  
    <label class="pointer mb-3" onclick="LabelToevoegen();"><i class="fas fa-solid fa-plus"></i> Label toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="labellist">
      </ul>
    </div>
    <div class="col-sm-8" id="data-lyricist" name="DetailsLabel">

      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;" placeholder="Naam" required name="omschrijving">
          <label for="omschrijving">Naam</label>
      </div>
      <button type="button" onclick="labelOpslaanLabel();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="labelVerwijderLabel();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>

    </div>
  </div>
</div>