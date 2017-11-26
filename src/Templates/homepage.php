<div class="form-team-container">
    <form class="form-team" method="post" action="/team.php">
        <div class="form-team-subform-container">
            <?php foreach ([1, 2] as $team) { ?>
                <div>
                    <h2>Équipe <?php echo $team; ?></h2>
                    <div class="form-group">
                        <label for="teamname<?php echo $team; ?>">Votre équipe epic</label>
                        <input required
                               class="form-control"
                               type="text"
                               name="team<?php echo $team; ?>[name]"
                               id="teamname<?php echo $team; ?>"
                               placeholder="Ex : la confrérie de l'anneau"
                               maxlength="255"/>
                    </div>
                    <div class="form-group">
                        <label for="team<?php echo $team; ?>[color]">Votre couleur epic</label>
                        <input required class="color-input" type="color" name="team<?php echo $team; ?>[color]"
                               id="team<?php echo $team; ?>[color]"/>
                    </div>
                    <div class="form-group">
                        <select data-id="<?php echo $team; ?>"
                                id="team-<?php echo $team; ?>-marks"
                                required
                                multiple
                                name="team<?php echo $team; ?>[marks][]">
                            <?php
                            foreach ($markModels as $markModel) {
                                ?>
                                <option name="<?php echo $markModel->id; ?>"
                                        value="<?php echo $markModel->id; ?>"><?php echo $markModel->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <div class="form-team-marks hide">
                            <h3 class="form-team-marks-title">Mages</h3>
                            <?php
                            foreach ($wizards as $wizard) {
                                ?>
                                <div onclick="selectMark(<?php echo $team; ?>, this)"
                                     class="form-team-mark"
                                     data-mark-id="<?php echo $wizard->id; ?>"
                                     data-team="<?php echo $team; ?>"></div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="form-team-marks hide">
                            <h3 class="form-team-marks-title">Guerrier</h3>
                            <?php
                            foreach ($warriors as $warrior) {
                                ?>
                                <div onclick="selectMark(<?php echo $team; ?>, this)"
                                     class="form-team-mark"
                                     data-mark-id="<?php echo $warrior->id; ?>"
                                     style="background-image: url('/images/warriors/valkyrie')"
                                     data-team="<?php echo $team; ?>"></div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="form-team-marks hide">
                            <h3 class="form-team-marks-title">Archers</h3>
                            <?php
                            foreach ($archers as $archer) {
                                ?>
                                <div onclick="selectMark(<?php echo $team; ?>, this)"
                                     class="form-team-mark"
                                     data-team="<?php echo $team; ?>"
                                     data-mark-id="<?php echo $archer->id; ?>"></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="form-group form-team-submit-container">
            <input class="submit-input" type="submit" value="Créer"/>
        </div>
    </form>
</div>

<script>
    (() => {
        Array.from(document.querySelectorAll('select')).forEach(select => {
            const team = select.dataset.id;

            Array.from(select.selectedOptions).forEach(selectedOption => {
                console.log(`div[data-team="${team}"][data-mark-id="${selectedOption.value}"]`);
                const mark = document.querySelector(`div[data-team="${team}"][data-mark-id="${selectedOption.value}"]`);
                mark.classList.add('form-team-mark-selected');
            });

            select.classList.add('hide');
        });
        Array.from(document.querySelectorAll('.form-team-marks')).forEach(marks => {
            marks.classList.remove('hide');
        })
    })();

    function selectMark(team, element) {
        const selector = document.querySelector(`#team-${team}-marks`);
        const id = element.dataset.markId;
        const item = selector.namedItem(id);

        if (!item.selected && selector.selectedOptions.length >= 8) {
            return;
        }

        item.selected = !item.selected;
        element.classList.toggle('form-team-mark-selected');
    }
</script>
