

$(function() {
  $("#artiesten ul[name=artiestenlist]").on('click', "li", artiestenSelectArtiest);
  $("#selectie label[name=albumToevoegen]").on('click', LadenArtiesten);
  $("#selectie label[name=trackToevoegen]").on('click', LadenArtiesten);

  LadenArtiesten();
})

function artiestenArtiestToevoegen() {
  var naam = prompt("Wat is de naam van de artiest?");

  if (naam === null || naam == "") return;

  $.post(AjaxUrl, { naam: naam, func: "ArtiestToevoegen" },
    function(id) {
        $("#artiesten ul[name=artiestenlist]").append("<li class='listitem shadow' value=" + id + ">" + naam + "</li>");
        $("#artiesten ul[name=artiestenlist]>li[name=" + id + "]").trigger('click');
        console.log(id);
        $.post(AjaxUrl, { artiest_id: $(this).val(), func: "OphalenDetailsArtiest" },
          function(result) {
            console.log(result);
        })
    })
}

function artiestenSelectArtiest() {
  $("#artiesten ul[name=artiestenlist]>li").removeClass("active");
  $(this).addClass("active");

  $.post(AjaxUrl, { artiest_id: $(this).val(), func: "OphalenDetailsArtiest" },
    function(result) {
      var gegevens = JSON.parse(result);
      $.each(gegevens, function(k, v) {
          $("#artiesten").find("input[name=" + k + "]").val(v);
      })
    })
}

function artiestenVerwijderArtiest() {
  var naam = $("#artiesten ul[name=artiestenlist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
    var data = {};
    data['artiest_id'] = $("#artiesten ul[name=artiestenlist]>li.active").val();
    data['verwijderd'] = 1;
    $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderArtiest" },
      function(result) {
        console.log(result);
        if (result == 1) {
          $("#artiesten ul[name=artiestenlist]>li.active").remove();
          $("#artiesten div[name=DetailsArtiest] input").val('');
        }
      })
  }
}

function artiestenOpslaanArtiest() {
  var data = objectifyForm($("#artiesten div[name=DetailsArtiest] input").serializeArray());
  data['artiest_id'] = $("#artiesten ul[name=artiestenlist]>li.active").val();
  data['composer_id'] = $("#artiesten select[name=Composer]").val();
  $.post(AjaxUrl, { data: JSON.stringify(data), func: "OpslaanArtiest" },
    function(result) {
        console.log(result);
    })
}

function LadenArtiesten(){

  var names = ['Album_primary_artists', 'Album_featuring_artists','Track_primary_artists','Track_featuring_artists', 'Contributing_artists', 'Remixers', 'Performers'];
  
  var htmloptions = "";

  $.post(AjaxUrl, { func: "OphalenArtiesten" },
  function(artiesten) {
      artiesten = JSON.parse(artiesten);
      //console.log(artiesten);
      $.each(artiesten, function(k, v) {
        htmloptions += "<option value='" + v['artiest_id'] + "'>" + v['omschrijving'] + "</option>";
    })
    $.each(names,function(k,v){
      //console.log(v);
      html = "<select class=\"form-select multiple\" name=\"" + v + "\" multiple=\"multiple\">";
      html += htmloptions;
      html += "</select><label>" + v.replace(/_/g," ") + "</label>";
      $("select[name=" + v + "]").parent("div.dashboardcode-bsmultiselect").html(html);
      $("select[name=" + v + "]").bsMultiSelect();
    })        
  })
}