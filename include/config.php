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

session_start();
setlocale(LC_MONETARY, 'it_IT.utf8');

define('FTP_HOST','applicaties.zogroep.nl');
define('FTP_USER', '');
define('FTP_PASS', '');

define('DB_SERVER', 'applicaties.zogroep.nl');
define('DB_USERNAME', 'moongroup');
define('DB_PASSWORD', 'mhM3S8k7KvgpthG7VKQwjTFCGTDMuwTYLtu35URW');
define('DB_DATABASE', 'moon-group');

function ConnectFtp(){
  // Connect to the ftp server 
  $host  = 'applicaties.zogroep.nl';
  $usr = "Zobdoc";   
  $enc = new clsEncryption();
  $pass = $enc -> decrypt("yDRdhl%2BAin1iMORBfW7Z/A==");

  $f_conn = ftp_connect($host) or 
      die("Could not connect to ${host}"); 
        
  // Authenticating to ftp server      
  $login = ftp_login($f_conn, $usr, $pass);  
  ftp_pasv($f_conn, true);
  return $f_conn;   
}


function ConnectDb(){
   $db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
  
  if ($db->connect_errno) {
    printf(" Connect failed: %s\n", $db->connect_error);
    exit();
  }
  mysqli_set_charset($db, 'utf8');
  return $db;
}

function ExQuery($sql, $db, $close = true){
  
  if(is_null($db)) return false;
  if ($db->query($sql)){
    if($close) $db->close();
    return true;
  } else {
    if($close) $db->close();
    return $sql;
  }
}

function ReturnLastInsertedId($sql,$db = null, $close = true){
  if(is_null($db)) $db = ConnectDb();
  if(is_null($db)) return 0;

  $id = 0;
  if(mysqli_query($db,$sql)){
      $id= mysqli_insert_id($db);
  } else return $sql;

  if ($close) $db->close();
  return $id;
}

function ExMultipleSql($sql){
  $db = ConnectDb();
  if(is_null($db)) return 0;
  if(mysqli_multi_query($db,$sql)){
    $db->close();
    return true;
  } else{
    return $db->error;
    $db->close();
  }
}

function GetResult($sql){
  $db = ConnectDb();
  if(is_null($db)) return false;
  $result = $db->query($sql);
  $db->close();
  return $result;
}

function ReturnFirstRow($sql){
  $result = GetResult($sql);
  if(!$result) return false;

  if($result->num_rows > 0) return $result -> fetch_assoc();
  else return array();
}

function ResultToArray($sql){
  $result = GetResult($sql);
  if(!$result) return false;

  $arr = array();
  if($result->num_rows > 0){
    while ($row = $result -> fetch_assoc()) {
      array_push($arr,$row);
    }
  }
  return $arr;
}

function GetSingleResult($sql,$column ){
  $db = ConnectDb();
  if(is_null($db)) return false;
  $data = $db->query($sql);  
  $r = $data-> fetch_assoc();
  $result = $r[$column];
  $db->close();
  return $result;
}
?>