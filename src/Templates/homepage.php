    <div class="form-team-container">
        <form class="form-team" method="post" action="<?php echo SITE_URL ?>team.php">
            <div class="form-team-container">
                quelles dimensions pour votre grille ?
                <br/>
                <select name="gridsize">
                    <optgroup label="dimension">
                    <option value="3" selected>3*3</option>
                    <option value="4">4*4</option>
                </select>
            </div>
            <div >
                quel probabilité de double attaque
                <br/>
                <input type="number" value="probabilité-double-attaque" max=30 />
            <div/>

            <div class="form-group form-team-submit-container">
                <input class="submit-input" type="submit" value="Jouer (mode tradi)"/>
                <input class="submit-input" type="submit" value="Jouer (mode avancé)"/>
            </div>
        </form>
    </div>