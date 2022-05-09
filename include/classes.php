<?php

/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _|

 Geschreven door: Michel Raeven
 © ZO Groep - 16-02-2022
*/

class Album{
  //properties
   public $Album_title, $Label, $Album_version, $UPC, $Catalog_number, $Primary_artists, $Featuring_artists, $Release_date, $Main_genre, $CLine_year, $CLine_name, $PLine_year, $PLine_name, $Parental_advisory,
  $Album_format, $Number_of_volumes, $Territories, $Excluded_territories, $Language_Metadata, $Catalog_tier,$Original_release_date ,$tracks, $Recording_year;


  function __construct() {
    $this->Primary_artists = array();
    $this->Featuring_artists = array();
    $this->tracks = array();
    $this->Territories = array();
    $this->Excluded_territories = array();
  }
 
  function fromCSV($data){
    $db=ConnectDb();

     $albumitems = array('Album_title', 'Label', 'Album_version', 'UPC', 'Catalog_number', 'Release_date', 'Main_genre', 'CLine_year', 'CLine_name', 'PLine_year', 'PLine_name', 'Parental_advisory',
    'Album_format', 'Number_of_volumes', 'Catalog_tier', 'Original_release_date' , 'Recording_year');

    foreach ($albumitems as $a){
      $val = $db->real_escape_string($data[str_replace("_"," ",$a)]);
      $this->$a = $val;
    } 

    //$this->Album_title = $data['Album title'];
    $this->Primary_artists =  explode("|",$db->real_escape_string($data['Primary artists']));
    $this->Featuring_artists =  explode("|",$db->real_escape_string($data['Featuring artists']));
    $this->Territories = explode("|",$db->real_escape_string($data['Territories']));
    $this->Excluded_territories = explode("|",$db->real_escape_string($data['Excluded territories']));
    $this->Language_Metadata = $db->real_escape_string($data['Language (Metadata)']);
    $this->Original_release_date = $data['oreleasedate'];
    print_r ($this->Primaty_artists);

  }

  function GetUPC(){
    return $this->UPC;
  }

  function GetAlbumTitle(){
    return $this->Album_title;
  }

  function addTrack($track){
    array_push($this->tracks,$track);
  }
}

class Track{
  //properties
  public $Track_title, $Track_version, $ISRC, $Track_primary_artists, $Track_featuring_artists, $Volume_number, $Track_main_genre, $Track_main_subgenre, $Track_language_Metadata, $Audio_language, $Track_alternate_genre, 
  $Track_alternate_subgenre, $Available_separately, $Track_parental_advisory, $Contributing_artists, $Composers, $Lyricists, $Lyrics, $Remixers, $Performers, $Publishers, $Track_equence, $Track_catalog_tier, 
  $Original_file_name, $Original_release_date, $Track_recording_year, $Writers,$Producers;

  function __construct() {
    $this->Track_primary_artists = array();
    $this->Track_featuring_artists = array();
    $this->Contributing_artists = array();
    $this->Composers = array();
    $this->Lyricists = array();
    $this->Remixers = array();
    $this->Performers = array();
    $this->Publishers = array();
    $this->Writers = array();
    $this->Producers =array();
  } 

  function fromCSV($data){
    $db=ConnectDb();
     $trackitems = array('Track_title', 'Track_version', 'ISRC', 'Volume_number', 'Track_main_genre', 'Track_main_subgenre', 
     'Audio_language', 'Track_alternate_genre',  'Track_alternate_subgenre', 'Available_separately', 'Track_parental_advisory', 'Lyrics', 
      'Track_sequence', 'Track_catalog_tier', 'Original_file_name', 'Track_recording_year','Original_release_date');

    foreach ($trackitems as $t){
      $val = $db->real_escape_string($data[str_replace("_"," ",$t)]);
      $this->$t = $val;
    } 

    $this->Original_release_date = $data['oreleasedate'];

    $this->Track_primary_artists =  explode("|",$db->real_escape_string($data['Track primary artists']));
    $this->Track_featuring_artists =  explode("|",$db->real_escape_string($data['Track featuring artists']));
    $this->Contributing_artists = explode("|",$db->real_escape_string($data['Contributing artists']));
    $this->Composers = explode("|",$db->real_escape_string($data['Composers']));
    $this->Lyricists = explode("|",$db->real_escape_string($data['Lyricists']));
    $this->Remixers = explode("|",$db->real_escape_string($data['Remixers']));
    $this->Performers = explode("|",$db->real_escape_string($data['Performers']));
    $this->Publishers = explode("|",$db->real_escape_string($data['Publishers']));
    $this->Writers = explode("|",$db->real_escape_string($data['Writers']));
    $this->Producers = explode("|",$db->real_escape_string($data['Producers']));
    $this->Track_language_Metadata = $db->real_escape_string($data["Track language (Metadata)"]);
  }
}
?>