$(function() {
  $("#labels ul[name=labellist]").on('click', "li", labelSelectLabel);
  $("#selectie label[name=albumToevoegen]").on('click', labelLadenLabels);

})

function labelLadenLabels(){
  
  //Vullen alle list met labels
  $.post(AjaxUrl, { func: "GetLabels", bedrid: $("header select[name=bedrijf] option:selected").val() },
    function(labels) {
        labels = JSON.parse(labels);
        //console.log(labels);
        var html = "";
        $.each(labels, function(k) {
          html += "<li class=\"listitem shadow\" value=\"" + labels[k]['label_id'] + "\">" + labels[k]['omschrijving'] + "</li>";
          
        })
        $("ul[name=labellist]").html(html);
        $("#labels div[name=DetailsLabel] input").val('');
    })
}

function LabelToevoegen() {
    var naam = prompt("Wat is de naam van het label?");

    if (naam === null || naam == "") return;

    $.post(AjaxUrl, { naam: naam, func: "LabelToevoegen", bedrid: $("header select[name=bedrijf] option:selected").val() },
        function(id) {
          if (id == 0) {
              alert("Er is iets fout gegaan bij het toevoegen. Probeer het later opnieuw.");
              return;
          }
          $("#labels ul[name=labellist]").append("<li class='listitem shadow' name=" + id + ">" + naam + "</li>");
          $("#labels ul[name=labellist]>li[name=" + id + "]").trigger('click');
        })
}

function labelSelectLabel() {
  $("#labels ul[name=labellist]>li").removeClass("active");
  $(this).addClass("active");

  $.post(AjaxUrl, { label_id: $(this).val(), func: "OphalenDetailsLabel" },
    function(result) {
      console.log(result);
        var gegevens = JSON.parse(result);
        $.each(gegevens, function(k, v) {
            $("#labels div[name=DetailsLabel]").find("input[name=" + k + "]").val(v);
        })
    })
}

function labelOpslaanLabel() {
  var data = objectifyForm($("#labels div[name=DetailsLabel] input").serializeArray());
  data['label_id'] = $("#labels ul[name=labellist]>li.active").val();
  $.post(AjaxUrl, { data: JSON.stringify(data), func: "OpslaanLabel" },
    function(result) {
      //console.log(result);
      if(result == 1)  $("#labels ul[name=labellist]>li.active").text(data['omschrijving']);
    }
  )
}

function labelVerwijderLabel() {
  var naam = $("#labels ul[name=labellist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
      var data = {};
      data['label_id'] = $("#labels ul[name=labellist]>li.active").val();
      data['verwijderd'] = 1;
      $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderLabel" },
        function(result) {
          console.log(result);
          if (result == 1) {
              $("#labels ul[name=labellist]>li.active").remove();
              $("#labels div[name=DetailsLabel] input").val('');
          }
        })
  }
}

function LadenLabels(){
  var names = ['Label'];
  
  var htmloptions = "";

  $.post(AjaxUrl, { func: "OphalenLabels" },
  function(labels) {
      labels = JSON.parse(labels);
      $.each(labels, function(k, v) {
        htmloptions += "<option value='" + v['label_id'] + "'>" + v['omschrijving'] + "</option>";
    })
    $.each(names,function(k,v){
      html = "<select class=\"form-select\" name=\"" + v + "\">";
      html += htmloptions;
      html += "</select><label>" + v.replace("_"," ") + "</label>";
      $("select[name=" + v + "]").parent("div.form-floating").html(html);
    })   
     
  })
}