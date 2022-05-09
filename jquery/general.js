var AjaxUrl = window.location.origin + "/include/AjaxFunctions.php";

$(function() {
    $("#menu-items>a").on("click", togglemenu);
    $("select.multiple").bsMultiSelect();
    $('select.multi').selectpicker();
    ChangeMainCompany();
    
})

function ChangeMainCompany(){
  var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
  //console.log(bedrijfsid);

  labelLadenLabels(bedrijfsid);
  accountsLadenUsers(bedrijfsid);
  albumsChangeCompany();
  LadenLines();
  LadenLabels();
  LadenTabelAlbums();
  $("#main>div:not(#home)").hide();

  $("#selectie label[name=albumToevoegen]").show();
  $("#selectie label[name=trackToevoegen]").hide();
}

function Logout() {
    $.post(AjaxUrl, { func: "Logout" }, function() { window.location.reload(); })
}

function togglemenu() {
    var $el = $(this).attr("name");
    //console.log($el);
    $("#menu-items>a").removeClass("active");
    $(this).addClass("active");
    $("#main>div").hide();
    $("#" + $el).show();
}

function ExportToTable(callback) {
  if (typeof(callback) == "function") {
    //Checks whether the file is a valid csv file 
    if ($("#csvfile").val().slice(-3).toLowerCase() == "csv") {

      //Checks whether the browser supports HTML5    
      if (typeof(FileReader) != "undefined") {
        var csv = {};
        var reader = new FileReader();
        reader.onload = function(e) {
          //Splitting of Rows in the csv file    
          var csvrows = e.target.result.split("\n");
          for (var i = 0; i < csvrows.length; i++) {
            if (csvrows[i] != "") {

              var csvcols = csvrows[i].split(",");
              csv[i] = csvcols;
            }
          }
          callback(csv);
        }
        reader.readAsText($("#csvfile")[0].files[0]);
      } else {
          alert("Sorry! Uw browser ondersteund geen HTML5");
      }
    } else {
        alert("Upload a.u.b. een CSV bestand!");
    }
  }
}

function GetTracksCsv() {
    ExportToTable(function(result) {
        var tracks = {};
        var titels = result[0];
        for (r = 1; r < Object.keys(result).length; r++) {
            var track = {};
            for (i = 0; i <= titels.length - 1; i++) {

                track[titels[i]] = result[r][i].replace(/(\r\n|\n|\r)/gm, "");
            }
            track['oreleasedate'] = result[r][titels.length - 1].replace(/(\r\n|\n|\r)/gm, "");
            tracks[r] = track;
        }
        $.post(AjaxUrl, { func: "InsertDataCsv", data: JSON.stringify(tracks), bedrijfsid: $("header select[name=bedrijf] option:selected").val() },
            function(result) {
                console.log(result);
                LadenTabelAlbums();
            })
    })
}