class TicTacToe {
    constructor(game) {
        this.canvas = document.querySelector('canvas');
        this.noWinnerText = document.querySelector('#no-winner');
        this.winnerText = document.querySelector('#winner');
        this.winnerName = this.winnerText.querySelector('#winner-name');

        if (!this.canvas.getContext) {
            throw new Error('Canvas not supported');
        }

        this.width = this.canvas.scrollWidth;
        this.height = this.canvas.scrollHeight;

        this.canvas.width = this.width;
        this.canvas.height = this.height;

        const rect = this.canvas.getBoundingClientRect();

        this.leftOffset = rect.left;
        this.topOffset = rect.top;

        this.ctx = this.canvas.getContext('2d');

        this.game = game;

        this.teams = [this.game.team1, this.game.team2];
        this.giveEachTeamAMark();

        this.sides = parseInt(this.game.gridWidth, 10); // assuming width = height which should be the case
        this.backgroundColor = '#eee';

        this.finished = false;
        this.fetching = false;

        this.setGlobalStyle()
            .generateSquares()
            .createAndDrawGrid()
            .drawExistingMarks()
            .updateGameStatus();

        this.canvas.addEventListener('click', this.onClick.bind(this), false);
    }

    updateGameStatus() {
        if (this.game.winnerId) {
            const winner = this.teams.find(team => team.id == this.game.winnerId);

            this.canvas.classList.add('finished');

            this.winnerName.style.color = winner.color;
            this.winnerName.textContent = winner.name;
            this.winnerText.classList.remove('hide');
        }
        else if (this.game.ended == true) {
            this.canvas.classList.add('finished');
            this.noWinnerText.classList.remove('hide');
        }
    }

    onClick(event) {
        if (this.finished || this.fetching) {
            return;
        }

        const x = event.clientX - this.leftOffset;
        const y = event.clientY - this.topOffset;

        const square = this.getMatchingSquare(x, y);

        if (!square) {
            throw new Error('Woa sth went very wrong.');
        }


        this.fetching = true;

        sendPlacement({
            gameId: this.game.id,
            x: square.relativeX,
            y: square.relativeY
        }, (error, result) => {
            this.fetching = false;

            if (error) {
                return console.error(error);
            }

            if (result.newMark) {
                const mark = result.newMark;
                const team = this.teams.find(team => team.id == mark.teamId);

                this.ctx.strokeStyle = team.color;

                if (team.mark.toString() === ClassicTicTacToe.CROSS.toString()) {
                    this.drawCross(square);
                }
                else {
                    this.drawCircle(square);
                }
            }

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

    drawCross({centerX, centerY, x, y}) {
        const midX = this.widthPart / 4;
        const midY = this.heightPart / 4;

        this.empty({x, y});

        this.ctx.lineWidth = 2;
        this.ctx.beginPath();
        this.ctx.moveTo(centerX, centerY);
        this.ctx.lineTo(x + midX, y + midY);

        this.ctx.moveTo(centerX, centerY);
        this.ctx.lineTo(centerX + midX, centerY + midY);

        this.ctx.moveTo(centerX, centerY);
        this.ctx.lineTo(centerX + midX, y + midY);

        this.ctx.moveTo(centerX, centerY);
        this.ctx.lineTo(x + midX, centerY + midY);

        this.ctx.closePath();
        this.ctx.stroke();
        this.ctx.lineWidth = 1;
    }

    drawCircle({centerX, centerY, x, y}) {
        const radius = this.widthPart / 4;

        this.empty({x, y});

        this.ctx.lineWidth = 2;
        this.ctx.beginPath();
        this.ctx.arc(centerX, centerY, radius, Math.PI * 2, 0);
        this.ctx.stroke();
        this.ctx.lineWidth = 1;
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
