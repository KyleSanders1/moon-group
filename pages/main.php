<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/classes.php';

$clsAccount = new clsAccount();
$clsAlbum = new clsAlbum();
$clsRecords = new clsRecords();
$clsCsv = new clsCsv();
$clsBedrijf = new clsBedrijf();
$clsComposer = new clsComposers();
$clsLyricist = new clsLyricists();
?>
<head>
  <script type="text/javascript" src="/jquery/accounts.js"></script>
  <script type="text/javascript" src="/jquery/albums.js"></script>
  <script type="text/javascript" src="/jquery/tracks.js"></script>
  <script type="text/javascript" src="/jquery/tabellen.js"></script>
  <script type="text/javascript" src="/jquery/exportAlbum.js"></script>
  <script type="text/javascript" src="/jquery/csv/jquery.csv.js"></script>
  <script type="text/javascript" src="/jquery/csv/jquery.csv.min.js"></script>  
  <script type="text/javascript" src="/jquery/settings/bedrijven.js"></script> 
  <script type="text/javascript" src="/jquery/settings/artiesten.js"></script> 
  <script type="text/javascript" src="/jquery/settings/composers.js"></script> 
  <script type="text/javascript" src="/jquery/settings/lyricists.js"></script>
  <script type="text/javascript" src="/jquery/settings/label.js"></script>
  <script type="text/javascript" src="/jquery/settings/line.js"></script>
  <script type="text/javascript" src="/jquery/csv/table2csv.js"></script>   

  <script src="https://zogroep.nl/jquery/jQueryDatatable.js"></script>
  <script src="https://zogroep.nl/jquery/Bootstrap5Datatable.js"></script>
  <script src="https://zogroep.nl/jquery/general.js"></script>
  <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- Multiselect selectbox -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@dashboardcode/bsmultiselect@0.6.2/dist/js/BsMultiSelect.min.js"></script>

</head>

<header id="topnav">
  <div class="inner">
      <img class="logo" src="images/logo_white_moon.png" alt="logo">
      <nav role='navigation'>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-floating mb-1">
              <select class="form-select" name="bedrijf" onchange="ChangeMainCompany();">
                <?php 
                $bedrijven = $clsBedrijf -> OphalenBedrijven();
                $bedrijvenoptions= '';
                foreach ($bedrijven as $b){$bedrijvenoptions .="<option value='" . $b['bedrijf_id'] . "'>" . $b['weergavenaam'] . "</option>";}
                echo $bedrijvenoptions;
                ?>
              </select>
              <label>Bedrijf</label>
            </div>
          </div>
          
          <ul class="col-sm-8">
            <i class="fas fa-sign-out-alt pointer me-3 right" onclick="Logout();"></i>
            <i class="fas fa-cogs pointer me-3 right"  onclick="$('#menu-items').toggle(600)"></i>     
            <i class="fas fa-home pointer me-3 right" name="t" onclick="$('#main>div').hide();$('#home').show();$('#menu-items>a').removeClass('active');$('#menu-items').hide(600);"></i>               
          </ul>
        </div>
      </nav>  
  </div>
  <div id="menu-items" class="test">
    <a name="accounts">Accounts</a>
    <a name="artiesten">Artiesten</a>
    <a name="bedrijven">Bedrijven</a>
    <a name="composers">Composers</a>
    <a name="lyricists">Lyricists</a>
    <a name="labels">Labels</a>
    <a name="lines">Lines</a>
  </div>
</header>

<div id="main">
    <br/>
    <?php
    echo "<div id=\"home\">";
    include "pages/selectie.php";
    include "pages/tabel.php";
    echo "</div>";
    include "pages/toevoegenAlbum.php";
    include "pages/toevoegenTrack.php";
    include "pages/settings/account.php";
    include "pages/settings/bedrijven.php";
    include "pages/settings/artiesten.php";
    include "pages/settings/composers.php";
    include "pages/settings/lyricists.php";
    include "pages/settings/labels.php";
    include "pages/settings/line.php";
    include "modals/detailsTrack.php";
    include "modals/vertalingAlbumTrack.php";
    include "modals/copyToTracks.php";
    ?>   
</div>
