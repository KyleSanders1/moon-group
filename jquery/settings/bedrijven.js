$(function() {
    $("#bedrijven ul[name=bedrijvenlist]").on('click', "li", bedrijvenSelectBedrijf);

})

function bedrijvenBedrijfToevoegen() {
    var weergavenaam = prompt("Wat is de weergavenaam van het bedrijf?");

    if (weergavenaam === null || weergavenaam == "") return;

    console.log(weergavenaam);
    $.post(AjaxUrl, { weergavenaam: weergavenaam, func: "BedrijfToevoegen" },
      function(id) {
        id = id.trim();
        $("#bedrijven ul[name=bedrijvenlist]").append("<li class='listitem shadow' value=" + id + ">" + weergavenaam + "</li>");
        $("#bedrijven ul[name=bedrijvenlist]>li[name=" + id + "]").trigger('click');
        $("select[name=bedrijf]").append("<option value='" + id + "'>" + weergavenaam + "</option>");
        console.log(id);
      })
}

function bedrijvenSelectBedrijf() {
    $("#bedrijven ul[name=bedrijvenlist]>li").removeClass("active");
    $(this).addClass("active");

    $.post(AjaxUrl, { bedrijf_id: $(this).val(), func: "OphalenDetailsBedrijf" },
      function(result) {
        var gegevens = JSON.parse(result);
        $.each(gegevens, function(k, v) {
            $("#bedrijven").find("input[name=" + k + "]").val(v);
        })
      })
}

function bedrijvenVerwijderBedrijf() {
  var naam = $("#bedrijven ul[name=bedrijvenlist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
    var data = {};
    data['bedrijf_id'] = $("#bedrijven ul[name=bedrijvenlist]>li.active").val();
    data['verwijderd'] = 1;
    $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderBedrijf" },
      function(result) {
        console.log(result);
        if (result == 1) {
          console.log(naam);
            $("#bedrijven ul[name=bedrijvenlist]>li.active").remove();
            $("#bedrijven div[name=DetailsBedrijf] input").val('');
            console.log("header select[name=bedrijf] option[value=" + data['bedrijf_id'] + "]");
            $("header select[name=bedrijf] option[value=" + data['bedrijf_id'] + "]").remove(); //"<option value='" + data['bedrijf_id'] + "'>" + naam + "</option>"
        }
      })
  }
}

function bedrijvenOpslaanBedrijf() {
    var data = objectifyForm($("#bedrijven div[name=DetailsBedrijf] input").serializeArray());
    data['bedrijf_id'] = $("#bedrijven ul[name=bedrijvenlist]>li.active").val();
    $.post(AjaxUrl, { data: JSON.stringify(data), func: "OpslaanBedrijf" },
      function(result) {
          console.log(result);
      })
}