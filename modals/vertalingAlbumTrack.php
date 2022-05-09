<div class="modal fade" id="vertalingAlbumTrack" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog" style="max-width:unset">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vertalingModalLabel">Vertaling</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">
            <div class="col-sm-3 mb-3">
              <div class="form-floating mb-1">
                <select class="form-select" name="Album_language">
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
                <label>Album language</label>
              </div>
              <div class="form-floating mb-1">
                <select class="form-select" name="Track_language">
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
                <label>Track language</label>
              </div>
            
              <div class="form-floating mb-1">
                <select class="form-select" name="Audio_language">
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
                <label>Audio language</label>
              </div>
                </div>
                </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Sluiten</button>
        <button type="button" name="save" class="btn btn-primary" onclick="Vertaling();"><i class="fas fa-pen"></i> Opslaan</button>
      </div>
    </div>
  </div>
</div>