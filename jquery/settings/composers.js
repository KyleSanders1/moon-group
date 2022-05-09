$(function() {
  $("#composers").on("click", "ul[name=composerlist]>li", SelecteerComposer)
  $("#selectie label[name=trackToevoegen]").on('click', LadenComposers);
  LadenComposers();
})

function SelecteerComposer() {
  $el = $('div[name=DetailsComposer]');
  $("#composers ul[name=composerlist]>li").removeClass('active');
  $(this).addClass("active");
  var composer_id = $(this).attr("name");
  $.post(AjaxUrl, { composer_id: composer_id, func: "OphalenDetailsComposer" }, function(result) {
      var composer = JSON.parse(result);
      $.each(composer, function(key, value) {
          $el.find("input[name=" + key + "]").val(value);
      })
  })
}

function OpslaanComposer() {
  var data = objectifyForm($('div[name=DetailsComposer] input').serializeArray());
  data['composer_id'] = $("#composers ul[name=composerlist]>li.active").attr("name");
  console.log(data);
  $.post(AjaxUrl, {data: JSON.stringify(data),func: "ComposerOpslaan"},
  function(result){if(result != 1) {console.log(result); alert("Er is iets misgegaan bij het opslaan van de gegevens.");}})
}

function ComposerToevoegen() {
  var naam = prompt("Wat is de naam van de composer?");

  if (naam === null || naam == "") return;

  $.post(AjaxUrl, { naam: naam, func: "ComposerToevoegen" },
      function(id) {
          $("#composers ul[name=composerlist]").append("<li class='listitem shadow' name=" + id + ">" + naam + "</li>");
          $("#composers ul[name=composerlist]>li[name=" + id + "]").trigger('click');
          console.log(id);
  })

}

function ComposerVerwijderen() {
  var naam = $("#composers ul[name=composerlist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
      var data = {};
      data['composer_id'] = $("#composers ul[name=composerlist]>li.active").attr("name");
      data['verwijderd'] = 1;
      console.log(data['composer_id']);
      $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderComposer" },
        function(result) {
          console.log(result);
          if (result == 1) {
              $("#composers ul[name=composerlist]>li.active").remove();
              $("#composers div[name=DetailsComposer] input").val('');
          }
        })
  } 
}

function LadenComposers(){

  var names = ['Composers', 'Writers', 'Publishers', 'Lyricists'];
  
  var htmloptions = "";

  $.post(AjaxUrl, { func: "OphalenComposers" },
  function(composers) {
      composers = JSON.parse(composers);
      $.each(composers, function(k, v) {
        htmloptions += "<option value='" + v['composer_id'] + "'>" + v['omschrijving'] + "</option>";
    })
    $.each(names,function(k,v){
      html = "<select class=\"form-select multiple\" name=\"" + v + "\" multiple=\"multiple\">";
      html += htmloptions;
      html += "</select><label>" + v.replace(/_/g," ") + "</label>";
      $("select[name=" + v + "]").parent("div.dashboardcode-bsmultiselect").html(html);
      $("select[name=" + v + "]").bsMultiSelect();
    })        
  })
}