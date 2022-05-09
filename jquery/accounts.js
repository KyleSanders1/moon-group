$(function() {
    $("#accounts").on("click", "ul[name=userlist]>li", SelecteerGebruiker)
})

function accountsLadenUsers(bedrijfsId){
  
  //Vullen alle list met users
  $.post(AjaxUrl, { func: "GetUsers", bedrid: bedrijfsId },
    function(users) {
        users = JSON.parse(users);
        var html = "";
        $.each(users, function(k) {
          html += "<li class=\"listitem shadow\" value=\"" + users[k]['user_id'] + "\">" + users[k]['name'] + "</li>";
          
        })
        $("ul[name=userlist]").html(html);
        $("#users div[name=DetailsUser] input").val('');
    })
}

function SelecteerGebruiker() {
    $el = $('div[name=DetailsUser]');
    $("#accounts ul[name=userlist]>li").removeClass('active');
    $(this).addClass("active");
    var account_id = $(this).val();
    console.log(account_id);
    $.post(AjaxUrl, { account_id: account_id, func: "GetUserDetails" }, function(result) {
        var account = JSON.parse(result);
        $.each(account, function(key, value) {
            $el.find("input[name=" + key + "]").val(value);
        })
    })
}

function AccountOpslaan() {
    var data = objectifyForm($('div[name=DetailsUser] input').serializeArray());
    data['user_id'] = $("#accounts ul[name=userlist]>li.active").attr("name");
    console.log(data);
    $.post(AjaxUrl, {data: JSON.stringify(data),func: "AccountOpslaan"},
    function(result){if(result != 1) {console.log(result); alert("Er is iets misgegaan bij het opslaan van de gegevens.");}})
}

function AccountToevoegen() {
  var weergavenaam = prompt("Wat is de weergavenaam van het account?");

  if (weergavenaam === null || weergavenaam == "") return;

  $.post(AjaxUrl, { weergavenaam: weergavenaam, func: "AccountToevoegen" },
      function(id) {
          $("#accounts ul[name=userlist]").append("<li class='listitem shadow' name=" + id + ">" + weergavenaam + "</li>");
          $("#accounts ul[name=userlist]>li[name=" + id + "]").trigger('click');
          console.log(id);
      })

}

function AccountVerwijderen() {
  var naam = $("#accounts ul[name=userlist]>li.active").text();
  if (confirm("Weet je zeker dat je " + naam + " wilt verwijderen?")) {
      var data = {};
      data['user_id'] = $("#accounts ul[name=userlist]>li.active").val();
      data['verwijderd'] = 1;
      $.post(AjaxUrl, { data: JSON.stringify(data), func: "VerwijderAccount" },
          function(result) {
              console.log(result);
              if (result == 1) {
                  $("#accounts ul[name=userlist]>li.active").remove();
                  $("#accounts div[name=DetailsUser] input").val('');
              }
          })
  }
}