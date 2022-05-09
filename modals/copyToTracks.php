<div class="modal fade" id="copyToTracks" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog" style="max-width:unset">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="copyModalLabel">Kopieer naar tracks</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">
            <div class="col-sm-3 mb-3">
            <div class="form-floating mb-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="Primary_artists">
                <label class="form-check-label">Primary artists</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="Featuring_artists">
                <label class="form-check-label">Featuring artists</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="Release_date">
                <label class="form-check-label">Release date</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="Original_release_date">
                <label class="form-check-label">Original release date</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="Main_genre">
                <label class="form-check-label">Main genre</label>
              </div>
            </div>
            </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Sluiten</button>
        <button type="button" name="save" class="btn btn-primary" onclick="CopyToTracks();"><i class="fas fa-pen"></i> Opslaan</button>
      </div>
    </div>
  </div>
</div>