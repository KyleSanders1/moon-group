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
<div id="TotalTable">
  <div name="albums" class="mb-3">

  </div>
  <div name="tracks" class="mb-3">
    <div name="DetailsAlbum" class="mb-3">
      <label class="pointer" onclick="tabellenTerugNaarAlbum()"><i class="fas fa-arrow-left"></i> Naar albums</label>
      <div class="accordion accordion-flush">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" >
          Details album
          </button>
        </h2>
        <div class="accordion-collapse collapse" aria-labelledby="flush-headingOne" name="tabeltracks" >
          <button type="button" class="btn btn-primary" onclick="albumsOpslaanWijziging($(this).siblings('div[name=DetailsAlbum]'));"><i class="fas fa-pen"></i> Wijzigingen opslaan</button>
          <button type="button" name="copy" class="btn btn-primary" onclick="albumsAlbumKopie();"><i class="fas fa-copy"></i> Kopieer</button>
          <button type="button" name="translate" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vertalingAlbumTrack"><i class="fas fa-language"></i> Vertaling</button>
          <button type="button" name="copyToTracks" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#copyToTracks"><i class="fas fa-copy"></i> Kopieer naar tracks</button>

        </div>
      </div>
    </div>
    <div name="tracktabel" class="mb-3">
    </div>
  </div>
  <div name="ExportTable" class="mb-3">
</div>
</div>