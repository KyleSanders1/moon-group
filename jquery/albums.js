$(function() {
    $("#albums").on("click", "ul[name=albumlist]>li", SelecteerAlbum)
    $("#toevoegenAlbum input[name=Locked]").parent().hide();

})

function albumsChangeCompany() {
    GetNextCatalogNr();
    GetListBoxes();
}

function albumsFillFieldsAlbum() {
    GetNextUPC();
    GetNextCatalogNr();
    GetListBoxes();
}
//Automatisch vullen UPC
function GetNextUPC() {
    $.post(AjaxUrl, { func: "GetNextUPC" },
        function(upc) {
            $("#toevoegenAlbum input[name=UPC]").val(upc.trim());
        })
}

//Vullen van de select boxen
function GetListBoxes() {
    var bedrijfsid = $("header select[name=bedrijf] option:selected").val();

    //Labels
    $.post(AjaxUrl, { func: "GetLabels", bedrid: bedrijfsid },
        function(labels) {
            labels = JSON.parse(labels);
            var html;
            $.each(labels, function(k) {
                html += "<option value='" + labels[k]['label_id'] + "'>" + labels[k]['omschrijving'] + "</option>";
            })
            $("select[name=label]").html(html);
        })
}

//Automatisch vullen Catalog number
function GetNextCatalogNr() {
    var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
    //console.log(bedrijfsid);
    $.post(AjaxUrl, { bedrijfsid: bedrijfsid, func: "GetNextCatalogNr" },
        function(catnr) {
          catnr.trim();
            //console.log(catnr);
            $("#toevoegenAlbum input[name=Catalog_number]").val(catnr.trim());
        })
}

function SelecteerAlbum() {
    $el = $('div[name=DetailsAlbum]');
    $el.show(600);


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
    var album_id = $(this).attr("name");
    $.post(AjaxUrl, { album_id: album_id, func: "GetAlbumDetails" }, function(result) {

        var album = JSON.parse(result);
        album = album[0];
        $.each(album, function(key, value) {
            $el.find("input[name=" + key + "]").val(value);
        })

        
    })
}

function AlbumToevoegen() {
  var data = objectifyForm($('div[name=DetailsAlbum] :input').serializeArray());

  data['Album_primary_artists'] = [];
  $.each($("#toevoegenAlbum select[name=Album_primary_artists]").parent().find("li.badge>span"), function() {
      data['Album_primary_artists'].push($(this).text());
  })

  data['Album_featuring_artists'] = [];
  $.each($("#toevoegenAlbum select[name=Album_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Album_featuring_artists'].push($(this).text());
  })

  data['Bedrijf_id'] = $("header select[name=bedrijf] option:selected").val();


  if ($("#toevoegenAlbum input[name=UPC]").parent().hasClass("fout")) {
    alert("Vul een niet bestaande UPC in.");
    return;
  } 
  if ($("#toevoegenAlbum input[name=Catalog_number]").parent().hasClass("fout")) {
      alert("Vul een niet bestaande catalogus nummer in.");
      return;
    } 
else {
  
      $.post(AjaxUrl, { func: "GetNextUPC" },
        function(upc) {
          $("#toevoegenAlbum input[name=UPC]").val(upc);
          var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
          $.post(AjaxUrl, { bedrijfsid: bedrijfsid, func: "GetNextCatalogNr" },
            function(catnr) {
                //console.log(catnr);
                $("#toevoegenAlbum input[name=Catalog_number]").val(catnr);
            $.post(AjaxUrl, { data: JSON.stringify(data), func: "InsertDetailsAlbum" }, function(id) {
              console.log(id);
              data['album_id'] = id;
              $('#toevoegenAlbum').hide();
              LadenTabelAlbums();
            })
          })
        })
      
      }
}

function albumsOpslaanWijziging($el) {
    console.log($el);

    var data = objectifyForm($('#TotalTable div[name=DetailsAlbum] :input').serializeArray());
    data['album_id'] = sessionStorage.getItem("selectedAlbum")
    var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
    data['Bedrijf_id'] = bedrijfsid;
  
    data['Album_primary_artists'] = [];
    $.each($el.find("select[name=Album_primary_artists]").parent().find("li.badge>span"), function() {
        data['Album_primary_artists'].push($(this).text());
    })
  
    data['Album_featuring_artists'] = [];
    $.each($el.find("select[name=Album_featuring_artists]").parent().find("li.badge>span"), function() {
        data['Album_featuring_artists'].push($(this).text());
    })
    console.log(data);

    $.post(AjaxUrl, { data: JSON.stringify(data), func: "OpslaanAlbum" },
        function(result) {
          console.log(result);
          LadenTabelAlbums();
            if (result != 1) {
                console.log(result);
                alert("Er is iets misgegaan bij het opslaan van de gegevens. \nWijzigen wellicht niet mogelijk wegens locked status.");
            }
        })
}

function albumsAlbumKopie(album_id){
  $el = $('#TotalTable div[name=DetailsAlbum]');

  var data = objectifyForm($('#TotalTable div[name=DetailsAlbum] :input').serializeArray());
  data['album_id'] = album_id;
  var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
  data['Bedrijf_id'] = bedrijfsid;

  data['Album_primary_artists'] = [];
  $.each($el.find("select[name=Album_primary_artists]").parent().find("li.badge>span"), function() {
      data['Album_primary_artists'].push($(this).text());
  })

  data['Album_featuring_artists'] = [];
  $.each($el.find("select[name=Album_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Album_featuring_artists'].push($(this).text());
  })
 
  $.post(AjaxUrl, { func: "GetNextUPC" },
    function(upc) {
     data['UPC']=upc;

      $.post(AjaxUrl, { bedrijfsid: bedrijfsid, func: "GetNextCatalogNr" },
        function(catnr) {
          data['Catalog_number']=catnr;

          $.post(AjaxUrl, { func: "InsertDetailsAlbum", data: JSON.stringify(data) },
            function() { LadenTabelAlbums();})
        })
    })
}

function VerwijderAlbums(){
  var albums = JSON.parse(sessionStorage.getItem("albumsExport"));
  if (albums.length == 0) {
    alert("Er is nog geen album geselecteerd om te verwijderen");
    return;
  }
  if (confirm("Weet je zeker dat je het wilt verwijderen?")) {
    $.post(AjaxUrl, { albums: sessionStorage.getItem("albumsExport"), func: "VerwijderAlbums" },
    function(result) { 
      console.log(result); 
      if (result = 1){
        sessionStorage.removeItem('albumsExport');
        $("#Export").css("background-color", "");
        $("#Delete").css("background-color", "");
        LadenTabelAlbums();
      }else {
        alert("Er is iets misgegaan bij het verwijderen van de gegevens. \nVerwijderen wellicht niet mogelijk wegens locked status.");
        LadenTabelAlbums();
      }
    })
  }
}

function CheckUPC(){
  $el = $("#toevoegenAlbum input[name=UPC]").parent();
  $el.removeClass("fout");
  console.log($("#toevoegenAlbum input[name=UPC]").val());
  $.post(AjaxUrl, { func: "CheckUPC", upc: $("#toevoegenAlbum input[name=UPC]").val() },
  function(upc) {
      console.log(upc);
      if(upc >= 1){
        $("#toevoegenAlbum input[name=UPC]").css("background-color", "lightcoral");
        $el.addClass("fout");
      } else {
        $("#toevoegenAlbum input[name=UPC]").css("background-color", "");
        $el.removeClass("fout");
      }
  })
}

function CheckCatNum(){
  $el = $("#toevoegenAlbum input[name=Catalog_number]").parent();
  console.log($("#toevoegenAlbum input[name=Catalog_number]").val());
  $el.addClass("fout");
  $.post(AjaxUrl, { func: "CheckCatNum", catNum: $("#toevoegenAlbum input[name=Catalog_number]").val() },
  function(catNum) {
      console.log(catNum);
      if(catNum >= 1){
        $("#toevoegenAlbum input[name=Catalog_number]").css("background-color", "lightcoral");
        $el.addClass("fout");
      } else {
        $("#toevoegenAlbum input[name=Catalog_number]").css("background-color", "");
        $el.removeClass("fout");
      }
  })
}