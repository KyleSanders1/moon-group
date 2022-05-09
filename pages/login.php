<?php
/*
 ________    ______        _______ .______     ______    _______ .______
|       /   /  __  \      /  _____||   _  \   /  __  \  |   ____||   _  \
`---/  /   |  |  |  |    |  |  __  |  |_)  | |  |  |  | |  |__   |  |_)  |
   /  /    |  |  |  |    |  | |_ | |      /  |  |  |  | |   __|  |   ___/
  /  /----.|  `--'  |    |  |__| | |  |\  \  |  `--'  | |  |____ |  |
 /________| \______/      \______| | _| `._\  \______/  |_______|| _|

 Geschreven door: Michel Raeven
 © ZO Groep - 31-01-2022
*/

// Initialize the session

session_start();

?>
<HTML>
<body>
  <div id="login">
    <div class="wrapper fadeInDown">
      <div class="formContent">
        <!-- Icon -->
        <div class="fadeIn first">
         <!-- <img src="/images/logo.jpg" id="icon" alt="User Icon" width='70%' style="margin:auto"/> -->
        </div>
        <form id='loginform' onsubmit="event.preventDefault();" method="post">
          <div class="form-group">
            <div class="form-floating mb-3">
              <input type="text" class="fadeIn second form-control" id="username" name="username" placeholder="Gebruikersnaam" autocomplete='username'>
              <label>Username</label>
            </div>
            <div class="form-floating">
              <input type="password" id="password" class="fadeIn third form-control" name="password" placeholder="Wachtwoord" autocomplete='current-password'>
              <label>Password</label>
            </div>
            <label class='error' id="Err_Username"></label>
          </div>
          
          <div class="form-group">
          <button type="button" class="fadeIn fourth btn btn-primary btn-block" id="LoginButton" onclick="Login();">Login</button>
          </div>
          <a href='#frmwachtwoordvergeten' style="color:white;" id='awachtwoordvergeten'>Reset password</a>
        </form>
        <form id='frmwachtwoordvergeten' onsubmit="event.preventDefault();" method="post" style="display:none">
          <input type='text' class="fadeIn first" id='WachtwoordVergetenEmail' name='WachtwoordVergetenEmail' placeholder='E-mailadres' autocomplete='email'>
          <label class='error' id="Err_WachtwoordVergetenEmail"></label>
          <input type="submit" class="fadeIn second" id="WachtwoordOpvragen" value="Opvragen">
          <input type="submit" class="fadeIn third" id="WwVergetenNaarLogin" value="Terug"><br>
        </form>
      </div>
    </div>
  </div>
</body>
</HTML>