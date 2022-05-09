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

<div id="selectie" style="line-height:1;" class="row mb-3">
  <input type="file" id="csvfile" style="display:none;" onchange="GetTracksCsv();" />
    
  <div class="col-sm-2">
    <div class="input-group mb-3">
      <span class="input-group-text">Van </span>
      <input type="date" class="form-control" onchange="LadenTabelAlbums();" style="line-height:1;" value="<?php echo date("Y-m-d",strtotime("-20 year")); ?>" name="van">
    </div>
  </div>
  <div class="col-sm-2">
    <div class="input-group mb-3">
      <span class="input-group-text">Tot </span>
      <input type="date" class="form-control" onchange="LadenTabelAlbums();"  style="line-height:1;" value="<?php echo date("Y-m-d",strtotime("+10 year")); ?>" name="tot">
    </div>
  </div>
  <div class="col-sm-8">
  <label name="albumToevoegen" class="control-label pointer fright p-3 shadow"  onclick="$('#home').hide();$('#toevoegenAlbum').show(600);albumsFillFieldsAlbum();"><i class="fas fa-solid fa-plus"></i> Album Toevoegen </label> 
  <label name="trackToevoegen" class="control-label pointer fright p-3 shadow" onclick="$('#home').hide();$('#toevoegenTrack').show(600);VullenISRC();" style="display:none;"><i class="fas fa-solid fa-plus"></i> Track Toevoegen </label> 
  <label id="Import" class="control-label pointer fright p-3 shadow" for="filebutton"  onclick="$('#csvfile').click();"><i class="fas fa-solid fa-file-import" ></i> Import </label> 
  <label id="Export" class="control-label pointer fright p-3 shadow" for="filebutton"  onclick="ExportSelectie(); "><i class="fas fa-solid fa-file-csv" ></i> Export </label> 
  <label id="Delete" class="control-label pointer fright p-3 shadow" for=""  onclick="VerwijderAlbums();"><i class="fas fa-solid fa-trash" ></i> Verwijder </label> 


</div>
</div>


