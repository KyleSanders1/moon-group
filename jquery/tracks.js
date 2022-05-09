$(function() {
  $("#toevoegenTrack input[name=Locked]").parent().hide();
  $("#toevoegenTrack ").on("change", "select[name=Track_primary_artists]", AddComposerOnchangeArtist);
})

function trackToevoegen() {
  var data = objectifyForm($('#toevoegenTrack div[name=DetailsTrack] :input').serializeArray());

  data['Track_primary_artists'] = [];
  $.each($("#toevoegenTrack select[name=Track_primary_artists]").parent().find("li.badge>span"), function() {
      data['Track_primary_artists'].push($(this).text());
  })

  data['Track_featuring_artists'] = [];
  $.each($("#toevoegenTrack select[name=Track_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Track_featuring_artists'].push($(this).text());
  })

  data['Contributing_artists'] = [];
  $.each($("#toevoegenTrack select[name=Contributing_artists]").parent().find("li.badge>span"), function() {
      data['Contributing_artists'].push($(this).text());
  })

  data['Composers'] = [];
  $.each($("#toevoegenTrack select[name=Composers]").parent().find("li.badge>span"), function() {
      data['Composers'].push($(this).text());
  })

  data['Lyricists'] = [];
  $.each($("#toevoegenTrack select[name=Lyricists]").parent().find("li.badge>span"), function() {
      data['Lyricists'].push($(this).text());
  })

  data['Remixers'] = [];
  $.each($("#toevoegenTrack select[name=Remixers]").parent().find("li.badge>span"), function() {
      data['Remixers'].push($(this).text());
  })

  data['Performers'] = [];
  $.each($("#toevoegenTrack select[name=Performers]").parent().find("li.badge>span"), function() {
      data['Performers'].push($(this).text());
  })

  data['Writers'] = [];
  $.each($("#toevoegenTrack select[name=Writers]").parent().find("li.badge>span"), function() {
      data['Writers'].push($(this).text());
  })

  data['albumId'] = sessionStorage.getItem("selectedAlbum");
  console.log(data);

  if($("#toevoegenTrack input[name=ISRC]").parent().hasClass('fout')) {
    alert("Vul een niet bestaande ISRC in.");
    return;
  } else {
    $.post(AjaxUrl, { data: JSON.stringify(data), func: "InsertDetailsTrack" }, function(result) {

        if (result == 1) {
          $("#TotalTable div[name=tracktabel]").html('');
          $.post(AjaxUrl, { func: "GetTrackRows", albumId: albumId },
            function(rows) {
              var html = "<table name=\"tracks\" class=\"table table-striped table-hover\">";
              html += rows;
              html += "</table";
              $("#TotalTable div[name=tracktabel]").html(html);
              $("#TotalTable div[name=tracktabel] table").DataTable();
              $('#toevoegenTrack').hide();
              $("#home").show();

              tabellenVullenTabelTracks();
            })
        } else {
          alert("Er is iets misgegaan bij het toevoegen van de track. \nProbeer het later opnieuw.");
          console.log(result);
        }
    })
  }
}

function VullenISRC() {

    $.post(AjaxUrl, { func: "GetNextISRC", albumId: sessionStorage.getItem("selectedAlbum") },
        function(ISRC) {
            console.log(ISRC);
            $("#toevoegenTrack input[name=ISRC]").val(ISRC.trim());
        })
}

//Details van een track laden en tonen na selectie van een track
function tracksToonDetailsTrack(track_id) {
  $el = $("#detailsTrackModal div[name=DetailsTrack]");

  $.post(AjaxUrl, {func: "CheckUserRole"}, function(role){
    //console.log(role);
    if(role != 1){
      $el.find("input[name=Locked]").parent().hide();
    }
  })

  $.post(AjaxUrl, { trackId: track_id, func: "CheckLockedStatusTrack"}, function(status) {
    //console.log(status);
    if (status == 1){
      $el.find("input[name=Locked]").prop("checked", "checked");
    }else {
      $el.find("input[name=Locked]").prop("checked", "");
    }
    $.post(AjaxUrl, { func: "GetTrackDetails", track_id: track_id }, function(result) {

        var details = JSON.parse(result);
        console.log(details);
        $.each(details, function(k, v) {
            $el.find("select[name=" + k + "]").val(v);
            $el.find("input[name=" + k + "]").val(v);
        })

        var names = ["Track_primary_artists","Track_featuring_artists","Composers","Lyricists","Remixers","Performers","Writers","Publishers","Contributing_artists"];
        var labels = ["Track primary artists","Track featuring artists","Composers","Lyricists","Remixers","Performers","Writers","Publishers","Contributing artists"];
        var multiselelectNames = ["multiselectPrimaryArtists","multiselectFeaturingArtists","multiselectComposers","multiselectLyricists","multiselectRemixers", 
        "multiselectPerformers", "multiselectWriters","multiselectPublishers","multiselectContributingArtists"];

        for(var i = 0; i < names.length; i++){
          var options = $el.find("select[name=" + names[i] + "]").html();
          options = options.replace(/selected=\"selected\"/g,"");
          var html = "<select class=\"form-select multiple\" name= " + names[i] + " multiple=\"multiple\">" + options + "</select><label>" + labels[i] + "</label>";
          $el.find("div[name=" + multiselelectNames[i] + "]").html(html);
        }
        $("select.multiple").bsMultiSelect();

        //comboboxen vullen
        $.each(details['Track_primary_artists'], function(k, v) {
            $el.find("select[name=Track_primary_artists] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Track_primary_artists] option[value=" + v['artiest_id'] + "]").index();
            $el.find("select[name=Track_primary_artists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Track_featuring_artists'], function(k, v) {
            $el.find("select[name=Track_featuring_artists] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Track_featuring_artists] option[value=" + v['artiest_id'] + "]").index();
            $el.find("select[name=Track_featuring_artists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Composers'], function(k, v) {
            $el.find("select[name=Composers] option[value=" + v['composer_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Composers] option[value=" + v['composer_id'] + "]").index();
            console.log(index);
            $el.find("select[name=Composers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })
 
        $.each(details['Lyricists'], function(k, v) {
            $el.find("select[name=Lyricists] option[value=" + v['lyricist_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Lyricists] option[value=" + v['lyricist_id'] + "]").index();
            $el.find("select[name=Lyricists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Remixers'], function(k, v) {
            $el.find("select[name=Remixers] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Remixers] option[value=" + v['artiest_id'] + "]").index();
            $el.find("select[name=Remixers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Performers'], function(k, v) {
            $el.find("select[name=Performers] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Performers] option[value=" + v['artiest_id'] + "]").index();
            $el.find("select[name=Performers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Writers'], function(k, v) {
            $el.find("select[name=Writers] option[value=" + v['composer_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Writers] option[value=" + v['composer_id'] + "]").index();
            $el.find("select[name=Writers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Publishers'], function(k, v) {
          $el.find("select[name=Publishers] option[value=" + v['composer_id'] + "]").attr('selected', 'selected');
          var index = $el.find("select[name=Publishers] option[value=" + v['composer_id'] + "]").index();
          $el.find("select[name=Publishers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $.each(details['Contributing_artists'], function(k, v) {
            $el.find("select[name=Contributing_artists] option[value=" + v['artiest_id'] + "]").attr('selected', 'selected');
            var index = $el.find("select[name=Contributing_artists] option[value=" + v['artiest_id'] + "]").index();
            $el.find("select[name=Contributing_artists]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
        })

        $el.find("input[name=Track_parental_advisory]").prop("checked", details['Track_parental_advisory'] == 1);
        $el.find("input[name=Available_separately]").prop("checked", details['Available_separately'] == 1);
        $el.find("input[name=Locked]").prop("checked", details['Locked'] == 1);

        $("#detailsTrackModal").find("button[name=save]").attr("onclick", "tracksTrackWijzigen($('#detailsTrackModal').find('div[name=DetailsTrack]')," + track_id + ");");
        $("#detailsTrackModal").find("button[name=delete]").attr("onclick", "tracksTrackVerwijderen(" + track_id + ");");
    })
  })
}

function tracksTrackWijzigen($el, track_id) {

  var data = objectifyForm($el.find(' :input').serializeArray());
  data['track_id'] = track_id;

  data['Track_primary_artists'] = [];
  $.each($el.find("select[name=Track_primary_artists]").parent().find("li.badge>span"), function() {
      data['Track_primary_artists'].push($(this).text());
  })

  data['Track_featuring_artists'] = [];
  $.each($el.find("select[name=Track_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Track_featuring_artists'].push($(this).text());
  })

  data['Contributing_artists'] = [];
  $.each($el.find("select[name=Contributing_artists]").parent().find("li.badge>span"), function() {
      data['Contributing_artists'].push($(this).text());
  })

  data['Composers'] = [];
  $.each($el.find("select[name=Composers]").parent().find("li.badge>span"), function() {
      data['Composers'].push($(this).text());
  })

  data['Lyricists'] = [];
  $.each($el.find("select[name=Lyricists]").parent().find("li.badge>span"), function() {
      data['Lyricists'].push($(this).text());
  })

  data['Remixers'] = [];
  $.each($el.find("select[name=Remixers]").parent().find("li.badge>span"), function() {
      data['Remixers'].push($(this).text());
  })

  data['Performers'] = [];
  $.each($el.find("select[name=Performers]").parent().find("li.badge>span"), function() {
      data['Performers'].push($(this).text());
  })

  data['Writers'] = [];
  $.each($el.find("select[name=Writers]").parent().find("li.badge>span"), function() {
      data['Writers'].push($(this).text());
  })


  $.post(AjaxUrl, { func: "TrackWijzigen", data: JSON.stringify(data) },
    function(result) {
      if (result != 1) {
        console.log(result);
        alert("Er is iets misgegaan bij het opslaan van de gegevens. \nWijzigen wellicht niet mogelijk wegens locked status.");
      } else {
        tabellenVullenTabelTracks();
      }
    })
}

function tracksTrackKopie(track_id) {
  $("#detailsTrackModal").find("button[name=copy]").attr("onclick", "tracksTrackKopie($('#detailsTrackModal').find('div[name=DetailsTrack]')," + track_id + ");");

  $el = $("#detailsTrackModal div[name=DetailsTrack]");

  var data = objectifyForm($el.find(' :input').serializeArray());
  data['track_id'] = track_id;

  data['Track_primary_artists'] = [];
  $.each($el.find("select[name=Track_primary_artists]").parent().find("li.badge>span"), function() {
      data['Track_primary_artists'].push($(this).text());
  })

  data['Track_featuring_artists'] = [];
  $.each($el.find("select[name=Track_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Track_featuring_artists'].push($(this).text());
  })

  data['Contributing_artists'] = [];
  $.each($el.find("select[name=Contributing_artists]").parent().find("li.badge>span"), function() {
      data['Contributing_artists'].push($(this).text());
  })

  data['Composers'] = [];
  $.each($el.find("select[name=Composers]").parent().find("li.badge>span"), function() {
      data['Composers'].push($(this).text());
  })

  data['Lyricists'] = [];
  $.each($el.find("select[name=Lyricists]").parent().find("li.badge>span"), function() {
      data['Lyricists'].push($(this).text());
  })

  data['Remixers'] = [];
  $.each($el.find("select[name=Remixers]").parent().find("li.badge>span"), function() {
      data['Remixers'].push($(this).text());
  })

  data['Performers'] = [];
  $.each($el.find("select[name=Performers]").parent().find("li.badge>span"), function() {
      data['Performers'].push($(this).text());
  })

  data['Writers'] = [];
  $.each($el.find("select[name=Writers]").parent().find("li.badge>span"), function() {
      data['Writers'].push($(this).text());
  })

  data['albumId'] = sessionStorage.getItem("selectedAlbum");

  var aantal = parseInt(prompt("Hoevaak wilt u de track kopiëren?", "1"));
  if (isNaN(aantal) || aantal === null || aantal == "") {
    alert("Vul een getal in.");
    return;
  }

  $.post(AjaxUrl, { func: "CopyTracks", data: JSON.stringify(data), aantal:aantal },
    function(result) {
      if (result != 1) {
          console.log(result);
          alert("Er is iets misgegaan bij het opslaan van de gegevens.");
      } else {
          tabellenVullenTabelTracks();
          $("#detailsTrackModal").hide();
      }
    })
}

function tracksTrackVerwijderen(track_id){
  $("#detailsTrackModal").find("button[name=delete]").attr("onclick", "tracksTrackVerwijderen(" + track_id + ");");
  $el = $("#detailsTrackModal div[name=DetailsTrack]");
  
  var data = objectifyForm($el.find(' :input').serializeArray());
  data['track_id'] = track_id;
  if (confirm("Weet je zeker dat je de track wilt verwijderen?")) {

    $.post(AjaxUrl, { func: "TrackVerwijderen", track_id: track_id },
    function(result) {
        //console.log(result);
        if (result == 0){
          alert("Er is iets misgegaan bij het verwijderen van de gegevens. \nVerwijderen wellicht niet mogelijk wegens locked status.");
        } else {
          $("table[name=tracks] tr[name=" + track_id + "]").remove();
        }
    })
  }
}

function CheckISRC(){
  $el = $("#toevoegenTrack input[name=ISRC]").parent();
  $el.removeClass("fout");
  $.post(AjaxUrl, { func: "CheckISRC", isrc: $("#toevoegenTrack input[name=ISRC]").val() },
  function(ISRC) {
      console.log(ISRC);
      if(ISRC >= 1){
        $("#toevoegenTrack input[name=ISRC]").css("background-color", "lightcoral");
        $el.addClass("fout");
      } else {
        $("#toevoegenTrack input[name=ISRC]").css("background-color", "");
        $el.removeClass("fout");
      }
  })
}

function Vertaling(){
  $.post(AjaxUrl, { func: "Vertaling",    
   Album_language: $("#vertalingAlbumTrack select[name=Album_language]").val(),
   Track_language: $("#vertalingAlbumTrack select[name=Track_language]").val(),
   Audio_language: $("#vertalingAlbumTrack select[name=Audio_language]").val(),
   albumId: sessionStorage.getItem("selectedAlbum")
  }, function(result) {
    console.log(result);
  })
}

function CopyToTracks() {

  $el = $('#TotalTable div[name=DetailsAlbum]');
  var album_id =  sessionStorage.getItem("selectedAlbum");
  var data = objectifyForm($('#TotalTable div[name=DetailsAlbum] :input').serializeArray());
  data['album_id'] = album_id;
  // var bedrijfsid = $("header select[name=bedrijf] option:selected").val();
  // data['Bedrijf_id'] = bedrijfsid;

  data['Album_primary_artists'] = [];
  $.each($el.find("select[name=Album_primary_artists]").parent().find("li.badge>span"), function() {
      data['Album_primary_artists'].push($(this).text());
  })

  data['Album_featuring_artists'] = [];
  $.each($el.find("select[name=Album_featuring_artists]").parent().find("li.badge>span"), function() {
      data['Album_featuring_artists'].push($(this).text());
  })

   $.post(AjaxUrl, { func: "CopyToTracks",
   Primary_artists: $("#copyToTracks input[name=Primary_artists]").prop('checked'),
   Featuring_artists: $("#copyToTracks input[name=Featuring_artists]").prop('checked'),
   Release_date: $("#copyToTracks input[name=Release_date]").prop('checked'),
   Original_release_date: $("#copyToTracks input[name=Original_release_date]").prop('checked'),
   Main_genre: $("#copyToTracks input[name=Main_genre]").prop('checked'),
   albumId: sessionStorage.getItem("selectedAlbum"),
   data: JSON.stringify(data)
  }, function(result) {
    console.log(result);
    tabellenVullenTabelTracks(); 
  })
}

function AddComposerOnchangeArtist(){
  $el = $("#toevoegenTrack");
  $.post(AjaxUrl, { func: "AddComposerOnchangeArtist",    
   artiestId: $("#toevoegenTrack select[name=Track_primary_artists]").val()
  }, function(composerIds) {
    composersIds = JSON.parse(composerIds);
    console.log(composersIds);
    $.each(composersIds,function(k,v){
      console.log(v);
      $el.find("select[name=Composers] option[value=" + v['composer_id'] + "]").attr('selected', 'selected');
      var index = $el.find("select[name=Composers] option[value=" + v['composer_id'] + "]").index();
      $el.find("select[name=Composers]").data('DashboardCode.BsMultiSelect').updateOptionSelected(index);
    })
  })
}