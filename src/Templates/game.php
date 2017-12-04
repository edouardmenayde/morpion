<div class="container">
    <div class="teams">
        <h2><span style="color:<?php echo $game->team1->color; ?>"><?php echo $game->team1->name; ?></span> vs <span
                    style="color:<?php echo $game->team2->color; ?>"><?php echo $game->team2->name; ?></span></h2>
    </div>
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
    </div>
</div>
<script>
    function sendPlacement(placement, cb) {
        const xhr = new XMLHttpRequest();

        xhr.open('POST', '<?php echo SITE_URL . 'classic.php'; ?>', true);
        xhr.onload = function (event) {
            if (this.status === 200) {
                return cb(null, JSON.parse(this.response));
            }

            return cb(event);
        };

        const formData = new FormData();

        for (let key in placement) {
            if (placement.hasOwnProperty(key)) {
                formData.append(key, placement[key]);
            }
        }

        xhr.send(formData);
    }

    (() => {
        const canvas = document.querySelector('canvas');
        const noWinnerText = document.querySelector('#no-winner');
        const winnerText = document.querySelector('#winner');
        const winnerName = winnerText.querySelector('#winner-name');

        if (!canvas.getContext) {
            throw new Error('Canvas not supported');
        }

        const width = canvas.scrollWidth;
        const height = canvas.scrollHeight;

        canvas.width = width;
        canvas.height = height;

        const left = canvas.parentElement.offsetLeft;
        const top = canvas.parentElement.offsetTop;

        const ctx = canvas.getContext('2d');

        const cross = Symbol('cross');
        const circle = Symbol('circle');

        const teams = <?php echo json_encode([$game->team1, $game->team2]); ?>;
        teams[0].mark = cross;
        teams[1].mark = circle;

        const gameId = <?php echo json_encode($game->id); ?>;

        const backgroundColor = '#eee';

        ctx.fillStyle = 'transparent';
        ctx.strokeStyle = '#b3b3b3';

        const sides = 3;

        const squares = Array(sides).fill(null).map(() => Array(sides).fill(null));

        const widthPart = width / sides;
        const heightPart = height / sides;

        createAndDrawGrid();

        teams.forEach(team => {
            const marks = team.marks;
            ctx.strokeStyle = team.color;

            marks.forEach(mark => {
                const square = squares[mark.x][mark.y];

                if (team.mark === cross) {
                    drawCross(square);
                }
                else {
                    drawCircle(square);
                }
            });
        });

        let turn = 0;

        let finished = false;
        let fetching = false;

        function createAndDrawGrid() {
            let i = 0;
            let j = 0;

            for (let x = 0; x < width; x += widthPart) {
                j = 0;

                for (let y = 0; y < height; y += heightPart) {
                    const w = x + widthPart;
                    const h = y + heightPart;

                    squares[i][j] = {
                        relativeX: i,
                        relativeY: j,
                        x,
                        y,
                        centerX: w - (widthPart / 2),
                        centerY: h - (heightPart / 2),
                        content: null
                    };

                    ctx.strokeRect(x, y, widthPart, heightPart);
                    drawEmpty(squares[i][j]);

                    j++;
                }

                i++;
            }
        }

        function empty({x, y}) {
            const delta = 5;
            ctx.fillStyle = backgroundColor;
            ctx.fillRect(x + delta, y + delta, widthPart - delta * 2, heightPart - delta * 2);
        }

        function drawCross({centerX, centerY, x, y}) {
            const midX = widthPart / 4;
            const midY = heightPart / 4;

            empty({x, y});

            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.lineTo(x + midX, y + midY);

            ctx.moveTo(centerX, centerY);
            ctx.lineTo(centerX + midX, centerY + midY);

            ctx.moveTo(centerX, centerY);
            ctx.lineTo(centerX + midX, y + midY);

            ctx.moveTo(centerX, centerY);
            ctx.lineTo(x + midX, centerY + midY);

            ctx.closePath();
            ctx.stroke();
            ctx.lineWidth = 1;
        }

        function drawCircle({centerX, centerY, x, y}) {
            const radius = widthPart / 4;

            empty({x, y});

            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, Math.PI * 2, 0);
            ctx.stroke();
            ctx.lineWidth = 1;
        }

        function drawEmpty({centerX, centerY}) {
            ctx.lineWidth = 2;

            const width = 75;

            ctx.beginPath();
            ctx.moveTo(centerX - width / 2, centerY + width / 2);
            ctx.lineTo(centerX + width / 2, centerY + width / 2);

            ctx.closePath();
            ctx.stroke();
            ctx.lineWidth = 1;
        }

        function isWinnerHorizontally(team) {
            for (let i = 0; i < sides; i++) {
                let matches = 0;

                for (let j = 0; j < sides; j++) {
                    if (squares[i][j].content === team) {
                        matches++;
                    }
                }

                if (matches === sides) {
                    return true;
                }
            }
        }

        function isWinnerVertically(team) {
            for (let i = 0; i < sides; i++) {
                let matches = 0;

                for (let j = 0; j < sides; j++) {
                    if (squares[j][i].content === team) {
                        matches++;
                    }
                }

                if (matches === sides) {
                    return true;
                }
            }
        }

        function isWinnerInDiagonal(team) {
            let matches = 0;


            for (let i = 0; i < sides; i++) {
                if (squares[i][i].content === team) {
                    matches++;
                }
            }

            if (matches === sides) {
                return true;
            }

            matches = 0;

            for (let i = 0; i < sides; i++) {
                if (squares[i][sides - 1 - i].content === team) {
                    matches++;
                }
            }

            if (matches === sides) {
                return true;
            }
        }

        function isWinner(team) {
            // we need to test if they are similar symbol on a line or diagonal
            return isWinnerHorizontally(team) || isWinnerVertically(team) || isWinnerInDiagonal(team);
        }

        function isNoWinner() {
            let notEmpty = 0;

            for (let i = 0; i < sides; i++) {
                for (let j = 0; j < sides; j++) {
                    if (squares[i][j].content !== null) {
                        notEmpty++;
                    }
                }
            }

            return notEmpty === sides * sides;
        }

        function findMatchingSquare(x, y) {
            let matchingSquare;

            for (let i = 0; i < sides; i++) {
                for (let j = 0; j < sides; j++) {
                    const square = squares[i][j];
                    const squareWidth = square.x + widthPart;
                    const squareHeight = square.y + heightPart;

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

        canvas.addEventListener('click', event => {
            if (finished || fetching) {
                return;
            }

            const x = event.pageX - left;
            const y = event.pageY - top;

            const square = findMatchingSquare(x, y);

            if (!square) {
                throw new Error('Woa sth went very wrong.');
            }


            fetching = true;

            sendPlacement({
                gameId: gameId,
                x: square.relativeX,
                y: square.relativeY
            }, (error, mark) => {
                fetching = false;

                if (error) {
                    return;
                }

                const team = teams.find(team => team.id == mark.teamId);

                ctx.strokeStyle = team.color;

                if (turn === 0) {
                    drawCross(square);

                    square.content = team.id;
                    turn = 1;
                }
                else {
                    drawCircle(square);

                    square.content = team.id;
                    turn = 0;
                }

                for (let i = 0; i < 2; i++) {
                    if (isWinner(teams[i].id)) {
                        finished = true;
                        const winner = teams[i];

                        canvas.classList.add('finished');

                        winnerName.style.color = winner.color;
                        winnerName.textContent = winner.name;
                        winnerText.classList.remove('hide');
                    }
                }

                if (isNoWinner()) {
                    canvas.classList.add('finished');
                    noWinnerText.classList.remove('hide');
                }
            });
        }, false);
    })();
</script>
