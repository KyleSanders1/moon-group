<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
  $clsArtiest = new clsArtiest();
  $artiesten = $clsArtiest -> OphalenArtiesten();
   $artiestenoptions= '';
  foreach ($artiesten as $a){$artiestenoptions .="<option value='" . $a['artiest_id'] . "'>" . $a['omschrijving'] . "</option>";}
?>

<div class="row" name="DetailsAlbum" class="mb-3">

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Label">
        </select>
        <label>Label</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Album title" name="Album_title">
        <label>Album title</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Album version" name="Album_version">
        <label>Album version</label>
      </div>
    </div>
    
    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" onkeyup="CheckUPC();" required class="form-control" placeholder="UPC" name="UPC" >
        <label>UPC</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" onkeyup="CheckCatNum();" required class="form-control" placeholder="Catalog number" name="Catalog_number">
        <label>Catalog number</label>
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
      <div class="form-floating mb-1 dashboardcode-bsmultiselect">
        <select class="form-select multiple" name="Album_primary_artists" multiple="multiple">
        </select>
        <label>Primary artists</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1 dashboardcode-bsmultiselect">
        <select class="form-select multiple" name="Album_featuring_artists" multiple="multiple">
        </select>
        <label>Featuring artists</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Main_genre">
          <?php 
          $genres = ResultToArray("SELECT * FROM tbl_genre");
          foreach ($genres as $g){
            echo "<option value='" . $g['genre_id'] . "'>" . $g['omschrijving'] . "</option>";
          }?>
        </select>
        <label>Main genre</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="number" autocomplete="off" required class="form-control" placeholder="Cline year" min=2000 max=29999 name="Cline_year" value="<?php echo date("Y")?>">
        <label>Cline year</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Cline_name">
        </select>
        <label>Cline name</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="number" autocomplete="off" required class="form-control" placeholder="Pline year" min=2000 max=29999 name="Pline_year" value="<?php echo date("Y")?>">
        <label>Pline year</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Pline_name">
        </select>
        <label>Pline name</label>
      </div>
    </div>


    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="number" autocomplete="off" required class="form-control" placeholder="Recording year" min=2000 max=29999 name="Recording_year" value="<?php echo date("Y")?>">
        <label>Recording year</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Album_format">
          <?php 
          $format = ResultToArray("SELECT * FROM tbl_format");
          foreach ($format as $f){
            if ($f['format_id'] == 3) {
            echo "<option value='" . $f['format_id'] . "' selected>" . $f['description'] . "</option>";
          } else {
            echo "<option value='" . $f['format_id'] . "'>" . $f['description'] . "</option>";
          }
        }
          ?>
        </select>
        <label>Album format</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <input type="text" autocomplete="off" required class="form-control" placeholder="Number of volumes" name="Number_of_volumes" value="1">
        <label>Number of volumes</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Language">
          <?php 
          $language = ResultToArray("SELECT * FROM tbl_language");
          foreach ($language as $l){
            if($l['language_id'] == 250) {
              echo "<option value='" . $l['language_id'] . "' selected>" . $l['2_letter_code'] . "</option>";
            } else {
              echo "<option value='" . $l['language_id'] . "' >" . $l['2_letter_code'] . "</option>";
            }
          }
          ?>
        </select>
        <label>Language</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-floating mb-1">
        <select class="form-select" name="Catalog_tier">
          <?php 
          $tier = ResultToArray("SELECT * FROM tbl_catalog_tier");
          foreach ($tier as $t){
            if ($t['cat_tier_id'] == 3){
            echo "<option value='" . $t['cat_tier_id'] . "' selected>" . $t['omschrijving'] . "</option>";
            } else {
              echo "<option value='" . $t['cat_tier_id'] . "'>" . $t['omschrijving'] . "</option>";
            }
          }?>
        </select>
        <label>Catalog tier</label>
      </div>
    </div>

    <div class="col-sm-3 mb-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="Parental_advisory">
        <label class="form-check-label">Parental advisory</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="Locked">
        <label class="form-check-label">Locked</label>
      </div>
    </div>
    
  </div>