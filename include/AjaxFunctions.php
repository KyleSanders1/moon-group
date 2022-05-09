<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
$clsUser = new clsUser();
$clsAccount = new clsAccount();
$clsCsv = new clsCsv();
$clsAlbum = new clsAlbum();
$clsTrack = new clsTrack();
$clsBedrijf = new clsBedrijf();
$clsArtiest = new clsArtiest();
$clsComposer = new clsComposers();
$clsLyricist = new clsLyricists();
$clsLabel = new clsLabel();
$clsLine = new clsLine();

switch($_POST["func"]){
  //INLOGGEN
  case 'Login':
    $username = $_POST['username'];
    $password = $_POST['password'];
    echo $clsUser -> Login($username,$password);
    break;
  case 'Logout':
    session_destroy();
    break;

  //USERS
  case "GetUsers":
    echo json_encode($clsAccount -> GetUsers($_POST['bedrid']));
    break;
  case "GetUserDetails":
    echo json_encode($clsAccount -> GetUserDetails($_POST['account_id']));
    break;
  case "AccountToevoegen":
    echo $clsAccount -> AccountToevoegen($_POST['weergavenaam']);
    break;
  case "AccountOpslaan":
    echo $clsAccount -> AccountOpslaan(json_decode($_POST['data'],true));
    break;

  case "VerwijderAccount":
    echo $clsAccount -> VerwijderAccount(json_decode($_POST['data'],true));
    break;

  //ALBUMS
  case "GetAlbumDetails":
    echo json_encode($clsAlbum -> GetAlbumDetails($_POST['album_id']));
    break;
  case "getAlbumTable":
    echo $clsAlbum -> getAlbumTable($_POST['van'],$_POST['tot'],$_POST['bedr_id']);
    break;
  case "InsertDetailsAlbum":
    echo $clsAlbum -> InsertDetailsAlbum(json_decode($_POST['data'],true));
    break;
  case "GetNextUPC":
    echo GetSingleResult("SELECT Code From tbl_upc Where gebruikt = 0 LIMIT 1","Code");
    break;
  case "GetNextCatalogNr":
    echo $clsAlbum -> GetNextCatalogNr($_POST['bedrijfsid']);
    break;
  case "OpslaanAlbum":
    echo $clsAlbum -> OpslaanAlbum(json_decode($_POST['data'],true));
    break;
  case "VerwijderAlbums":
    echo $clsAlbum -> VerwijderAlbums(json_decode($_POST['albums']));
    break;
  case "CheckUPC":
    echo $clsAlbum -> CheckUPC($_POST['upc']);
    break;
  case "CheckCatNum":
    echo $clsAlbum -> CheckCatNum($_POST['catNum']);
    break;
  case "GetAlbumIdVanTotBedrijf":
    echo json_encode($clsAlbum -> GetAlbumIdVanTotBedrijf($_POST['van'],$_POST['tot'],$_POST['bedrId']));
    break;
  case "CheckLockedStatusAlbum":
    echo $clsAlbum -> CheckLockedStatusAlbum($_POST['album_id']);
    break;

  //TRACKS
  case "GetTrackRows":
    echo $clsTrack -> GetTrackRows($_POST["albumId"]);
    break;
  case "CopyTracks":
    echo $clsTrack -> CopyTracks(json_decode($_POST['data'],true), $_POST['aantal']);
    break;
  case "InsertDetailsTrack":
    echo $clsTrack -> InsertDetailsTrack(json_decode($_POST['data'],true));
    break;
  case "GetNextISRC":
    echo $clsTrack -> GetNextISRC($_POST['albumId']);
    break;
  case "GetTrackDetails":
    echo json_encode($clsTrack -> GetTrackDetails($_POST['track_id']));
    break;
  case "TrackWijzigen":
    echo $clsTrack -> TrackWijzigen(json_decode($_POST['data'],true));
    break;
  case "TrackVerwijderen":
    echo json_encode($clsTrack -> TrackVerwijderen($_POST['track_id']));
    break;
  case "CheckISRC":
    echo $clsTrack -> CheckISRC($_POST['isrc']);
    break;
  case "CheckLockedStatusTrack":
    echo $clsTrack -> CheckLockedStatusTrack($_POST['trackId']);
    break;
  case "Vertaling":
    echo $clsTrack -> Vertaling($_POST['Album_language'],$_POST['Track_language'],$_POST['Audio_language'],$_POST['albumId']);
    break;
  case "CheckUserRole":
    echo $clsTrack -> CheckUserRole();
    break;
  case "CopyToTracks":
    echo $clsTrack -> CopyToTracks(json_decode($_POST['data'],true),$_POST['Primary_artists'],$_POST['Featuring_artists'],$_POST['Release_date'],$_POST['Original_release_date'],$_POST['Main_genre'],$_POST['albumId']);
    break;
  case "AddComposerOnchangeArtist":
    echo json_encode($clsTrack ->AddComposerOnchangeArtist($_POST['artiestId']));
  break;

  // BEDRIJVEN
  case "OphalenDetailsBedrijf":
    echo json_encode($clsBedrijf -> OphalenDetailsBedrijf($_POST["bedrijf_id"]));
    break;

  case "OpslaanBedrijf":
    echo $clsBedrijf -> OpslaanBedrijf(json_decode($_POST['data'],true));
    break;

  case "BedrijfToevoegen":
    echo $clsBedrijf -> BedrijfToevoegen($_POST["weergavenaam"]);
    break;
  
  case "VerwijderBedrijf":
    echo $clsBedrijf -> VerwijderBedrijf(json_decode($_POST['data'],true));
    break;

  //CSV 
  case "GetExportRowsCSV":
    echo $clsCsv -> GetExportRowsCSV(json_decode($_POST['albums']),json_decode($_POST['albumdata']),json_decode($_POST['trackdata']),$_POST['bedrId']);
    break;
  case "InsertDataCsv":
    $clsCsv -> InsertDataCsv($_POST["data"], $_POST['bedrijfsid']);
    break;

  //ARTIESTEN
  case "OphalenDetailsArtiest":
    echo json_encode($clsArtiest -> OphalenDetailsArtiest($_POST["artiest_id"]));
    break;

  case "OpslaanArtiest":
    echo $clsArtiest -> OpslaanArtiest(json_decode($_POST['data'],true));
    break;

  case "ArtiestToevoegen":
    echo $clsArtiest -> ArtiestToevoegen($_POST["naam"]);
    break;

  case "VerwijderArtiest":
    echo $clsArtiest -> VerwijderArtiest(json_decode($_POST['data'],true));
    break;
  
  case "OphalenArtiesten":
    echo json_encode(ResultToArray("SELECT artiest_id, omschrijving FROM tbl_artiest WHERE verwijderd = 0;"));
    break;
    
  //COMPOSERS
  case "OphalenDetailsComposer":
    echo json_encode($clsComposer -> OphalenDetailsComposer($_POST["composer_id"]));
    break;

  case "ComposerOpslaan":
    echo $clsComposer -> ComposerOpslaan(json_decode($_POST['data'],true));
    break;

  case "ComposerToevoegen":
    echo $clsComposer -> ComposerToevoegen($_POST["naam"]);
    break;

  case "VerwijderComposer":
    echo $clsComposer -> VerwijderComposer(json_decode($_POST['data'],true));
    break;
  case "OphalenComposers":
    echo json_encode(ResultToArray("SELECT composer_id, omschrijving FROM tbl_composers WHERE verwijderd = 0;"));
    break;

  //LYRICISTS
  case "OphalenDetailsLyricist":
    echo json_encode($clsLyricist -> OphalenDetailsLyricist($_POST["lyricists_id"]));
    break;

  case "LyricistOpslaan":
    echo $clsLyricist -> LyricistOpslaan(json_decode($_POST['data'],true));
    break;

  case "LyricistToevoegen":
    echo $clsLyricist -> LyricistToevoegen($_POST["naam"]);
    break;

  case "VerwijderLyricist":
    echo $clsLyricist -> VerwijderLyricist(json_decode($_POST['data'],true));
    break;
  case "OphalenLyricists":
    echo json_encode(ResultToArray("SELECT lyricists_id, omschrijving FROM tbl_lyricist WHERE verwijderd = 0;"));
    break;
  
  //LABEL
  case "LabelToevoegen":
    echo $clsLabel ->LabelToevoegen($_POST['naam'], $_POST['bedrid']);
    break;
  case "GetLabels":
    echo json_encode($clsLabel -> GetLabels($_POST['bedrid']));
    break;
  case "OphalenDetailsLabel":
    echo json_encode($clsLabel -> OphalenDetailsLabel($_POST['label_id']));
    break;
  case "VerwijderLabel":
    echo $clsLabel -> VerwijderLabel(json_decode($_POST['data'],true));
    break;
  case "OpslaanLabel":
    echo $clsLabel -> OpslaanLabel(json_decode($_POST['data'],true));
    break;
  case "OphalenLabels":
    echo json_encode(ResultToArray("SELECT label_id, omschrijving FROM tbl_label WHERE verwijderd = 0;"));
    break;

  //LINE
  case "LineToevoegen":
    echo $clsLine ->LineToevoegen($_POST['naam']);
    break;
  case "GetLines":
    echo json_encode($clsLine -> GetLines());
    break;
  case "OphalenDetailsLine":
    echo json_encode($clsLine -> OphalenDetailsLine($_POST['line_id']));
    break;
  case "VerwijderLine":
    echo $clsLine -> VerwijderLine(json_decode($_POST['data'],true));
    break;
  case "OpslaanLine":
    echo $clsLine -> OpslaanLine(json_decode($_POST['data'],true));
    break;
  case "OphalenLines":
    echo json_encode(ResultToArray("SELECT line_id, naam FROM tbl_line WHERE verwijderd = 0;"));
    break;
}
?>                   