    <div class="titre-homepage">
        <h1>on se fait une partie ?</h1>
        <p>Règles du jeu :
Dans le jeu de morpion classique, il faut être le ou la première à aligner sur une grille
une rangée de morpions (soit horizontalement, voit verticalement, soit en diagonale). Pour simplifier, on consi-
dère que les deux joueur.euse.s jouent sur la même machine, chacun.e leur tour (pas de jeu en réseau), et en
effectuant une seule action par tour. La grille est limitée aux dimensions 3x3 cases et 4x4 cases. Dans cette
version avancée, les règles sont étendues pour que les morpions soient ”actifs” pendant la partie, mais elles
restent volontairement basiques pour ne pas surcharger la programmation de l’application. Une équipe gagne
quand elle aligne une rangée de morpions (3 morpions sur une grille de 3x3 ou 4 morpions sur une grille de
4x4) ou quand tous les morpions adverses sont morts. Les morpions ont trois caractéristiques : points de vie,
points de dégâts et points de mana. La somme des points de ces trois caractéristiques vaut 10 (valeur par
défaut d’un paramètre de configuration). Quand les points de vie d’un morpion sont à zéro, il est mort (et la
case de la grille redevient libre). Quand un morpion en attaque un autre, il lui inflige un nombre de dégâts
égal à ses points de dégâts (i.e., on déduit les points de dégâts de l’attaquant aux points de vie de l’attaqué).
        </p>
    </div>

    <div class="formulaire-homepage">
        <form>
            <div>
                dimensions de la grille ?
                <br/>
                <select name="gridsize">
                    <optgroup >
                    <option value="3" selected>3*3</option>
                    <option value="4">4*4</option>
                </select>
            </div>
            <div>
                probabilité de double attaque ?
                <br/>
                0<input type="range" value="probabilité-double-attaque" min="0" max="30" step="2"/> 20
            </div>
            <br/>
            <div>
                <input class="bouton-homepage" type="submit" value="Jouer (mode tradi)"/>
                <input class="bouton-homepage" type="submit" value="Jouer (mode avancé)"/>
            </div>
        </form>
    </div>