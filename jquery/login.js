$(function() {

    $('#awachtwoordvergeten').on('click', ToonWwVergeten);
    $('#frmwachtwoordvergeten').hide();
    $('#WwVergetenNaarLogin').on('click', VerbergWwVergeten);

    $('#login').on('keyup', function(event) {
        if (event.keyCode === 13) Login();
    })
})

function Login() {
    var err = false;
    var username = $("#username").val();
    var password = $('#password').val();
    if (username == "") {
        $("#username").css("border", "5px solid red");
        err = true;
    } else $("#username").css("border", "1px solid #ced4da");

    if (password == "") {
        $("#password").css("border", "5px solid red");
        err = true;
    } else $("#password").css("border", "1px solid #ced4da");
    console.log(err);
    if (err) return;
    $.post(AjaxUrl, {
            username: username,
            password: password,
            func: "Login"
        },
        function(data) {
            data = data.trim();
            console.log(data);
            if (data.length > 3) {
                $('#Err_Username').html(data);
                $('#Err_Username').show();
                console.log(data.length);
            } else {
                window.location.reload();
            }
        });
}

function VerbergWwVergeten() {
    $('#loginform').show();
    $('#frmwachtwoordvergeten').hide();
}

function ToonWwVergeten() {
    $('#loginform').hide();
    $('#frmwachtwoordvergeten').show();
}

$('#frmwachtwoordvergeten').hide(); //hide the form on load