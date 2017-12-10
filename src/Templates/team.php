<?php
include __dir__ . '/../Utilities/xss.php';
?>
<div class="form-team-container">
    <form class="form-team" method="post" action="<?php xecho(SITE_URL) ?>team.php">
        <?php if ($errors) { ?>
            <ul class="form-team-errors">
                <?php foreach ($errors as $error) { ?>
                    <li><?php xecho($error); ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <div class="form-team-subform-container">
            <?php foreach ([1, 2] as $team) { ?>
                <div>
                    <h2>Équipe <?php xecho($team); ?></h2>
                    <div class="form-group">
                        <label for="teamname<?php xecho($team); ?>">Votre équipe epic</label>
                        <input required
                               class="form-control"
                               type="text"
                               name="team<?php xecho($team); ?>[name]"
                               id="teamname<?php xecho($team); ?>"
                               placeholder="Ex : la confrérie de l'anneau"
                               maxlength="255"/>
                    </div>
                    <div class="form-group">
                        <label for="team<?php xecho($team); ?>[color]">Votre couleur epic</label>
                        <input value="<?php xecho('#' . dechex(rand(256, 16777215))); ?>" required class="color-input"
                               type="color" name="team<?php xecho($team); ?>[color]"
                               id="team<?php xecho($team); ?>[color]"/>
                    </div>
                    <?php if ($type === 'advanced') { ?>
                        <div class="form-group">
                            <select data-id="<?php xecho($team); ?>"
                                    id="team-<?php xecho($team); ?>-marks"
                                    required
                                    multiple
                                    name="team<?php xecho($team); ?>[marks][]">
                                <?php
                                foreach ($markModels as $markModel) {
                                    ?>
                                    <option name="<?php xecho($markModel->id); ?>"
                                            value="<?php xecho($markModel->id); ?>"><?php xecho($markModel->name); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <div class="form-team-marks hide">
                                <h3 class="form-team-marks-title">Mages</h3>
                                <?php
                                foreach ($wizards as $wizard) {
                                    ?>
                                    <div onclick="selectMark(<?php xecho($team); ?>, this)"
                                         class="form-team-mark"
                                         data-mark-id="<?php xecho($wizard->id); ?>"
                                         data-team="<?php xecho($team); ?>"
                                         style="background-image: url('<?php echo SITE_URL . 'images/warriors/' . $wizard->icon ?>');"
                                    ></div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-team-marks hide">
                                <h3 class="form-team-marks-title">Guerrier</h3>
                                <?php
                                foreach ($warriors as $warrior) {
                                    ?>
                                    <div onclick="selectMark(<?php xecho($team); ?>, this)"
                                         class="form-team-mark"
                                         data-mark-id="<?php xecho($warrior->id); ?>"
                                         style="background-image: url('<?php echo SITE_URL . 'images/warriors/' . $warrior->icon ?>');"
                                         data-team="<?php xecho($team); ?>"
                                    ></div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-team-marks hide">
                                <h3 class="form-team-marks-title">Archers</h3>
                                <?php
                                foreach ($archers as $archer) {
                                    ?>
                                    <div onclick="selectMark(<?php xecho($team); ?>, this)"
                                         class="form-team-mark"
                                         data-team="<?php xecho($team); ?>"
                                         style="background-image: url('<?php echo SITE_URL . 'images/warriors/' . $archer->icon ?>');"
                                         data-mark-id="<?php xecho($archer->id); ?>"
                                    ></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <input type="hidden" name="type" value="<?php xecho($type); ?>"/>
        <input type="hidden" name="gridsize" value="<?php xecho($gridsize) ?>">
        <input type="hidden" name="doubleAttack" value="<?php xecho($doubleAttack) ?>">

        <div class="form-group form-team-submit-container">
            <input class="submit-input" type="submit" value="Jouer"/>
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
