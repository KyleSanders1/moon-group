$(function() {
  $("#lines ul[name=linelist]").on('click', "li", lineSelectLine)
 //$("#selectie label[name=albumToevoegen]").on('click', LadenLines);

})

function lineLadenLines(){
  //Vullen alle list met labels
  $.post(AjaxUrl, { func: "GetLines" },
    function(lines) {
        lines = JSON.parse(lines);
        //console.log(lines);
        var html = "";
        $.each(lines, function(k) {
          html += "<li class=\"listitem shadow\" value=\"" + lines[k]['line_id'] + "\">" + lines[k]['omschrijving'] + "</li>";
          
        })
        $("ul[name=linelist]").html(html);
        $("#lines div[name=DetailsLine] input").val('');
    })
}

function LineToevoegen() {
  var naam = prompt("Wat is de naam van de line?");

  if (naam === null || naam == "") return;

  $.post(AjaxUrl, { naam: naam, func: "LineToevoegen"},
      function(id) {
          if (id == 0) {
              alert("Er is iets fout gegaan bij het toevoegen. Probeer het later opnieuw.");
              return;
          }
          $("#lines ul[name=linelist]").append("<li class='listitem shadow' value=" + id + ">" + naam + "</li>");
          $("#lines ul[name=linelist]>li[name=" + id + "]").trigger('click');

      })
}

function lineSelectLine() {
  $("#lines ul[name=linelist]>li").removeClass("active");
  $(this).addClass("active");

  $.post(AjaxUrl, { line_id: $(this).val(), func: "OphalenDetailsLine" },
      function(result) {
        //console.log(result);
          var gegevens = JSON.parse(result);
          $.each(gegevens, function(k, v) {
              $("#lines div[name=DetailsLine]").find("input[name=" + k + "]").val(v);
          })
      })
}

function lineOpslaanLine() {
  var data = objectifyForm($("#lines div[name=DetailsLine] input").serializeArray());
  data['line_id'] = $("#labels ul[name=linelist]>li.active").val();
  $.post(AjaxUrl, { data: JSON.stringify(data), func: "OpslaanLine" },
    function(result) {
      if(result == 1)  $("#lines ul[name=linelist]>li.active").text(data['omschrijving']);

    }
  )
}

function lineVerwijderLine() {
  var naam = $("#lines ul[name=linelist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
      var data = {};
      data['line_id'] = $("#lines ul[name=linelist]>li.active").val();
      data['verwijderd'] = 1;
      $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderLine" },
          function(result) {
            console.log(result);
            if (result == 1) {
                $("#lines ul[name=linelist]>li.active").remove();
                $("#lines div[name=DetailsLine] input").val('');
            }
          })
  }
}

function LadenLines(){
  var names = ['Cline_name', 'Pline_name'];
  
  var htmloptions = "";

  $.post(AjaxUrl, { func: "OphalenLines" },
  function(lines) {
      lines = JSON.parse(lines);
      $.each(lines, function(k, v) {
        htmloptions += "<option value='" + v['line_id'] + "'>" + v['naam'] + "</option>";
    })
    $.each(names,function(k,v){
      html = "<select class=\"form-select\" name=\"" + v + "\">";
      html += htmloptions;
      html += "</select><label>" + v.replace("_"," ") + "</label>";
      $("select[name=" + v + "]").parent("div.form-floating").html(html);
    })   
     
  })
}