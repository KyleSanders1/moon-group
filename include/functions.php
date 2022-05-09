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

// Initialize the session
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/config.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/PHPMailer/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/PHPMailer/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/PHPMailer/SMTP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/classes.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class clsEncryption{

  const SESS_CIPHER = 'aes-128-cbc';
  const key = "20220131XUser1K";

  function encrypt($string) {
    // For an easy iv, MD5 the salt again.
    $iv = $this->_getIv();
    // Encrypt the string.
    $ciphertext = openssl_encrypt($string, self::SESS_CIPHER, self::key, $options=OPENSSL_RAW_DATA, $iv);
    // Base 64 encode the string.
    return str_replace("+","%2B",base64_encode($ciphertext));

  }

  public function decrypt($string) {
    // Get the iv.
    $string = str_replace("%2B","+",$string);
    $iv = $this->_getIv();
    // Decode the encrypted session ID from base 64.
    $decoded = base64_decode($string, TRUE);
    // Decrypt the string.
    $decryptedString = openssl_decrypt($decoded, self::SESS_CIPHER,  self::key, $options=OPENSSL_RAW_DATA, $iv);
    
    // Trim the whitespace from the end.
    return rtrim($decryptedString, '\0');
  }

  public function _getIv() {
    $ivlen = openssl_cipher_iv_length(self::SESS_CIPHER);
    return substr(md5(self::key), 0, $ivlen);
  }


}

class clsUser{
  function Login($username, $password){
    $username = trim($username);
    $password = trim($password);

    $clsEncryption = new clsEncryption;
    $encPassword = $clsEncryption->encrypt($password);
    
    // Prepare a select statement
    $sql = "SELECT * FROM tbl_users
      WHERE  username = '$username' AND password = '$encPassword' LIMIT 1";

    $user = ReturnFirstRow($sql);
    if(count($user) ==0) exit("Gebruikersnaam of wachtwoord onjuist.");

    if($user['locked'] == 1){
      $msg = "User is locked because of to many wrong attempts<br>
      Contact support@zogroep.nl to unlock your account.";
      return $msg;
    }
    $_SESSION['User'] = $user;  
    return;
  }
}

class clsAccount {
  // get account information
  function GetUserDetails($userid){
    return ReturnFirstRow("SELECT user_id, name, username, email FROM tbl_users WHERE user_id = $userid;");
  }
  // insert account 
  function AccountToevoegen($gebruikersnaam){
    $db = ConnectDb();
    $gebruikersnaam = $db->real_escape_string($gebruikersnaam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_users(name) VALUES('$gebruikersnaam')" ,$db);
  }

  //Ophalen alle accounts van een bedrijf
  function GetUsers($bedrid){
    return ResultToArray("SELECT u.user_id, u.name FROM tbl_users u 
                          INNER JOIN tbl_user_bedrijf b ON u.user_id = b.user_id 
                          WHERE u.verwijderd = 0 AND b.bedrijf_id = $bedrid;");
  }

  // shows data from account
  function displayDataAccount()
  {
    return ResultToArray("SELECT user_id, name, username, email FROM tbl_users where verwijderd = 0");
  }

  // updates an account
  function AccountOpslaan($data)
  {
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);
    
    $clsEncryption = new clsEncryption();
    if ($password != "") {
      $password = $clsEncryption->encrypt($password);
      return ExQuery("UPDATE tbl_users SET name = '$name', username = '$username', password = '$password', email = '$email' WHERE user_id = '$user_id'" ,$db);
    } else {
      
      return ExQuery("UPDATE tbl_users SET name = '$name', username = '$username', email = '$email' WHERE user_id = '$user_id'" ,$db);
    }
  }
 
  // deletes account
  function VerwijderAccount($data)
  {    
      $db = ConnectDb();
      foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

      return ExQuery("UPDATE tbl_users SET verwijderd = $verwijderd WHERE user_id = '$user_id'", $db);
    }
 
}

class clsAlbum {

  // insert data album               
  function InsertDetailsAlbum($data){  
    $db = ConnectDb();

    foreach($data as $k=>$val){
      $$k = $db->real_escape_string($val);
    }

    //print_r($data['albumId']);
    $db->close();
    $par = $Parental_advisory != "0" ? 0 : 1;
    ExQuery("UPDATE tbl_upc SET gebruikt = 1 WHERE Code = $UPC");

    $albumId = ReturnLastInsertedId("INSERT INTO tbl_albums(Label, ALbum_title, Album_version, UPC, Catalog_number, Release_date, Main_genre, Cline_year, Cline_name,
    Pline_year, Pline_name, Parental_advisory, Recording_year, Album_format, Number_of_volumes, Language, Catalog_tier, Original_release_date, Bedrijf_id) 
    VALUES ('$Label', '$Album_title', '$Album_version', '$UPC', '$Catalog_number', '$Release_date', '$Main_genre', '$Cline_year', '$Cline_name', '$Pline_year', '$Pline_name',
    '$par', '$Recording_year', '$Album_format', '$Number_of_volumes','$Language', '$Catalog_tier', '$Original_release_date', '$Bedrijf_id')");

    foreach ($data['Album_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_album_prim (album_id,artiest_id) VALUES ($albumId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Album_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_album_feat (album_id,artiest_id) VALUES ($albumId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    $sql .= "UPDATE tbl_upc SET gebruikt = 1 WHERE Code = $UPC;";

    ExMultipleSql($sql);

    return $albumId;  
  }

  function GetAlbumDetails($album_id){
    $data = ReturnFirstRow("SELECT * FROM tbl_albums WHERE album_id = $album_id AND Verwijderd = 0;");

    $data['Album_primary_artists'] = ResultToArray("SELECT artiest_id FROM tbl_artiest_album_prim  WHERE album_id = $album_id;");
    $data['Album_featuring_artists'] = ResultToArray("SELECT artiest_id FROM tbl_artiest_album_feat  WHERE album_id = $album_id;");
    return $data;
  }
 
  function getAlbumTable($van,$tot, $bedr_id){
    $arrTitles = array("Label","Album_title","Primary_artists", "Album_version", "UPC","Catalog_number","Release_date","Main_genre","Recording_year","Album_format"
    ,"Catalog_tier","Original_release_date");
    
    //Header 
    $tablehtml = "<thead><tr>";
    foreach($arrTitles as $t){
      $tablehtml .= "<th>".str_replace("_"," ",$t)."</th>";
    }
    $tablehtml .= "</tr></thead>";

    //Body
    $tablehtml .= "<tbody>";

    $albums = ResultToArray(
      "SELECT ta.album_id, tlb.omschrijving as Label,
      ta.Album_title,
      ta.Album_version,
      ta.UPC,
      ta.Catalog_number,
      ta.Release_date,
      tg.omschrijving as Main_genre,
      ta.Recording_year,
      tf.Description as Album_format,
      tc.Omschrijving as Catalog_tier,
      ta.Original_release_date
      FROM tbl_albums ta
      INNER JOIN tbl_label tlb ON ta.label = tlb.label_id
      INNER JOIN tbl_genre tg ON ta.Main_genre = tg.genre_id
      INNER JOIN tbl_format tf ON ta.Album_format = tf.format_id
      INNER JOIN tbl_catalog_tier tc ON ta.Catalog_tier = tc.cat_tier_id
      WHERE Release_date BETWEEN '$van' AND '$tot' AND ta.Bedrijf_id = $bedr_id AND ta.Verwijderd = 0 ORDER BY ta.Release_date DESC ;"
    );

    foreach ($albums as $k=>$album){
      //Add the primary artists
      $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_artiest_album_prim aa ON ta.artiest_id = aa.artiest_id WHERE aa.album_id= " . $album['album_id'] . ";");
      foreach ($artists as $a) $album['Primary_artists'] .= $a['omschrijving']." | ";
      $albums[$k]['Primary_artists'] = substr($album['Primary_artists'],0,-1);
    }

    foreach($albums as $a){
      $tablehtml .= "<tr name=\"".$a['album_id']."\">";
      foreach($arrTitles as $t){
        $tablehtml .= "<td>".$a[$t]."</td>";
      }
      $tablehtml .= "</tr>";
    }
    $tablehtml .= "</tbody>";
    return $tablehtml;  
  }

  function GetAlbumIdVanTotBedrijf($van, $tot, $bedrId){
    return ResultToArray("SELECT album_id FROM tbl_albums
      WHERE Release_date BETWEEN '$van' AND '$tot' AND Bedrijf_id = $bedrId AND Verwijderd = 0;");
  }

  function GetNextCatalogNr($bedrijfsid){
    //voorvoegsel van het bedrijf achterhalen.
    $voorvoegsel = GetSingleResult("SELECT Catalog_voorvoegsel FROM tbl_bedrijven WHERE bedrijf_id = $bedrijfsid AND Verwijderd = 0;","Catalog_voorvoegsel");
    
    //laatste voorvoegsel van het bedrijf uit de database halen
    $last_nr = GetSingleResult("SELECT Catalog_number FROM tbl_albums WHERE Catalog_number LIKE '$voorvoegsel%' AND Verwijderd = 0 ORDER BY Catalog_number DESC LIMIT 1","Catalog_number");
    return $voorvoegsel . sprintf("%05d",(str_replace($voorvoegsel,"",$last_nr) + 1));
  }

  function OpslaanAlbum($data){
    $db = ConnectDb();

    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);
    //print_r($data);
    $par = $Parental_advisory != "0" ? 0 : 1;
    $LockedStatus = $Locked != "0" ? 0 : 1;

    $currentuserid = $_SESSION['User']['user_id'];

    $locked =  GetSingleResult("SELECT Locked FROM tbl_albums WHERE album_id = $album_id;", "Locked");
    $role = GetSingleResult("SELECT role FROM tbl_users WHERE user_id = '$currentuserid'", "role");

    if ($locked == 0 || $role == 1) {
      $result = ExQuery("UPDATE tbl_albums SET label = '$Label', Album_title = '$Album_title', Album_version = '$Album_version',
      UPC = '$UPC', Catalog_number = '$Catalog_number', Release_date = '$Release_date', Original_release_date = '$Original_release_date',
      Main_genre = '$Main_genre', Cline_year = '$Cline_year', Cline_name = '$Cline_name', Pline_year = '$Pline_year', Pline_name = '$Pline_name',
      Recording_year = '$Recording_year', Album_format = '$Album_format', Number_of_volumes = '$Number_of_volumes', Language = '$Language',
      Catalog_tier = '$Catalog_tier', Parental_advisory = '$par', Locked = '$LockedStatus'
      WHERE album_id = '$album_id';" ,$db);
      
      if($result != true) return $result;

      $sql = "DELETE FROM tbl_artiest_album_prim WHERE album_id = $album_id;";
      $sql .= "DELETE FROM tbl_artiest_album_feat WHERE album_id = $album_id;";
      foreach ($data['Album_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_album_prim (album_id,artiest_id) VALUES ($album_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Album_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_album_feat (album_id,artiest_id) VALUES ($album_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
 
      return ExMultipleSql($sql);
    } else {
      return 0;
    }
  }

  function VerwijderAlbums($albums){
    $db = ConnectDb();

    $album_ids =  implode(",",$albums);

    $currentuserid = $_SESSION['User']['user_id'];

    $locked =  GetSingleResult("SELECT Locked FROM tbl_albums WHERE album_id IN($album_ids);", "Locked");
    $role = GetSingleResult("SELECT role FROM tbl_users WHERE user_id = '$currentuserid';", "role");

    if ($locked == 0 || $role == 1) {
    $sql = "UPDATE tbl_albums SET Verwijderd = 1 WHERE album_id IN($album_ids);";
    $sql .= "UPDATE tbl_track SET Verwijderd = 1 WHERE album_id IN ($album_ids);";
    foreach($albums as $a){
      $sql .= "UPDATE tbl_upc set gebruikt = 0 WHERE code = (SELECT UPC From tbl_albums WHERE album_id = $a); ";
    }
    //return $sql;
    return ExMultipleSql($sql);
    } else {
      return 0;
    }
  }

  function CheckUPC($upc){  
    return GetSingleResult("SELECT COUNT(Code) FROM tbl_upc WHERE Code = '$upc';", "COUNT(Code)");
  }
  function CheckCatNum($catNum){  
    return GetSingleResult("SELECT COUNT(Catalog_number) FROM tbl_albums WHERE Catalog_number = '$catNum' AND verwijderd = 0;", "COUNT(Catalog_number)");
  }

  function CheckLockedStatusAlbum($album_id){
    return GetSingleResult("SELECT Locked FROM tbl_albums WHERE album_id = $album_id;","Locked");
  }
}

class clsTrack {
  function GetTrackDetails($track_id){
    $sql ="SELECT tt.Track_title, td.track_id, td.Track_version, td.ISRC, td.Volume_number, td.Track_main_genre, td.Track_main_subgenre, td.Track_alternate_genre, 
           td.Track_alternate_subgenre, td.Track_language, td.Audio_language, td.Lyrics, td.Track_sequence, td.Track_catalog_tier, td.Available_separately, 
           td.Track_parental_advisory, td.Original_file_name, td.Track_recording_year, td.Release_date, td.Original_release_date, tt.Locked
           FROM tbl_track tt
           INNER JOIN tbl_track_details td ON tt.track_id = td.track_id 
           WHERE tt.track_id = $track_id AND tt.Verwijderd = 0;";
    $track = ReturnFirstRow($sql);

    $track['Track_primary_artists'] = ResultToArray("SELECT artiest_id FROM tbl_artiest_track_prim  WHERE track_id = $track_id;");
    $track['Track_featuring_artists'] = ResultToArray("SELECT artiest_id FROM tbl_artiest_track_feat  WHERE track_id = $track_id;");
    $track['Composers'] = ResultToArray("SELECT composer_id FROM tbl_composer_track WHERE track_id = $track_id;");
    $track['Lyricists'] = ResultToArray("SELECT lyricist_id FROM tbl_lyricist_track WHERE track_id = $track_id;");
    $track['Remixers'] = ResultToArray("SELECT artiest_id FROM tbl_remixers_track WHERE track_id = $track_id;");
    $track['Performers'] = ResultToArray("SELECT artiest_id FROM tbl_performers_track WHERE track_id = $track_id;");
    $track['Publishers'] = ResultToArray("SELECT publisher_id FROM tbl_publishers_track WHERE track_id = $track_id;");
    $track['Writers'] = ResultToArray("SELECT composer_id FROM tbl_writers_track WHERE track_id = $track_id;");
    $track['Contributing_artists'] = ResultToArray("SELECT artiest_id FROM tbl_contributing_artist_track WHERE track_id = $track_id;");
    return $track;
  }

  function TrackWijzigen($data){
    
    $db = ConnectDb();    

    foreach($data as $k=>$val){
        $$k = $db->real_escape_string($val);
    }
    
    $Track_par = $Track_parental_advisory != "0" ? 0 : 1;
    $Available_sep = $Available_separately != "0" ? 0 : 1;
    $LockedStatus = $Locked != "0" ? 0 : 1;
    $currentuserid = $_SESSION['User']['user_id'];

    $locked =  GetSingleResult("SELECT Locked FROM tbl_track WHERE track_id = $track_id;", "Locked");
    $role = GetSingleResult("SELECT role FROM tbl_users WHERE user_id = '$currentuserid'", "role");

    if ($locked == 0 || $role == 1) {
      $sql = "UPDATE tbl_track SET Track_title = '$Track_title', Locked = '$LockedStatus' WHERE track_id = $track_id;";

      $sql .= "UPDATE tbl_track_details SET Track_version = '$Track_version', ISRC = '$ISRC',  Volume_number='$Volume_number', Track_main_genre='$Track_main_genre', 
              Track_main_subgenre='$Track_main_subgenre', Track_language='$Track_language', Audio_language='$Audio_language', Track_catalog_tier='$Track_catalog_tier',
              Track_sequence='$Track_sequence', Original_file_name='$Original_file_name', Available_separately='$Available_sep',
              Track_parental_advisory='$Track_par', Track_recording_year='$Track_recording_year', Release_date='$Release_date', Original_release_date='$Original_release_date'
              WHERE track_id = $track_id;";
      
      //Verwijderen van de data uit de koppel tabel
      $tabels  = array("tbl_composer_track","tbl_lyricist_track","tbl_remixers_track","tbl_performers_track","tbl_publishers_track","tbl_writers_track","tbl_artiest_track_prim",
                    "tbl_artiest_track_feat","tbl_contributing_artist_track");
      foreach($tabels as $tabel){
        $sql .= "DELETE FROM $tabel WHERE track_id = $track_id;";
      }
  
      //Opnieuw toevoegen data aan koppel tabel
      foreach ($data['Composers'] as $item) if($item !== '') $sql .= "INSERT INTO tbl_composer_track (track_id,composer_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Lyricists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_lyricist_track (track_id,lyricist_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Remixers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_remixers_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Performers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_performers_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Publishers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_publishers_track (track_id,publisher_id) VALUES ($track_id,(SELECT publisher_id FROM tbl_publishers WHERE omschrijving = '$item'));";
      //foreach ($data['Producers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_producer_track (track_id,producer_id) VALUES ($Track_id,(SELECT producer_id FROM tbl_producers WHERE omschrijving = '$item'));";
      foreach ($data['Writers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_writers_track (track_id,composer_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Track_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Track_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Contributing_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_contributing_artist_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      //echo $sql;
      return ExMultipleSql($sql);
    } else {
      return 0;
    }
  }

  function GetTrackRows($albumId){
    $arrTitles =array("Track_title", "ISRC", "Track_main_genre", "Audio_language", "Track_catalog_tier", "Original_file_name", "Release_date", "Original_release_date");
    
    //Header
    $tablehtml = "<thead><tr>";
    foreach($arrTitles as $t){
      $tablehtml .= "<th>".str_replace("_"," ",$t)."</th>";
    }
    $tablehtml .= "</tr></thead>";

    //Body
    $tablehtml .= "<tbody>";
    $tracks = ResultToArray(
      "SELECT tt.track_id, tt.Track_title, td.ISRC, tg.omschrijving as Track_main_genre, tl.2_letter_code as Audio_language, 
      tc.Omschrijving as Track_catalog_tier, td.Original_file_name, td.Release_date, td.Original_release_date
      FROM tbl_track tt
      INNER JOIN tbl_track_details td ON td.track_id = tt.track_id
      INNER JOIN tbl_genre tg ON td.Track_main_genre = tg.genre_id
      INNER JOIN tbl_language tl ON td.Audio_language = tl.language_id 
      INNER JOIN tbl_catalog_tier tc ON td.Track_catalog_tier = tc.cat_tier_id
      WHERE tt.Album_id = $albumId AND tt.Verwijderd = 0 ORDER BY td.ISRC DESC;"
    );

    foreach($tracks as $tr){
      $tablehtml .= "<tr name=\"".$tr['track_id']."\" data-bs-toggle=\"modal\" data-bs-target=\"#detailsTrackModal\" onclick=\"tracksToonDetailsTrack('" . $tr['track_id'] . "');\">";
      foreach($arrTitles as $t){
        $tablehtml .= "<td>".$tr[$t]."</td>";
      }
      $tablehtml .= "</tr>";
    }

    $tablehtml .= "</tbody>";
    return $tablehtml;  
  }

  function CopyTracks ($data,$aantal){
    $db = ConnectDb();    
    //print_r($data);
    foreach($data as $k=>$val){
        $$k = $db->real_escape_string($val);
    }
    $Track_par = $Track_parental_advisory != "0" ? 0 : 1;
    $Available_sep = $Available_separately != "0" ? 0 : 1;

    for($i = 0; $i < $aantal; $i++){

      $ISRC = $this->GetNextISRC($albumId);
      
      
      $sql = "INSERT INTO tbl_track(Track_title, album_id) VALUES('$Track_title','$albumId');";
   
      $Track_id = ReturnLastInsertedId($sql);
  
      $sql = "INSERT INTO tbl_track_details(Track_id, Track_version, ISRC, Volume_number, Track_main_genre, Track_main_subgenre, Track_language, Audio_language,
      Track_catalog_tier, Track_sequence, Original_file_name, Available_separately, Track_parental_advisory, Track_recording_year, Release_date, Original_release_date) 
      VALUES('$Track_id', '$Track_version', '$ISRC', '$Volume_number', '$Track_main_genre', '$Track_main_subgenre', '$Track_language', '$Audio_language',
      '$Track_catalog_tier', '$Track_sequence', '$Original_file_name', '$Available_sep', '$Track_par', '$Track_recording_year', '$Release_date', '$Original_release_date');";
  
      foreach ($data['Composers'] as $item) if($item !== '') $sql .= "INSERT INTO tbl_composer_track (track_id,composer_id) VALUES ($Track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Lyricists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_lyricist_track (track_id,lyricist_id) VALUES ($Track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Remixers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_remixers_track (track_id,artiest_id) VALUES ($Track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Performers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_performers_track (track_id,artiest_id) VALUES ($Track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Publishers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_publishers_track (track_id,publisher_id) VALUES ($Track_id,(SELECT publisher_id FROM tbl_publishers WHERE omschrijving = '$item'));";
      //foreach ($data['Producers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_producer_track (track_id,producer_id) VALUES ($Track_id,(SELECT producer_id FROM tbl_producers WHERE omschrijving = '$item'));";
      foreach ($data['Writers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_writers_track (track_id,composer_id) VALUES ($Track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($data['Track_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($Track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Track_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($Track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($data['Contributing_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_contributing_artist_track (track_id,artiest_id) VALUES ($Track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      
      // return $sql;
      ExMultipleSql($sql);
    }
    return 1;
  }
  function InsertDetailsTrack($data){

    $db = ConnectDb();    

    foreach($data as $k=>$val){
        $$k = $db->real_escape_string($val);
    }
    $Track_par = $Track_parental_advisory != "0" ? 0 : 1;
    $Available_sep = $Available_separately != "0" ? 0 : 1;
    
    $newTrackId = ReturnLastInsertedId("INSERT INTO tbl_track(Track_title, album_id) VALUES('$Track_title','$albumId');");

    $sql = "INSERT INTO tbl_track_details(Track_id, Track_version, ISRC, Volume_number, Track_main_genre, Track_main_subgenre, Track_language, Audio_language,
    Track_catalog_tier, Track_sequence, Original_file_name, Available_separately, Track_parental_advisory, Track_recording_year, Release_date, Original_release_date) 
    VALUES('$newTrackId', '$Track_version', '$ISRC', '$Volume_number', '$Track_main_genre', '$Track_main_subgenre', '$Track_language', '$Audio_language',
    '$Track_catalog_tier', '$Track_sequence', '$Original_file_name', '$Available_sep', '$Track_par', '$Track_recording_year', '$Release_date', '$Original_release_date');";

    foreach ($data['Composers'] as $item) if($item !== '') $sql .= "INSERT INTO tbl_composer_track (track_id,composer_id) VALUES ($newTrackId,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
    foreach ($data['Lyricists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_lyricist_track (track_id,lyricist_id) VALUES ($newTrackId,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
    foreach ($data['Remixers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_remixers_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Performers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_performers_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Publishers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_publishers_track (track_id,publisher_id) VALUES ($newTrackId,(SELECT publisher_id FROM tbl_publishers WHERE omschrijving = '$item'));";
    //foreach ($data['Producers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_producer_track (track_id,producer_id) VALUES ($Track_id,(SELECT producer_id FROM tbl_producers WHERE omschrijving = '$item'));";
    foreach ($data['Writers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_writers_track (track_id,composer_id) VALUES ($newTrackId,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
    foreach ($data['Track_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Track_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Contributing_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_contributing_artist_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    
    // return $sql;
    return ExMultipleSql($sql);
  }

  function GetNextISRC($albumId){
    //achterhalen voorvoegsel van het bedrijf van het album.
    
     $voorvoegsel = GetSingleResult("SELECT b.ISRC_Voorvoegsel FROM tbl_bedrijven b
      INNER JOIN tbl_albums a ON a.Bedrijf_id = b.bedrijf_id
      WHERE a.album_id = $albumId AND a.Verwijderd = 0;","ISRC_Voorvoegsel");

    $year = Date("y");

    $left = "NL-$voorvoegsel-$year-";
    
    
    $last = GetSingleResult("SELECT td.ISRC FROM tbl_track_details td
                            INNER JOIN tbl_track t ON t.track_id = td.track_id 
                            WHERE td.ISRC LIKE '$left%' AND t.Verwijderd = 0 ORDER BY ISRC DESC LIMIT 1;","ISRC");

    if($left == "NL-GM1-22-"){
      $lastno = intval(str_replace($left,"",$last)) + 1;
      return $left . sprintf("%05d",max($lastno,3781));
    } 

    return $left . sprintf("%05d",(str_replace($left,"",$last) + 1)); 
  }

  function TrackVerwijderen($track_id){
    $db = ConnectDb(); 

    $currentuserid = $_SESSION['User']['user_id'];

    $locked =  GetSingleResult("SELECT Locked FROM tbl_track WHERE track_id = $track_id;", "Locked");
    $role = GetSingleResult("SELECT role FROM tbl_users WHERE user_id = '$currentuserid'", "role");

    if ($locked == 0 || $role == 1) {  
      return ExQuery("UPDATE tbl_track SET Verwijderd = 1 WHERE track_id = $track_id;", $db);
    } else {
      return 0;
    }   
  }

  function checkISRC($isrc){  
    return GetSingleResult("SELECT COUNT(td.ISRC) as aantal FROM tbl_track_details td 
                            INNER JOIN tbl_track t ON td.track_id = t.track_id WHERE td.ISRC = '$isrc' and t.verwijderd = 0;", "aantal");
  }

  function CheckLockedStatusTrack($trackId){
    return GetSingleResult("SELECT Locked From tbl_track WHERE track_id = $trackId;","Locked");
  }

  function Vertaling($AlbumLanguage, $TrackLanguage, $AudioLanguage, $albumId){
    $db = ConnectDb(); 
    $clsAlbum = new clsAlbum();

    $data = $clsAlbum->GetAlbumDetails($albumId);
     foreach($data as $k=>$val){
       $$k = $db->real_escape_string($val);
     }
    
    // Nieuw catalogus nummer, UPC en ISRC 
    $catNum = $clsAlbum->GetNextCatalogNr($Bedrijf_id);
    $upc = GetSingleResult("SELECT Code From tbl_upc Where gebruikt = 0 LIMIT 1;","Code");

    $newAlbumId = ReturnLastInsertedId("INSERT INTO tbl_albums(Label, ALbum_title, Album_version, UPC, Catalog_number, Release_date, Main_genre, Cline_year, Cline_name,
    Pline_year, Pline_name, Parental_advisory, Recording_year, Album_format, Number_of_volumes, Language, Catalog_tier, Original_release_date, Bedrijf_id) 
    VALUES ('$Label', '$Album_title', '$Album_version', '$upc', '$catNum', '$Release_date', '$Main_genre', '$Cline_year', '$Cline_name', '$Pline_year', '$Pline_name',
    '$Parental_advisory', '$Recording_year', '$Album_format', '$Number_of_volumes','$AlbumLanguage', '$Catalog_tier', '$Original_release_date', '$Bedrijf_id')");
    $sql = "UPDATE tbl_upc SET gebruikt = 1 WHERE Code = $upc;";

    foreach ($data['Album_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_album_prim (album_id,artiest_id) VALUES ($newAlbumId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    foreach ($data['Album_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_album_feat (album_id,artiest_id) VALUES ($newAlbumId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
    
    ExMultipleSql($sql);

    $trackIds = ResultToArray("SELECT track_id FROM tbl_track WHERE album_id = $albumId AND Verwijderd = 0;");
    $t_ids = [];
    $_isrc = $this->GetNextISRC($newAlbumId);

    foreach($trackIds as $k=>$v){
      array_push($t_ids,$v['track_id']);
    }
    foreach($t_ids as $id){
      $track = $this->GetTrackDetails($id);
      
      foreach($track as $k=>$value) $$k = $db->real_escape_string($value);
       
       $newTrackId = ReturnLastInsertedId("INSERT INTO tbl_track(Track_title, album_id) VALUES('$Track_title','$newAlbumId');");

      $sql = "INSERT INTO tbl_track_details(Track_id, Track_version, ISRC, Volume_number, Track_main_genre, Track_main_subgenre,Track_language, Audio_language,
       Track_catalog_tier, Track_sequence, Original_file_name, Available_separately, Track_parental_advisory, Track_recording_year, Release_date, Original_release_date) 
       SELECT Track_id, Track_version, '$_isrc', Volume_number, Track_main_genre, Track_main_subgenre, '$TrackLanguage', '$AudioLanguage',
       Track_catalog_tier, Track_sequence, Original_file_name, Available_separately, Track_parental_advisory, Track_recording_year, Release_date, Original_release_date 
         FROM tbl_track_details WHERE track_id = '$id';";

      foreach ($track['Composers'] as $item) if($item !== '') $sql .= "INSERT INTO tbl_composer_track (track_id,composer_id) VALUES ($newTrackId,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($track['Lyricists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_lyricist_track (track_id,lyricist_id) VALUES ($newTrackId,(SELECT lyricists_id FROM tbl_lyricist WHERE omschrijving = '$item'));";
      foreach ($track['Remixers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_remixers_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($track['Performers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_performers_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($track['Publishers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_publishers_track (track_id,publisher_id) VALUES ($newTrackId,(SELECT publisher_id FROM tbl_publishers WHERE omschrijving = '$item'));";
      foreach ($track['Writers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_writers_track (track_id,composer_id) VALUES ($newTrackId,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
      foreach ($track['Track_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($track['Track_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      foreach ($track['Contributing_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_contributing_artist_track (track_id,artiest_id) VALUES ($newTrackId,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";

      ExQuery($sql, $db);

      $left = substr($_isrc,0,strlen($ISRC) - 5);
      $_isrc = $left . sprintf("%05d",(str_replace($left,"",$_isrc) + 1)); 
    }
  }

  function CheckUserRole(){
    $userRole = $_SESSION['User']['role'];
    return GetSingleResult("SELECT role FROM tbl_users WHERE role = $userRole;", "role");
  }

  function CopyToTracks($data, $prim_art, $feat_art, $rel_date, $or_rel_date, $main_genre, $album_id){
    $sql = "";

    $trackIds = ResultToArray("SELECT track_id FROM tbl_track WHERE album_id = $album_id AND Verwijderd = 0;");

    foreach($trackIds as $k => $v){
      $val = $v['track_id'];
      if($prim_art == 'true'){
        $sql .= "DELETE FROM tbl_artiest_track_prim WHERE track_id = $val;";
        foreach ($data['Album_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($val,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      }
      if ($feat_art == 'true'){
        $sql .= "DELETE FROM tbl_artiest_track_feat WHERE track_id = $val;";
        foreach ($data['Album_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($val,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
      }
      if ($rel_date == 'true'){
        $sql .= "UPDATE tbl_track_details SET Release_date = (SELECT Release_date FROM tbl_albums WHERE album_id = $album_id) WHERE track_id = $val;";
      }
      if ($or_rel_date == 'true'){
        $sql .= "UPDATE tbl_track_details SET Original_release_date =(SELECT Original_release_date FROM tbl_albums WHERE album_id = $album_id) WHERE track_id = $val;";
      }
      if ($main_genre == 'true'){
        $sql .= "UPDATE tbl_track_details SET Track_main_genre = (SELECT Main_genre FROM tbl_albums WHERE album_id = $album_id) WHERE track_id = $val;";
      }
    }
    //echo $sql;
    return ExMultipleSql($sql);
  }

  function AddComposerOnchangeArtist($artiestId){
    foreach($artiestId as $k=>$v){
      return ResultToArray("SELECT composer_id FROM tbl_artiest_composer WHERE artiest_id = $v;");
    }
  }
}

class clsArtiest {

    function OphalenDetailsArtiest($artiest_id){
      return ReturnFirstRow("SELECT * FROM tbl_artiest WHERE artiest_id = $artiest_id AND verwijderd = 0");
    }
     
    function OpslaanArtiest($data){
      $db = ConnectDb();

      foreach($data as $k=>$v) $$k = $db->real_escape_string($v);
      //print_r($data['composer_id']);
      $sql="";
      foreach($data['composer_id'] as $key=>$val){
        $sql .= "INSERT INTO tbl_artiest_composer(artiest_id, composer_id)VALUES('$artiest_id', '$val');";
      }
      $sql .= "UPDATE tbl_artiest SET omschrijving = '$omschrijving' WHERE artiest_id = '$artiest_id';";

      return ExMultipleSql($sql);
    }
  
    function ArtiestToevoegen($naam){
        $db = ConnectDb();
        $naam = $db->real_escape_string($naam);

        return ReturnLastInsertedId("INSERT INTO tbl_artiest(omschrijving) VALUES('$naam')" ,$db);
    }
  
    function VerwijderArtiest($data){
      $db = ConnectDb();
      foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

      return ExQuery("UPDATE tbl_artiest SET verwijderd = $verwijderd WHERE artiest_id = '$artiest_id'", $db);
    }

    function OphalenArtiesten(){
      return ResultToArray("SELECT artiest_id, omschrijving FROM tbl_artiest WHERE verwijderd = 0;");
    } 


}

class clsComposers{
  function GetComposers(){
    return ResultToArray("SELECT composer_id, omschrijving FROM tbl_composers where verwijderd = 0 order by omschrijving;");
  }

  function ComposerOpslaan($data){
    $db = ConnectDb();

    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_composers SET omschrijving = '$omschrijving' WHERE composer_id = '$composer_id'" ,$db);
  }

  function GetComposerDetails($composer_id){
      
    $db = ConnectDb();

    $query = "SELECT omschrijving FROM tbl_composers WHERE composer_id = $composer_id AND verwijderd = 0";
    return ReturnFirstRow($query);
  }

  function OphalenDetailsComposer($composer_id){
    return ReturnFirstRow("SELECT * FROM tbl_composers WHERE composer_id = $composer_id AND verwijderd = 0");
  }

  function VerwijderComposer($data){
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_composers SET verwijderd = $verwijderd WHERE composer_id = '$composer_id'", $db);
  }

  // insert composer 
  function ComposerToevoegen($naam){
    $db = ConnectDb();
    $naam = $db->real_escape_string($naam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_composers(omschrijving) VALUES('$naam')" ,$db);
  }

  // shows data from composer
  function displayDataComposer()
  {
    return ResultToArray("SELECT composer_id, omschrijving FROM tbl_composers AND verwijderd = 0");
  }
}

class clsPublishers{
  function GetPublishers(){
    return ResultToArray("SELECT publisher_id, omschrijving FROM tbl_publishers where verwijderd = 0 order by omschrijving;");
  }
}

class clsLyricists{
  function GetLyricists(){
    return ResultToArray("SELECT lyricists_id, omschrijving FROM tbl_lyricist where verwijderd = 0 order by omschrijving;");
  }

  function LyricistOpslaan($data){
    $db = ConnectDb();

    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_lyricist SET omschrijving = '$omschrijving' WHERE lyricists_id = '$lyricists_id'" ,$db);
  }

  function GetLyricistDetails($lyricists_id){
      
    $db = ConnectDb();

    $query = "SELECT omschrijving FROM tbl_lyricist WHERE lyricists_id = $lyricists_id";
    return ReturnFirstRow($query);
  }

  function OphalenDetailsLyricist($lyricists_id){
    return ReturnFirstRow("SELECT * FROM tbl_lyricist WHERE lyricists_id = $lyricists_id AND verwijderd = 0");
  }

  function VerwijderLyricist($data){
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_lyricist SET verwijderd = $verwijderd WHERE lyricists_id = '$lyricists_id'", $db);
  }

  function LyricistToevoegen($naam){
    $db = ConnectDb();
    $naam = $db->real_escape_string($naam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_lyricist(omschrijving) VALUES('$naam')" ,$db);
  }

  function displayDataLyricist()
  {
    return ResultToArray("SELECT lyricists_id, omschrijving FROM tbl_lyricist where verwijderd = 0");
  }
}

class clsRecords{
  function getAllRecords(){

    $db = ConnectDb();
    $query = "SELECT * FROM tbl_users";
    $result = $db->query($query);
    if ($result->num_rows > 0) {
      echo "<div class='table-responsive'><table id='myTable' class='table table-striped table-bordered'>
              <thead><tr> <th>Name</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Password</th>
                        </tr></thead><tbody>";
      while($row = $result->fetch_assoc()) {
          echo "<tr><td>" . $row['name']."</td>
                    <td>" . $row['username']."</td>
                    <td>" . $row['email']."</td>
                    <td>" . $row['password']."</td></tr>";
      }
    
      echo "</tbody></table></div>";
      
    } else {
          echo "Er zijn geen tabelrijen";
    }
  }
}

class clsCsv {

  function GetExportRowsCSV($albums, $albumdata,$trackdata, $bedrId){
    $db = ConnectDb();

    $album_ids =  implode(",",$albums);
    //return $album_ids;
    $albums = [];
    //create the array for the Export
    $album = [];
    foreach ($albumdata as $t){
      $album[$t] = "";
    }

    $lockAlbumTrack = "UPDATE tbl_albums SET Locked = 1 WHERE album_id IN($album_ids);";

    $lockAlbumTrack .= "UPDATE tbl_track SET Locked = 1 WHERE album_id IN($album_ids);";

    ExMultipleSql($lockAlbumTrack);

    

    //Create the SQL query for the albums.
    $sql = "SELECT a.album_id, a.Album_title, a.Album_version, a.UPC, a.Catalog_number, a.Release_date, g.Omschrijving as 'Main_genre', l.omschrijving as Label,
            a.CLine_year, l1.naam as CLine_name, a.PLine_year, l2.naam as PLine_name, CASE WHEN a.Parental_advisory = 1 THEN 'Y' else 'N' END as Parental_advisory, a.Recording_year,
            af.description as Album_format, a.Number_of_volumes,tl.2_letter_code as 'Language_(Metadata)', 'Mid' as Catalog_tier
            FROM tbl_albums a
            LEFT JOIN tbl_genre g ON a.Main_genre = g.genre_id
            LEFT JOIN tbl_label l ON a.Label = l.label_id
            LEFT JOIN tbl_line l1 ON a.CLine_name = l1.line_id
            LEFT JOIN tbl_line l2 ON a.PLine_name =l2.line_id
            LEFT JOIN tbl_format af ON a.Album_format = af.format_id
            LEFT JOIN tbl_language tl ON a.Language = tl.language_id
            LEFT JOIN tbl_catalog_tier ct ON a.catalog_tier  = ct.cat_tier_id
            WHERE a.album_id IN($album_ids) AND a.Verwijderd = 0 AND a.Bedrijf_id = $bedrId;";
            
    $result = GetResult($sql);

    
    

    foreach($result as $r){
      
      foreach ($album as $k=>$v){
        $album[$k] = $r[$k];
        
      }
      $albums[$r['album_id']]= $album;


    }
    
    foreach ($albums as $k=>$v){

      //Add the Territories
      $albums[$k]['Territories'] = "World";

      //Add the excluded territories
      $territories = GetResult("SELECT t.alpha_2_code FROM tbl_territories t INNER JOIN tbl_excluded_territories_album ta ON t.territorie_id = ta.territory_id WHERE ta.album_id= $k;");
      foreach ($territories as $t) $albums[$k]['Excluded_territories'] .= $t['alpha_2_code']."|";
      $albums[$k]['Excluded_territories'] = substr($albums[$k]['Excluded_territories'],0,-1);

      //Add the primary artists
      $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_artiest_album_prim aa ON ta.artiest_id = aa.artiest_id WHERE aa.album_id= $k;");
      foreach ($artists as $a) $albums[$k]['Primary_artists'] .= $a['omschrijving']."|";
      $albums[$k]['Primary_artists'] = substr($albums[$k]['Primary_artists'],0,-1);

      //Add the featuring artists
      $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_artiest_album_feat aa ON ta.artiest_id = aa.artiest_id WHERE aa.album_id= $k;");
      foreach ($artists as $a) $albums[$k]['Featuring_artists'] .= $a['omschrijving']."|";
      $albums[$k]['Featuring_artists'] = substr($albums[$k]['Featuring_artists'],0,-1);

    }
    
    //Add the tracks to the album
    foreach ($albums as $k=>$v){

      $sql = "SELECT t.track_id,t.Track_title, td.Track_version, td.ISRC, td.Volume_number, tg.omschrijving as Track_main_genre, tg2.omschrijving as Track_main_subgenre, 
              tg3.omschrijving as Track_alternate_genre,tg4.omschrijving as Track_alternate_subgenre, tl.2_letter_code as 'Track_language_(Metadata)', 
              tl2.2_letter_code  as Audio_language, td.Lyrics, 'Y' as Available_separately,
              case when td.Track_parental_advisory = 1 then 'Y' else 'N' END as Track_parental_advisory, td.Track_recording_year, '' as Track_sequence, (SELECT tc.omschrijving FROM tbl_catalog_tier WHERE tc.omschrijving = 'Mid' LIMIT 1) as Track_catalog_tier,
              td.Original_file_name,Original_release_date
              FROM tbl_track t
              LEFT JOIN tbl_track_details td ON t.track_id = td.track_id
              LEFT JOIN tbl_genre tg ON td.Track_main_genre = tg.genre_id
              LEFT JOIN tbl_genre tg2 ON td.Track_main_subgenre = tg2.genre_id
              LEFT JOIN tbl_genre tg3 ON td.Track_alternate_genre = tg3.genre_id
              LEFT JOIN tbl_genre tg4 ON td.Track_alternate_subgenre = tg4.genre_id
              LEFT JOIN tbl_language tl ON td.Track_language = tl.language_id
              LEFT JOIN tbl_language tl2 ON td.Audio_language = tl2.language_id
              INNER JOIN tbl_catalog_tier tc ON td.Track_catalog_tier = tc.cat_tier_id
              WHERE t.album_id = $k AND t.Verwijderd = 0;";
      
      $result = GetResult($sql);

      // return print_r($albums[$k]);
      $albums[$k]['tracks'] = [];
      $track=[];

      foreach($result as $r){
        foreach ($trackdata as $td){
          $track[$td] = $r[$td];
        }

    
        //Add the primary artists
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_artiest_track_prim aa ON ta.artiest_id = aa.artiest_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Track_primary_artists'] .= $a['omschrijving']."|";
        $track['Track_primary_artists'] = substr($track['Track_primary_artists'],0,-1);

        //Add the featuring artists
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_artiest_track_feat aa ON ta.artiest_id = aa.artiest_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Track_featuring_artists'] .= $a['omschrijving']."|";
        $track['Track_featuring_artists'] = substr($track['Track_featuring_artists'],0,-1);

        //Add the contributing artists
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_contributing_artist_track aa ON ta.artiest_id = aa.artiest_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Contributing_artists'] .= $a['omschrijving']."|";
        $track['Contributing_artists'] = substr($track['Contributing_artists'],0,-1);

        //Add the composers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_composers ta INNER JOIN tbl_composer_track aa ON ta.composer_id = aa.composer_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Composers'] .= $a['omschrijving']."|";
        $track['Composers'] = substr($track['Composers'],0,-1);
            
        //Add the lyricists
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_lyricist ta INNER JOIN tbl_lyricist_track aa ON ta.lyricist_id = aa.lyricist_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Lyricists'] .= $a['omschrijving']."|";
        $track['Lyricists'] = substr($track['Lyricists'],0,-1);

        //Add the remixers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_remixers_track aa ON ta.artiest_id = aa.artiest_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Remixers'] .= $a['omschrijving']."|";
        $track['Remixers'] = substr($track['Remixers'],0,-1);

        //Add the Performers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_artiest ta INNER JOIN tbl_performers_track aa ON ta.artiest_id = aa.artiest_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Performers'] .= $a['omschrijving']."|";
        $track['Performers'] = substr($track['Performers'],0,-1);

        //Add the Producers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_producers ta INNER JOIN tbl_producers_track aa ON ta.producer_id = aa.producer_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Producers'] .= $a['omschrijving']."|";
        $track['Producers'] = substr($track['Producers'],0,-1);

        //Add the writers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_composers ta INNER JOIN tbl_composer_track aa ON ta.composer_id = aa.composer_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Writers'] .= $a['omschrijving']."|";
        $track['Writers'] = substr($track['Writers'],0,-1);

        //Add the Publishers
        $artists = GetResult("SELECT ta.omschrijving FROM tbl_publishers ta INNER JOIN tbl_publishers_track aa ON ta.publisher_id = aa.publisher_id WHERE aa.track_id= " . $r['track_id'] . ";");
        foreach ($artists as $a) $track['Publishers'] .= $a['omschrijving']."|";
        $track['Publishers'] = substr($track['Publishers'],0,-1);

        $albums[$k]['tracks'][$r['track_id']]= $track;

      }
    }

      
    foreach($albums as $key=>$value){

      foreach($albums[$key]['tracks'] as $track){

        $html .= '<tr>';

        foreach($value as $key2=>$value2){
          if($key2 != "tracks") $html .= '<td>' . htmlspecialchars($value2) . '</td>';
        }
        foreach($track as $t){
          $html .= '<td>' . htmlspecialchars($t) . '</td>';
        }
        $html .= '</tr>';
      }
    }  
    
    return $html;
  }

  function InsertDataCsv($data, $bedrijfsid) {
    //echo $bedrijfsid;
    $db=ConnectDb();
    $tracks = json_decode($data,true); 
    
    if(count($tracks) == 0) return;
    $albums = array();
    
    //Eerst het eerste album toevoegen
    $album = new Album();
    $album->fromCSV($tracks[1]);
    array_push($albums,$album);


    

    //Loop door de records, als een upc veranderd nieuw album toevoegen.
    foreach ($tracks as $t){
      if($t['UPC']){
        if($t['UPC'] != $album->GetUPC()){
          $album = new Album();
          $album->fromCSV($t);
          array_push($albums,$album);
        }  
      } 
      else if($t['Album title'] != $album->GetAlbumTitle()){
        $album = new Album();
        $album->fromCSV($t);
        array_push($albums,$album);
      }
      
      

      $track = new Track();
      $track->fromCSV($t);
      $album->addTrack($track);
    } 

    $albums = json_decode(json_encode($albums), true);
    //print_r($albums);

    $result = $this->InvoerenDataWaarIdGebruiktWordt($albums);


    if($result == 1){
      foreach($albums as $album){
        if(!$album['UPC']){
          $album['UPC'] = GetSingleResult("SELECT Code From tbl_upc Where gebruikt = 0 LIMIT 1","Code");
        }
        if(!$album['Catalog_number']){
          $clsAlbum = new clsAlbum();
          $album['Catalog_number'] = $clsAlbum -> GetNextCatalogNr($bedrijfsid);
        }

        $album['Parental_advisory'] = strtolower($album['Parental_advisory']) =="n" ? 0 : 1;
        // Insert Album
        $sql = "INSERT INTO tbl_albums (Album_title, Label, Album_version, UPC, Catalog_number, Release_date, Main_genre, Cline_year, Cline_name,
        Pline_year, Pline_name, Parental_advisory, Album_format, Number_of_volumes, Catalog_tier, Original_release_date , Recording_year, Language, Bedrijf_id)
        VALUES ('".$album['Album_title']."',(SELECT label_id FROM tbl_label WHERE omschrijving = '".$album['Label']."'),'".$album['Album_version']."','".$album['UPC']."',
        '".$album['Catalog_number']."','".$album['Release_date']."',(SELECT genre_id FROM tbl_genre WHERE omschrijving = '".$album['Main_genre']."')
        ,'".$album['CLine_year']."',(SELECT line_id FROM tbl_line WHERE naam = '".$album['CLine_name']."'),'".$album['PLine_year']."',
        (SELECT line_id FROM tbl_line WHERE naam = '".$album['PLine_name']."'),'".$album['Parental_advisory']."',(SELECT format_id FROM tbl_format WHERE description = '".$album['Album_format']."'),
        '".$album['Number_of_volumes']."',(SELECT cat_tier_id FROM tbl_catalog_tier WHERE omschrijving = '".$album['Catalog_tier']."'),'".$album['Original_release_date']."',
        '".$album['Recording_year']."',(SELECT language_id FROM tbl_language WHERE 2_letter_code = '".$album['Language_Metadata']."'),'". $bedrijfsid ."');";
        //echo "$sql \n";
      
      $album_id = ReturnLastInsertedId($sql);

      $sql = "";
      $upc = $album['UPC'];
      $sql .= "UPDATE tbl_upc SET gebruikt = 1 WHERE Code = $upc;";
      //INSERT Territories and excluded territories
      foreach ($album['Territories'] as $t){
        $sql .= "INSERT INTO tbl_territories_album (album_id,territory_id) VALUES ($album_id,(SELECT territorie_id FROM tbl_territories WHERE alpha_2_code = '$t'));";
      }
      foreach ($album['Excluded_territories'] as $et){
        $sql .= "INSERT INTO tbl_excluded_territories_album (album_id,territory_id) VALUES ($album_id,(SELECT territorie_id FROM tbl_territories WHERE alpha_2_code = '$et'));";
      }

      //INSERT Primary and featuring artists
      foreach ($album['Primary_artists'] as $pa){
        $sql .= "INSERT INTO tbl_artiest_album_prim (album_id,artiest_id) VALUES ($album_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$pa'));";
      }
      foreach ($album['Featuring_artists'] as $fa){
        $sql .= "INSERT INTO tbl_artiest_album_feat (album_id,artiest_id) VALUES ($album_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$fa'));";
      }

      ExMultipleSql($sql);

       // Insert Track
       foreach($album['tracks'] as $track){
         //echo $album_id;
        //print_r($track);
        $track['Track_parental_advisory'] = strtolower($track['Track_parental_advisory']) =="n" ? 0 : 1;
        $track['Available_separately'] = strtolower($track['Available_separately']) =="n" ? 0 : 1; 
        $sql = "INSERT INTO tbl_track (Track_title, album_id) VALUES ('".$track['Track_title']."',$album_id);";

        $track_id = ReturnLastInsertedId($sql);

        if(!$track['ISRC']){
          $clsTrack = new clsTrack();
          $track['ISRC'] = $clsTrack -> GetNextISRC($album_id);
        }
      // Insert Track Details
      $sql = "INSERT INTO tbl_track_details (track_id, Track_version, ISRC, Volume_number, Track_main_genre, Track_main_subgenre, Track_language,
      Audio_language, Track_alternate_genre,  Track_alternate_subgenre, Available_separately, Track_parental_advisory, Lyrics, 
       Track_catalog_tier, Original_file_name, Track_recording_year,Original_release_date)
       VALUES ('".$track_id."', '".$track['Track_version']."','".$track['ISRC']."','".$track['Volume_number']."',(SELECT genre_id FROM tbl_genre WHERE omschrijving = '".$track['Track_main_genre']."'),
       (SELECT genre_id FROM tbl_genre WHERE omschrijving = '".$track['Track_main_subgenre']."'),(SELECT language_id FROM tbl_language WHERE 2_letter_code = '".$track['Track_language_Metadata']."')
       ,(SELECT language_id FROM tbl_language WHERE 2_letter_code = '".$track['Audio_language']."'),
       (SELECT genre_id FROM tbl_genre WHERE omschrijving = '".$track['Track_alternate_genre']."'),(SELECT genre_id FROM tbl_genre WHERE omschrijving = '".$track['Track_alternate_subgenre']."'),
       '".$track['Available_separately']."','".$track['Track_parental_advisory']."',(SELECT lyricists_id FROM tbl_lyricist WHERE omschrijving = '".$track['Lyrics']."'),
       (SELECT cat_tier_id FROM tbl_catalog_tier WHERE omschrijving = '".$track['Track_catalog_tier']."'),
       '".$track['Original_file_name']."','".$track['Track_recording_year']."','" . $track['Original_release_date'] . "');";

       foreach ($track['Composers'] as $item) if($item !== '') $sql .= "INSERT INTO tbl_composer_track (track_id,composer_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
       foreach ($track['Lyricists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_lyricist_track (track_id,lyricist_id) VALUES ($track_id,(SELECT lyricists_id FROM tbl_lyricist WHERE omschrijving = '$item'));";
       foreach ($track['Remixers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_remixers_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
       foreach ($track['Performers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_performers_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
       foreach ($track['Publishers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_publisher_track (track_id,publisher_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
       foreach ($track['Producers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_producer_track (track_id,producer_id) VALUES ($track_id,(SELECT producer_id FROM tbl_producers WHERE omschrijving = '$item'));";
       foreach ($track['Writers'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_writers_track (track_id,composer_id) VALUES ($track_id,(SELECT composer_id FROM tbl_composers WHERE omschrijving = '$item'));";
       foreach ($track['Track_primary_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_artiest_track_prim (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
       foreach ($track['Track_featuring_artists'] as $item) if($item !=='')  $sql .= "INSERT INTO tbl_artiest_track_feat (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
       foreach ($track['Contributing_artists'] as $item) if($item !== '')  $sql .= "INSERT INTO tbl_contributing_artist_track (track_id,artiest_id) VALUES ($track_id,(SELECT artiest_id FROM tbl_artiest WHERE omschrijving = '$item'));";
        //echo $sql;
       $result = ExMultipleSql($sql,$db, false);
       if(!$result) {
        echo $result;
        return;
       }
       }
      }
      $db->close(); 
    } 
  }

  //Loop through the tracks and filter all unique items so we can insert them to the database. (if not exist)
  function InvoerenDataWaarIdGebruiktWordt($albums){

    //Array maken van het object
    //variabelen definieren waar we een id voor invoeren
    $uniek = array();
    $uniek['label'] = array();
    $uniek['artiest'] = array();
    $uniek['line'] = array(); 
    $uniek['composer'] = array();
    $uniek['lyricists'] = array();
    $uniek['publishers'] = array();
    $uniek['producers'] = array();

     foreach ($albums as $a){

      //eerst de gegevens per album
      array_push($uniek['label'],$a['Label']);
      foreach($a['Primary_artists'] as $p_artiest) {array_push($uniek['artiest'],$p_artiest);}
      foreach($a['Featuring_artists'] as $f_artiest) {array_push($uniek['artiest'],$f_artiest);}
      array_push($uniek['line'],$a['CLine_name']);
      array_push($uniek['line'],$a['PLine_name']);
      
      //dan per track
      foreach ($a['tracks'] as $track){
        foreach($track['Track_primary_artists'] as $p_artiest) {array_push($uniek['artiest'],$p_artiest);}
        foreach($track['Track_featuring_artists'] as $f_artiest) {array_push($uniek['artiest'],$f_artiest);}
        foreach($track['Contributing_artists'] as $c_artiest) {array_push($uniek['artiest'],$c_artiest);}
        foreach($track['Remixers'] as $remixers) {array_push($uniek['artiest'],$remixers);}
        foreach($track['Performers'] as $performers) {array_push($uniek['artiest'],$performers);}
        foreach($track['Writers'] as $writers) {array_push($uniek['artiest'],$writers);}
        foreach($track['Composers'] as $composer) {array_push($uniek['composer'],$composer);}
        foreach($track['Lyricists'] as $lyricists) {array_push($uniek['lyricists'],$lyricists);}
        foreach($track['Publishers'] as $publishers) {array_push($uniek['publishers'],$publishers);}
        foreach($track['Producers'] as $producer) {array_push($uniek['producers'],$producer);}
      }  
    }

    //zorgen dat het enkel unieke waarden zijn
    $uniek['label'] = array_unique($uniek['label']);
    $uniek['artiest'] = array_unique($uniek['artiest']);
    $uniek['line'] = array_unique($uniek['line']);
    $uniek['composer'] = array_unique($uniek['composer']);
    $uniek['lyricists'] = array_unique($uniek['lyricists']);
    $uniek['publishers'] = array_unique($uniek['publishers']);
    $uniek['producers'] = array_unique($uniek['producers']);
    
    // Insert the unique values to the databases, if the item doesn't exist.
    $sql = "";
    foreach($uniek['label'] as $label){
      if($label != ""){$sql .= "INSERT IGNORE INTO tbl_label (omschrijving) values ('$label');";}
    }
    foreach($uniek['line'] as $line){
      if($line != ""){$sql .= "INSERT IGNORE INTO tbl_line (naam) values ('$line');";}
    }
    foreach($uniek['artiest'] as $artiest){
      if($artiest != ""){$sql .= "INSERT IGNORE INTO tbl_artiest (omschrijving) values ('$artiest');";}
    }
    foreach($uniek['composer'] as $composer){
      if($composer != ""){$sql .= "INSERT IGNORE INTO tbl_composers (omschrijving) values ('$composer');";}
    }
    foreach($uniek['lyricists'] as $lyricists){
      if($lyricists != ""){$sql .= "INSERT IGNORE INTO tbl_lyricists (omschrijving) values ('$lyricists');";}
    }
    foreach($uniek['publishers'] as $publishers){
      if($publishers != ""){$sql .= "INSERT IGNORE INTO tbl_composers (omschrijving) values ('$publishers');";}
    }  
    foreach($uniek['producers'] as $producer){
      if($producer != ""){$sql .= "INSERT IGNORE INTO tbl_producers (omschrijving) values ('$producer');";}
    }  
    return ExMultipleSql($sql);
  }
}

class clsBedrijf{

  function OphalenDetailsBedrijf($bedrijf_id, $Catalog_voorvoegsel){
    return ReturnFirstRow("SELECT * FROM tbl_bedrijven WHERE bedrijf_id = $bedrijf_id AND verwijderd = 0");
  }

  function OpslaanBedrijf($data){

    $db = ConnectDb();

    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_bedrijven SET Catalog_voorvoegsel = '$Catalog_voorvoegsel', ISRC_voorvoegsel = '$ISRC_voorvoegsel',
      weergavenaam = '$weergavenaam' WHERE bedrijf_id = '$bedrijf_id'" ,$db);
  }

  function BedrijfToevoegen($weergavenaam){
    $db = ConnectDb();

    $weergavenaam = $db->real_escape_string($weergavenaam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_bedrijven(weergavenaam) VALUES('$weergavenaam')" ,$db);
  }

  function OphalenBedrijven(){
    return ResultToArray("SELECT bedrijf_id,weergavenaam FROM tbl_bedrijven where verwijderd = 0;");
  }

  function VerwijderBedrijf($data){
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_bedrijven SET verwijderd = $verwijderd WHERE bedrijf_id = '$bedrijf_id'", $db);
  }
}

class clsLabel {

  function GetLabels($bedrid){
    return ResultToArray("SELECT label_id, omschrijving FROM tbl_label WHERE bedrijf_id = $bedrid AND verwijderd = 0;");
  }

  // insert label 
  function LabelToevoegen($naam, $bedrid){
    $db = ConnectDb();
    $naam = $db->real_escape_string($naam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_label (omschrijving, bedrijf_id) VALUES('$naam', $bedrid)" ,$db);
  }

  function OphalenDetailsLabel($label_id){
    return ReturnFirstRow("SELECT * FROM tbl_label WHERE label_id = $label_id;");
  }

  function VerwijderLabel($data){
      $db = ConnectDb();
      foreach($data as $k=>$v) $$k = $db->real_escape_string($v);
      return ExQuery("UPDATE tbl_label SET verwijderd = $verwijderd WHERE label_id = '$label_id'", $db);
  }

  function OpslaanLabel($data){
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_label SET omschrijving = '$omschrijving' WHERE label_id = $label_id;" ,$db);
  }
}

class clsLine {

  function GetLines(){
    return ResultToArray("SELECT line_id, naam FROM tbl_line WHERE verwijderd = 0;");
  }

  // insert label 
  function LineToevoegen($naam){
    $db = ConnectDb();
    $naam = $db->real_escape_string($naam);
    
    return ReturnLastInsertedId("INSERT INTO tbl_line (naam) VALUES('$naam')" ,$db);
  }

  function OphalenDetailsLine($line_id){
    return ReturnFirstRow("SELECT * FROM tbl_line WHERE line_id = $line_id WHERE verwijderd = 0;");
  }

  function VerwijderLine($data){
      $db = ConnectDb();
      foreach($data as $k=>$v) $$k = $db->real_escape_string($v);
      return ExQuery("UPDATE tbl_line SET verwijderd = $verwijderd WHERE line_id = '$line_id'", $db);
  }

  function OpslaanLine($data){
    $db = ConnectDb();
    foreach($data as $k=>$v) $$k = $db->real_escape_string($v);

    return ExQuery("UPDATE tbl_line SET naam = '$naam' WHERE line_id = $line_id;" ,$db);
  }
}
?>