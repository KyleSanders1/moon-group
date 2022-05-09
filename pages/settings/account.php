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
?>

<div id="accounts">
  
    <label class="pointer mb-3" onclick="AccountToevoegen();"><i class="fas fa-solid fa-plus"></i> Account toevoegen</label>
  
  <div class="row mb-3" >
    <div class="col-sm-2">
      <ul name="userlist">
     
      </ul>
    </div>
    <div class="col-sm-8" id="data-user" name="DetailsUser">

      <div class="form-floating mb-3">
      
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;" placeholder="Naam" required name="name">
          <label for="name">Naam</label>
          
      </div>
      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;"  placeholder="Gebruikersnaam" required name="username">
          <label for="username">Gebruikersnaam</label>
      </div>
      <div class="form-floating mb-3">
          <input type="text" autocomplete="off" class="form-control" style="width: 25%;"  placeholder="Email" required name="email">
          <label for="email">Email</label>
      </div>
      <div class="form-floating mb-3">
          <input type="password" autocomplete="off" class="form-control" style="width: 25%;"  placeholder="Wachtwoord" required name="password">
          <label for="wachtwoord">Wachtwoord</label>
      </div>
      <button type="button" onclick="AccountOpslaan();" class="btn btn-primary"><i class="fa fa-pencil"></i> Opslaan</button>
      <button type="button" onclick="AccountVerwijderen();" class="btn btn-danger"><i class="fa fa-trash"></i> Verwijder</button>
    </div>
  </div>
</div>
