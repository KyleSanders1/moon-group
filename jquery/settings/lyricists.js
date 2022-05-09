$(function() {
  $("#lyricists").on("click", "ul[name=lyricistlist]>li", SelecteerLyricist)
  //$("#selectie label[name=trackToevoegen]").on('click', LadenLyricists);

})

function SelecteerLyricist() {
  $el = $('div[name=DetailsLyricist]');
  $("#lyricists ul[name=lyricistlist]>li").removeClass('active');
  $(this).addClass("active");
  var lyricists_id = $(this).attr("name");
  $.post(AjaxUrl, { lyricists_id: lyricists_id, func: "OphalenDetailsLyricist" }, function(result) {
      var lyricist = JSON.parse(result);
      $.each(lyricist, function(key, value) {
          $el.find("input[name=" + key + "]").val(value);
      })
  })
}

function OpslaanLyricist() {
  var data = objectifyForm($('div[name=DetailsLyricist] input').serializeArray());
  data['lyricists_id'] = $("#lyricists ul[name=lyricistlist]>li.active").attr("name");
  console.log(data);
  $.post(AjaxUrl, {data: JSON.stringify(data),func: "LyricistOpslaan"},
  function(result){if(result != 1) {console.log(result); alert("Er is iets misgegaan bij het opslaan van de gegevens.");}})
}

function LyricistToevoegen() {
  var naam = prompt("Wat is de naam van de lyricist?");
 
  if (naam === null || naam == "") return;

  $.post(AjaxUrl, { naam: naam, func: "LyricistToevoegen" },
      function(id) {
          $("#lyricists ul[name=lyricistlist]").append("<li class='listitem shadow' name=" + id + ">" + naam + "</li>");
          $("#lyricists ul[name=lyricistlist]>li[name=" + id + "]").trigger('click');
          console.log(id);
  })

}

function LyricistVerwijderen() {
  var naam = $("#lyricists ul[name=lyricistlist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
      var data = {};
      data['lyricists_id'] = $("#lyricists ul[name=lyricistlist]>li.active").attr("name");
      data['verwijderd'] = 1;
      console.log(data['lyricists_id']);
      $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderLyricist" },
          function(result) {
              console.log(result);
              if (result == 1) {
                  $("#lyricists ul[name=lyricistlist]>li.active").remove();
                  $("#lyricists div[name=DetailsLyricist] input").val('');
              }
      })
  }
}


// function LadenLyricists(){

//   var names = [];
  
//   var htmloptions = "";

//   $.post(AjaxUrl, { func: "OphalenLyricists" },
//   function(lyricists) {
//       lyricists = JSON.parse(lyricists);
//       $.each(lyricists, function(k, v) {
//         htmloptions += "<option value='" + v['lyricists_id'] + "'>" + v['omschrijving'] + "</option>";
//     })
//     $.each(names,function(k,v){
//       html = "<select class=\"form-select multiple\" name=\"" + v + "\" multiple=\"multiple\">";
//       html += htmloptions;
//       html += "</select><label>" + v.replace("_"," ") + "</label>";
//       $("select[name=" + v + "]").parent("div.dashboardcode-bsmultiselect").html(html);
//       $("select[name=" + v + "]").bsMultiSelect();
//     })        
//   })
// }