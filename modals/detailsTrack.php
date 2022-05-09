<!-- Modal -->
<div class="modal fade" id="detailsTrackModal" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog" style="max-width:unset">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Details track</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php include "pages/details/detailsTrack.php";?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Sluiten</button>
        <button type="button" name="delete" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#detailsTrackModal" onclick="tracksTrackVerwijderen();"><i class="fas fa-trash" ></i> Verwijder</button>
        <button type="button" name="save" class="btn btn-primary" onclick="tracksTrackWijzigen($('#detailsTrackModal').find('DetailsTrack');$(this);"><i class="fas fa-pen"></i> Opslaan</button>
        <button type="button" name="copy" class="btn btn-primary" onclick="tracksTrackKopie();"><i class="fas fa-copy"></i> Kopieer</button>
      </div>
    </div>
  </div>
</div>