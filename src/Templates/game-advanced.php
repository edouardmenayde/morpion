<?php
include __dir__ . '/../Utilities/xss.php';
?>
<div class="container">
    <div class="teams">
        <h2><span style="color:<?php xecho($game->team1->color); ?>"><?php xecho($game->team1->name); ?></span> vs
            <span style="color:<?php xecho($game->team2->color); ?>"><?php xecho($game->team2->name); ?></span></h2>
    </div>
    <div class="advanced-game">
        <div class="marks" id="team1-marks"></div>
        <div class="grid-container">
            <canvas class="grid"></canvas>
            <div class="winner hide" id="winner">
                <h3>
                    <span>Le gagnant est</span> <span id="winner-name"></span>
                </h3>
            </div>
            <div class="no-winner hide" id="no-winner">
                <h3>Il n'y a pas de gagnant</h3>
            </div>
            <ul class="menu hide" id="wizard-menu">
                <li id="spell">Attaquer</li>
                <li id="heal">Soigner</li>
                <li id="armageddon">Armageddon</li>
            </ul>
            <ul class="menu hide" id="warrior-menu">
                <li id="attack">Attaquer</li>
            </ul>
            <ul class="menu hide" id="archer-menu">
                <li id="arrow-attack">Envoyer une flêche</li>
            </ul>
        </div>
        <div class="marks" id="team2-marks"></div>
    </div>
</div>
<script src="<?php echo SITE_URL ?>scripts/interact.min.js"></script>
<script>
    (() => {
        function sendAction(action, cb) {
            const xhr = new XMLHttpRequest();

            xhr.open('POST', '<?php xecho(SITE_URL . '/advanced.php'); ?>', true);
            xhr.onload = function () {
                if (this.status === 200) {
                    return cb(null, JSON.parse(this.response));
                }

                return cb(JSON.parse(this.response).error);
            };

            const formData = new FormData();

            for (let key in action) {
                if (action.hasOwnProperty(key)) {
                    formData.append(key, action[key]);
                }
            }

            xhr.send(formData);
        }

        class AdvancedTicTacToe {
            constructor() {
                this.canvas = document.querySelector('canvas');
                this.noWinnerText = document.querySelector('#no-winner');
                this.winnerText = document.querySelector('#winner');
                this.winnerName = this.winnerText.querySelector('#winner-name');
                this.team1Marks = document.querySelector('#team1-marks');
                this.team2Marks = document.querySelector('#team2-marks');
                this.warriorMenu = document.querySelector('#warrior-menu');
                this.wizardMenu = document.querySelector('#wizard-menu');
                this.archerMenu = document.querySelector('#archer-menu');
                this.selectedMark = null;
                this.showingMenu = false;

                if (!this.canvas.getContext) {
                    throw new Error('Canvas not supported');
                }

                this.width = this.canvas.scrollWidth;
                this.height = this.canvas.scrollHeight;

                this.canvas.width = this.width;
                this.canvas.height = this.height;

                this.leftOffset = this.canvas.parentElement.offsetLeft;
                this.topOffset = this.canvas.parentElement.offsetTop;

                this.ctx = this.canvas.getContext('2d');

                this.game = <?php echo(json_encode($game)); ?>;

                this.sides = parseInt(this.game.gridWidth, 10); // assuming width = height which should be the case

                this.backgroundColor = '#eee';

                this.finished = false;
                this.fetching = false;

                this.setGlobalStyle()
                    .generateSquares()
                    .createAndDrawGrid()
                    .drawExistingMarks()
                    .updateGameStatus();

                interact(this.canvas).dropzone({
                    accept: '.mark',
                    ondrop: this.onDrop.bind(this)
                });

                this.canvas.addEventListener('click', this.onClick.bind(this), false);
                this.canvas.addEventListener('contextmenu', this.onContextMenu.bind(this), false);
                document.querySelector('#attack').addEventListener('click', this.onAction.bind(this, 'attack'), false);
                document.querySelector('#arrow-attack').addEventListener('click', this.onAction.bind(this, 'arrowAttack'), false);
                document.querySelector('#heal').addEventListener('click', this.onAction.bind(this, 'heal'), false);
                document.querySelector('#spell').addEventListener('click', this.onAction.bind(this, 'spell'), false);
                document.querySelector('#armageddon').addEventListener('click', this.onAction.bind(this, 'armageddon'), false);
            }

            drawMarks(team, container) {
                team.marks.forEach(mark => {
                    const matchingMarkElement = document.querySelector(`div[data-mark-id="${mark.id}"]`);
                    if (matchingMarkElement) {
                        if (mark.x && mark.y) {
                            matchingMarkElement.remove();
                        }

                        return;
                    }

                    if (mark.x && mark.y) {
                        return;
                    }

                    const markElement = document.createElement('div');
                    markElement.draggable = true;
                    markElement.dataset.markId = mark.id;
                    markElement.classList.add('mark');

                    const markElementImg = document.createElement('img');
                    markElementImg.src = '<?php echo SITE_URL; ?>images/warriors/' + mark.markModel.icon + '.jpg';
                    markElementImg.classList.add('img');
                    markElement.append(markElementImg);

                    const markElementCaption = document.createElement('div');
                    markElementCaption.textContent = mark.markModel.name;

                    markElement.append(markElementCaption);

                    interact(markElement).draggable({
                        inertia: true,
                        onstart: () => {
                            markElement.style.opacity = '0.4';
                        },
                        onend: () => {
                            markElement.style.opacity = '1';
                        }
                    });

                    container.append(markElement);
                });
            }

            updateGameStatus() {
                if (this.game.winnerId) {
                    const winner = this.game.teams.find(team => team.id == this.game.winnerId);

                    this.canvas.classList.add('finished');

                    this.winnerName.style.color = winner.color;
                    this.winnerName.textContent = winner.name;
                    this.winnerText.classList.remove('hide');
                }
                else if (this.game.ended == true) {
                    this.canvas.classList.add('finished');
                    this.noWinnerText.classList.remove('hide');
                }

                this.drawMarks(this.game.team1, this.team1Marks);
                this.drawMarks(this.game.team2, this.team2Marks);
            }

            onDrop(event) {
                const x = event.dragEvent.pageX - this.leftOffset;
                const y = event.dragEvent.pageY - this.topOffset;

                const square = this.getMatchingSquare(x, y);

                if (!square) {
                    throw new Error('Woa sth went very wrong.');
                }

                this.fetching = true;

                sendAction({
                    gameId: this.game.id,
                    markId: event.relatedTarget.dataset.markId,
                    x: square.relativeX,
                    y: square.relativeY,
                    action: 'placement'
                }, (error, result) => {
                    this.fetching = false;

                    if (error) {
                        return console.error(error);
                    }

                    this.game = result.game;

                    result.updatedMarks.forEach(mark => this.placeMark(this.squares[mark.x][mark.y], mark));


                    this.updateGameStatus();
                });
            }

            onClick(event) {
                if (event.which !== 1) { // only left mouse button allowed to affect
                    return;
                }

                const x = event.pageX - this.leftOffset;
                const y = event.pageY - this.topOffset;

                const square = this.getMatchingSquare(x, y);

                if (!square) {
                    throw new Error('Woa sth went very wrong.');
                }

                if (this.showingMenu) {
                    this.hideMenu();
                }

                this.game.teams.forEach(team => {
                    team.marks.forEach(mark => {
                        if (mark.x == square.relativeX && mark.y == square.relativeY) {
                            this.selectedMark = mark;
                        }
                    })
                });
            }

            hideMenu() {
                this.archerMenu.classList.add('hide');
                this.wizardMenu.classList.add('hide');
                this.warriorMenu.classList.add('hide');

                this.showingMenu = false;
            }

            showMenu({x, y}) {
                this.showingMenu = true;

                if (this.selectedMark.markModel.type === 'wizard') {
                    this.wizardMenu.classList.remove('hide');
                    this.wizardMenu.style.top = `${y}px`;
                    this.wizardMenu.style.left = `${x}px`;
                    return;
                }

                if (this.selectedMark.markModel.type === 'archer') {
                    this.archerMenu.classList.remove('hide');
                    this.archerMenu.style.top = `${y}px`;
                    this.archerMenu.style.left = `${x}px`;
                    return;
                }

                this.warriorMenu.classList.remove('hide');
                this.warriorMenu.style.top = `${y}px`;
                this.warriorMenu.style.left = `${x}px`;
            }

            onContextMenu(event) {
                event.preventDefault();

                if (this.finished || this.fetching || !this.selectedMark) {
                    return;
                }

                const x = event.pageX - this.leftOffset;
                const y = event.pageY - this.topOffset;

                const square = this.getMatchingSquare(x, y);

                if (!square) {
                    throw new Error('Woa sth went very wrong.');
                }

                this.showMenu({x: event.pageX, y: event.pageY});
            }

            onAction(action, event) {
                if (this.finished || this.fetching || !this.selectedMark) {
                    return;
                }

                const x = event.pageX - this.leftOffset;
                const y = event.pageY - this.topOffset;

                const square = this.getMatchingSquare(x, y);

                if (!square) {
                    throw new Error('Woa sth went very wrong.');
                }

                this.hideMenu();

                this.fetching = true;
                sendAction({
                    gameId: this.game.id,
                    markId: this.selectedMark.id,
                    x: square.relativeX,
                    y: square.relativeY,
                    action: action
                }, (error, result) => {
                    this.fetching = false;

                    if (error) {
                        return console.error(error);
                    }

                    result.updatedMarks.forEach(mark => this.placeMark(this.squares[mark.x][mark.y], mark));

                    this.game = result.game;

                    this.updateGameStatus();
                });
            }

            setGlobalStyle() {
                this.ctx.fillStyle = 'transparent';
                this.ctx.strokeStyle = '#b3b3b3';

                return this;
            }

            generateSquares() {
                this.squares = Array(this.sides).fill(null).map(() => Array(this.sides).fill(null));

                this.widthPart = this.width / this.sides;
                this.heightPart = this.height / this.sides;

                return this;
            }

            createAndDrawGrid() {
                let i = 0;
                let j = 0;

                for (let x = 0; x < this.width; x += this.widthPart) {
                    j = 0;

                    for (let y = 0; y < this.height; y += this.heightPart) {
                        const w = x + this.widthPart;
                        const h = y + this.heightPart;

                        this.squares[i][j] = {
                            relativeX: i,
                            relativeY: j,
                            x,
                            y,
                            centerX: w - (this.widthPart / 2),
                            centerY: h - (this.heightPart / 2)
                        };

                        this.ctx.strokeRect(x, y, this.widthPart, this.heightPart);

                        this.drawEmpty(this.squares[i][j]);

                        j++;
                    }

                    i++;
                }

                return this;
            }

            drawEmpty({centerX, centerY}) {
                const width = 75;

                this.ctx.lineWidth = 2;
                this.ctx.beginPath();

                this.ctx.moveTo(centerX - width / 2, centerY + width / 2);
                this.ctx.lineTo(centerX + width / 2, centerY + width / 2);

                this.ctx.closePath();
                this.ctx.stroke();
                this.ctx.lineWidth = 1;
            }

            empty({x, y}) {
                const delta = 5;
                this.ctx.fillStyle = this.backgroundColor;
                this.ctx.fillRect(x + delta, y + delta, this.widthPart - delta * 2, this.heightPart - delta * 2);
            }

            drawStatus({x, y}, mark) {
                this.ctx.fillStyle = '#3d3d3d';
                this.ctx.font = '13px serif';
                this.ctx.fillText(`${mark.hp} HP | ${mark.mana} mana | ${mark.damage} dmg`, x + this.widthPart / 5, y + this.heightPart - 10);
            }

            placeMark({x, y}, mark) {
                this.empty({x, y});

                const image = new Image();

                image.onload = () => {
                    const w = image.naturalWidth;
                    const h = image.naturalHeight;
                    this.ctx.drawImage(image, x + (this.widthPart - w) / 2, y + (this.heightPart - h) / 2);
                };

                image.src = '<?php echo SITE_URL?>images/warriors/' + mark.markModel.icon + '.jpg';

                const delta = 1;
                this.ctx.strokeWidth = 3;
                this.ctx.fillStyle = mark.team.color;
                this.ctx.strokeRect(x + delta, y + delta, this.widthPart - delta * 2, this.heightPart - delta * 2);
                this.ctx.strokeWidth = 1;

                this.drawStatus({x, y}, mark);
            }

            drawExistingMarks() {
                this.game.teams.forEach(team => {
                    const marks = team.marks;
                    this.ctx.strokeStyle = team.color;

                    marks.forEach(mark => {
                        if (mark.x && mark.y) {
                            const square = this.squares[mark.x][mark.y];

                            this.placeMark(square, mark);
                        }
                    });
                });

                return this;
            }

            getMatchingSquare(x, y) {
                let matchingSquare;

                for (let i = 0; i < this.sides; i++) {
                    for (let j = 0; j < this.sides; j++) {
                        const square = this.squares[i][j];
                        const squareWidth = square.x + this.widthPart;
                        const squareHeight = square.y + this.heightPart;

                        if (x >= square.x && x < squareWidth && y >= square.y && y < squareHeight) {
                            matchingSquare = square;
                            break;
                        }
                    }

                    if (matchingSquare) {
                        break;
                    }
                }

                return matchingSquare;
            }
        }

        new AdvancedTicTacToe();

    })();
</script>
