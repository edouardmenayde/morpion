<div class="form-container">
    <form class="team-form" method="post" action="/team.php">
        <div class="form-group">
            <label for="teamname">Votre équipe epic</label>
            <input class="form-control" type="text" name="teamname" id="teamname" placeholder="Ex : la confrérie de l'anneau" maxlength="255"/>
        </div>
        <div class="form-group">
            <label for="teamcolor">Votre couleur epic</label>
            <input class="color-input" type="color" name="teamcolor" id="teamcolor"/>
        </div>
        
        <div class="form-group">
            <input class="submit-input" type="submit" value="Créer"/>
        </div>
    </form>
</div>
