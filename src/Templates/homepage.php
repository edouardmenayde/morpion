<?php
include __dir__ . '/../Utilities/xss.php';
?>
<div class="homepage-container">
    <div class="rules">
        <h1>On se fait une partie ?</h1>
        <p>Règles du jeu : <br>
            Dans le jeu de morpion classique, il faut être le ou la première à aligner sur une grille
            une rangée de morpions (soit horizontalement, voit verticalement, soit en diagonale). <br> La grille est
            limitée
            aux dimensions 3x3 cases et 4x4 cases.<br> Dans cette
            version avancée, les règles sont étendues pour que les morpions soient ”actifs” pendant la partie, mais
            elles
            restent volontairement basiques pour ne pas surcharger la programmation de l’application.<br> Une équipe
            gagne
            quand elle aligne une rangée de morpions (3 morpions sur une grille de 3x3 ou 4 morpions sur une grille de
            4x4) ou quand tous les morpions adverses sont morts. Les morpions ont trois caractéristiques : points de
            vie,
            points de dégâts et points de mana. La somme des points de ces trois caractéristiques vaut 10 (valeur par
            défaut d’un paramètre de configuration). Quand les points de vie d’un morpion sont à zéro, il est mort (et
            la
            case de la grille redevient libre). Quand un morpion en attaque un autre, il lui inflige un nombre de dégâts
            égal à ses points de dégâts (i.e., on déduit les points de dégâts de l’attaquant aux points de vie de
            l’attaqué).
        </p>
    </div>

    <div class="form-homepage-container">
        <form action="<?php xecho(SITE_URL)?>" method="post">
            <div class="class form-group">
                <label for="type">Mode de jeu</label>
                <select name="type" id="gridsize" required>
                    <option value="classic" selected>Classique</option>
                    <option value="advanced">Avancé</option>
                </select>
            </div>
            <div class="form-group">
                <label for="gridsize">Dimensions de la grille</label>
                <select name="gridsize" id="gridsize" required>
                    <option value="3" selected>3 x 3</option>
                    <option value="4">4 x 4</option>
                </select>
            </div>
            <div class="form-group">
                <label for="doubleAttack">Probabilité de double attaque</label>

                <div style="doubleAttack">
                    0<input title="Probabilité de double attaque" name="doubleAttack" id="doubleAttacks" type="range"
                            value="20" min="0" max="30" step="5" required/>30
                </div>
            </div>
            <div class="form-group">
                <input class="submit-input" type="submit" value="Jouer"/>
            </div>
        </form>
    </div>
</div>
