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

$clsLyricists = new clsLyricists();
$lyricists = $clsLyricists -> GetLyricists();
$lyricistsoptions= '';
foreach ($lyricists as $l){$lyricistsoptions .="<option value='" . $l['lyricists_id'] . "'>" . $l['omschrijving'] . "</option>";}

$clsComposers = new clsComposers();
$composers = $clsComposers -> GetComposers();
$composersoptions= '';
foreach ($composers as $c){$composersoptions .="<option value='" . $c['composer_id'] . "'>" . $c['omschrijving'] . "</option>";}

$clsPublishers = new clsPublishers();
$publishers = $clsPublishers -> GetPublishers();
$publishersoptions= '';
foreach ($publishers as $p){$publishersoptions .="<option value='" . $p['publisher_id'] . "'>" . $p['omschrijving'] . "</option>";}

$clsArtiest = new clsArtiest();
$artiesten = $clsArtiest -> OphalenArtiesten();
$artiestenoptions= '';
foreach ($artiesten as $a){$artiestenoptions .="<option value='" . $a['artiest_id'] . "'>" . $a['omschrijving'] . "</option>";}

?>
<div class="row" name="DetailsTrack" class="mb-3">

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Track title" name="Track_title">
        <label>Track title</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Track version" name="Track_version">
        <label>Track version</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" onkeyup="CheckISRC();" required class="form-control" placeholder="ISRC" name="ISRC">
        <label>ISRC</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectPrimaryArtists">
        <select class="form-select multiple" name="Track_primary_artists" multiple="multiple">
        </select>
        <label>Track primary artists</label>
        
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectFeaturingArtists">
        <select class="form-select multiple" name="Track_featuring_artists" multiple="multiple">
        </select>
        <label>Track featuring artists</label>
        
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Volume number" name="Volume_number" value="1">
        <label>Volume number</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Track_main_genre">
          <?php 
          $genres = ResultToArray("SELECT * FROM tbl_genre");
          foreach ($genres as $g){
            echo "<option value='" . $g['genre_id'] . "'>" . $g['omschrijving'] . "</option>";
          }?>
        </select>
        <label>Track main genre</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Track_main_subgenre">
          <?php 
          $genres = ResultToArray("SELECT * FROM tbl_genre");
          foreach ($genres as $g){
            echo "<option value='" . $g['genre_id'] . "'>" . $g['omschrijving'] . "</option>";
          }?>
        </select>
        <label>Track main subgenre</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Track_language">
          <?php 
          $language = ResultToArray("SELECT * FROM tbl_language");
          foreach ($language as $l){
            if ($l['language_id'] == 250) {
            echo "<option value='" . $l['language_id'] . "' selected>" . $l['2_letter_code'] . "</option>";
            } else {
              echo "<option value='" . $l['language_id'] . "'>" . $l['2_letter_code'] . "</option>";
            }
          }?>
        </select>
        <label>Track language</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Audio_language">
          <?php 
          $language = ResultToArray("SELECT * FROM tbl_language");
          foreach ($language as $l){
            if ($l['language_id'] == 250) {
              echo "<option value='" . $l['language_id'] . "' selected>" . $l['2_letter_code'] . "</option>";
              } else {
                echo "<option value='" . $l['language_id'] . "'>" . $l['2_letter_code'] . "</option>";
              }
          }?>
        </select>
        <label>Audio language</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectContributingArtists">
        <select class="form-select multiple" name="Contributing_artists" multiple="multiple">
        </select>
        <label>Contributing artists</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectComposers">
        <select class="form-select multiple" name="Composers" multiple="multiple">
        </select>
        <label>Composers</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectLyricists">
        <select class="form-select multiple" name="Lyricists" multiple="multiple">
        </select>
        <label>Lyricists</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectRemixers">
        <select class="form-select multiple" name="Remixers" multiple="multiple">
        </select>
        <label>Remixers</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectPerformers">
        <select class="form-select multiple" name="Performers" multiple="multiple">
        </select>
        <label>Performers</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectWriters">
        <select class="form-select multiple" name="Writers" multiple="multiple">
        </select>
        <label>Writers</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect" name="multiselectPublishers">
        <select class="form-select multiple" name="Publishers" multiple="multiple">
        </select>
        <label>Publishers</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Track_catalog_tier">
          <?php 
          $tier = ResultToArray("SELECT * FROM tbl_catalog_tier");
          foreach ($tier as $t){
            echo "<option value='" . $t['cat_tier_id'] . "'>" . $t['omschrijving'] . "</option>";
          }?>
        </select>
        <label>Catalog tier</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="number" autocomplete="off" required value=0 min=0 class="form-control" placeholder="Track sequence" name="Track_sequence" value="1">
        <label>Track sequence</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Original file name" name="Original_file_name">
        <label>Original file name</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="number" autocomplete="off" required class="form-control" placeholder="Recording year" min=2000 max=29999 name="Track_recording_year" value="<?php echo date("Y")?>">
        <label>Recording year</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="date" autocomplete="off" required class="form-control" placeholder="Release date" value = "<?php echo date("Y-m-d");?>" name="Release_date">
        <label>Release date</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="date" autocomplete="off" required class="form-control" placeholder="Original release date" value = "<?php echo date("Y-m-d");?>" name="Original_release_date">
        <label>Original release date</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="Available_separately">
        <label class="form-check-label">Available separately</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="Track_parental_advisory">
        <label class="form-check-label">Parental advisory</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="Locked">
        <label class="form-check-label">Locked</label>
      </div>
    </div>

  </div>