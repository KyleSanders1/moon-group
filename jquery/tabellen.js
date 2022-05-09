var isDblclick, timeoutTiming;
var clickTimeout, dblclickTimeout;
isDblclick = false;
timeoutTiming = 500;

$(function() {
  sessionStorage.clear();
  LadenTabelAlbums();
  $("#TotalTable>div[name=albums]").on("click", "tbody>tr", selecteerAlbumExport);
  $("#TotalTable>div[name=albums]").on("dblclick", "tbody>tr", LadenTabelTracks);
})

function LadenTabelAlbums() {
  $("#home").show();
    $("#TotalTable>div[name=albums]").html('');
    $("#TotalTable>div[name=tracks]").hide();
    $.post(AjaxUrl, {
            van: $("#selectie").find("input[name=van]").val(),
            tot: $("#selectie").find("input[name=tot]").val(),
            func: "getAlbumTable",   
            bedr_id: $("header select[name=bedrijf] option:selected").val()
        },
        function(rows) {
            var html = "<table name=\"albums\" class=\"table table-striped table-hover\">";
            html += rows;
            html += "</table";
            $("#TotalTable>div[name=albums]").html(html);
            $("#TotalTable>div[name=albums] table").DataTable({
              "order": [[ 5, "asc" ]],
              "iDisplayLength": 100
            });
            $("#TotalTable>div[name=albums]").show(600);
            ColorRowsExport();
            
        })
}

//Als er op een album regel gedubbelklikt wordt, tabel met albums verbergen en de tracks van het album laten zien
function LadenTabelTracks() {
    isDblclick = true;
    clearTimeout(dblclickTimeout);
    dblclickTimeout = setTimeout(function() {
        isDblclick = false;
    }, timeoutTiming);
    $("#TotalTable div[name=tabeltracks]>div[name=DetailsAlbum]").remove();
    $.get('pages/details/detailsAlbum.php',function(response){ 
      //console.log(response);
      $('div[name=tabeltracks]').prepend(response); 
      LadenArtiesten();
      LadenLines();
      LadenLabels();
      LadenComposers();
      ToonDetailsAlbum();
     });
    
    //Knop album toevoegen verbergen en track toevoegen tonen
    $("#selectie label[name=albumToevoegen]").hide();
    $("#selectie label[name=trackToevoegen]").show(600);

    var albumRow = $(this);
    var albumId = albumRow.attr("name");
    sessionStorage.setItem("selectedAlbum", albumId);
    tabellenVullenTabelTracks();

}

function tabellenVullenTabelTracks() {
    albumId = sessionStorage.getItem("selectedAlbum");
    $("#TotalTable div[name=tracktabel]").html('');
    
    $.post(AjaxUrl, { func: "GetTrackRows", albumId: albumId },
        function(rows) {
            var html = "<table name=\"tracks\" class=\"table table-striped table-hover\">";
            html += rows;
            html += "</table";
            $("#TotalTable div[name=tracktabel]").html(html);
            $("#TotalTable div[name=tracktabel] table").DataTable({
              "order": [[ 1, "asc" ]],
              "iDisplayLength": 100
            });
            $("#TotalTable>div[name=albums]").hide();
            $("#TotalTable div[name=tracks]").show(600);

        })
}

//Details van een album laden en tonen na selectie van een album
function ToonDetailsAlbum() {
  albumId = sessionStorage.getItem("selectedAlbum");
  $.post(AjaxUrl, { func: "CheckLockedStatusAlbum", album_id: albumId}, function(status) {
    //console.log(status);
    $el = $("#TotalTable div[name=DetailsAlbum] div[name=DetailsAlbum]");
    $.post(AjaxUrl, {func: "CheckUserRole"}, function(role){
      // console.log(role);
      if(role != 1){
        $el.find("input[name=Locked]").parent().hide();
      }
    })
    if (status == 1){
      $el.find("input[name=Locked]").prop("checked", "checked");
    }else {
      $el.find("input[name=Locked]").prop("checked", "");
    }

      $.post(AjaxUrl, { func: "GetAlbumDetails", album_id: albumId }, function(result) {
          var details = JSON.parse(result);
          $el = $("#TotalTable div[name=DetailsAlbum] div[name=DetailsAlbum]");
          $.each(details, function(k, v) {
            //console.log( "name: "+ k + " value: " + v)
              $el.find("select[name=" + k + "]").val(v);
              $el.find("input[name=" + k + "]").val(v);
          })
          $el.find("input[name=Parental_advisory]").prop("checked", details['Parental_advisory'] == 1);

          //artiesten
          $.each(details['Album_primary_artists'], function(k, v) {
              $el.find("select[name=Album_primary_artists] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
              var index = $el.find("select[name=Album_primary_artists] option[value=" + v['artiest_id'] + "]").index();
              $el.find("select[name=Album_primary_artists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
          })
          $.each(details['Album_featuring_artists'], function(k, v) {
              $el.find("select[name=Album_featuring_artists] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
              var index = $el.find("select[name=Album_featuring_artists] option[value=" + v['artiest_id'] + "]").index();
              $el.find("select[name=Album_featuring_artists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
          })
          console.log(details);

      })
  })
}

//Als je op de knop terug klikt bij de track tabel, teruggaan naar de album tabel
function tabellenTerugNaarAlbum() {
    //Verbergen track toevoegen, tonen album toevoegen
    sessionStorage.removeItem("selectedAlbum");
    $("#selectie label[name=trackToevoegen]").hide();
    $("#selectie label[name=albumToevoegen]").show(600);

    $('#TotalTable div[name=tracks]').hide();
    $('#TotalTable>div[name=albums]').show(600);
}

function ColorRowsExport() {
    var albumsExport = JSON.parse(sessionStorage.getItem("albumsExport"));
    if (albumsExport == null) return;
    if (albumsExport.length != 0) $("#Export").css("background-color", "lightgreen");

    $.each(albumsExport, function(k, v) {
        $("#TotalTable>div[name=albums]").find("tr[name=" + v + "]").addClass("exportAlbum");
    })
}

//selecteerAlbumsVoorExport
function selecteerAlbumExport() {

    clearTimeout(clickTimeout);

    var $album = $(this);
    clickTimeout = setTimeout(function() {
        if (!isDblclick) {
            var albumsExport = JSON.parse(sessionStorage.getItem("albumsExport"));
            var album_id = $album.attr("name");
            if ($album.hasClass("exportAlbum")) {
                $album.removeClass("exportAlbum");
                albumsExport.splice($.inArray(album_id, albumsExport), 1);
                sessionStorage.setItem("albumsExport", JSON.stringify(albumsExport));
            } else {
                $album.addClass("exportAlbum");
                if (albumsExport === null) albumsExport = [];
                albumsExport[albumsExport.length] = album_id;
                sessionStorage.setItem("albumsExport", JSON.stringify(albumsExport));
            }
            if (albumsExport.length != 0) {
            $("#Export").css("background-color", "lightgreen");
            $("#Delete").css("background-color", "lightcoral");
            }
            else {
              $("#Export").css("background-color", "");
              $("#Delete").css("background-color", "");
            }
        }
    }, timeoutTiming);
}

function LadenExportTabel() {
    $("#TotalTable div[name=ExportTable]").html('');
    var albumRow = $(this);
    var albumId = albumRow.attr("name");

    $.post(AjaxUrl, { func: "BuildTable", albumId: albumId },
        function(rows) {
            var html = "<table name=\"ExportTable\" class=\"table table-striped table-hover\">";
            html += rows;
            html += "</table";
            $("#TotalTable div[name=ExportTable]").html(html);
            $("#TotalTable div[name=ExportTable] table").DataTable();

        })
}