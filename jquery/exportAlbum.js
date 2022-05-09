function ExportSelectie() {
    var albumsExport = JSON.parse(sessionStorage.getItem("albumsExport"));
    
    if (albumsExport === null) albumsExport = [];

    var trackdata = ["Track_title", "Track_version", "ISRC", "Track_primary_artists", "Track_featuring_artists",
        "Volume_number", "Track_main_genre", "Track_main_subgenre", "Track_alternate_genre", "Track_alternate_subgenre", "Track_language_(Metadata)", "Audio_language",
        "Lyrics", "Available_separately", "Track_parental_advisory", "Preview_start", "Preview_length", "Track_recording_year", "Track_recording_location", "Contributing_artists",
        "Composers", "Lyricists", "Remixers", "Performers", "Producers", "Writers", "Publishers", "Track_sequence", "Track_catalog_tier", "Original_file_name", "Original_release_date"
    ];
    var albumdata = ["Field_name", "Album_title", "Album_version", "UPC", "Catalog_number", "Primary_artists", "Featuring_artists", "Release_date", "Main_genre", "Main_subgenre", "Alternate_genre",
        "Alternate_subgenre", "Label", "CLine_year", "CLine_name", "PLine_year", "PLine_name", "Parental_advisory", "Recording_year", "Recording_location", "Album_format", "Number_of_volumes",
        "Territories", "Excluded_territories", "Language_(Metadata)", "Catalog_tier"
    ];

    var table = "<table class=\"table table-hover\"><thead><tr>";
    $.each(albumdata, function(k, v) {
        table += "<th>" + v.replace(/_/g, " ") + "</th>";
    })
    $.each(trackdata, function(k, v) {
        table += "<th>" + v.replace(/_/g, " ") + "</th>";
    })
    table += "</tr></thead><tbody>";

    var bedrijfsId = $("header select[name=bedrijf] option:selected").val();
    
    if (albumsExport.length == 0) {
      if(confirm("Weet u zeker dat u alles wilt exporteren?")) {
        $.post(AjaxUrl, {
          van: $("#selectie").find("input[name=van]").val(),
          tot: $("#selectie").find("input[name=tot]").val(),
          bedrId: bedrijfsId,
          func: "GetAlbumIdVanTotBedrijf" },
          function(albumIds) {
            albumIds = JSON.parse(albumIds);
            console.log(albumIds);
            var ids =[];
            $.each(albumIds, function(k, v) {
               console.log(v['album_id']);
               ids.push(v['album_id']);
             })
             sessionStorage.setItem("albumsExport",JSON.stringify(ids));
          
              $.post(AjaxUrl, {
              albumdata: JSON.stringify(albumdata),
              trackdata: JSON.stringify(trackdata), 
              albums: sessionStorage.getItem("albumsExport"),
              bedrId: bedrijfsId,
              func: "GetExportRowsCSV"}, 
              function(result){
                console.log(result); table += result;
                table += "</tbody></table>";

                $("div[name=ExportTable]").html(table);
                $("div[name=ExportTable] table").table2csv(); 
              })
              sessionStorage.removeItem("albumsExport");   
          })
      } else {
        return;
      }
    } else {
    //Via php de data ophalen
    $.post(AjaxUrl, { albums: sessionStorage.getItem("albumsExport"), albumdata: JSON.stringify(albumdata), trackdata: JSON.stringify(trackdata), bedrId: bedrijfsId, func: "GetExportRowsCSV" },
        function(result) { 
          console.log(result); table += result;
          table += "</tbody></table>";

          $("div[name=ExportTable]").html(table);
          $("div[name=ExportTable] table").table2csv(); 
    
        })
    }
}