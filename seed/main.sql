DROP TABLE IF EXISTS MarkModel;
CREATE TABLE MarkModel (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  icon VARCHAR(255),
  type ENUM('warrior', 'wizard', 'archer')
) ENGINE=InnoDB;

DROP TABLE IF EXISTS Team;
CREATE TABLE Team (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  color INT,
  createdAt DATETIME
) ENGINE=InnoDB;

DROP TABLE IF EXISTS Game;
CREATE TABLE Game (
  id INT PRIMARY KEY AUTO_INCREMENT,
  startedAt TIMESTAMP,
  initialPoints INT,
  doubleAttack INT,
  gridWidth INT,
  gridHeight INT,
  team1Id INT,
  team2Id INT,
  winnerId INT
) ENGINE=InnoDB;

DROP TABLE IF EXISTS Actions;
CREATE TABLE Actions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('placement', 'attack', 'spell'),
  x INT,
  y INT,
  teamMarkId INT,
  gameId INT
) ENGINE=InnoDB;

DROP TABLE IF EXISTS Configuration;
CREATE TABLE Configuration (
  id INT PRIMARY KEY AUTO_INCREMENT,
  initialPoints INT,
  doubleAttack INT,
  gridWidth INT,
  gridHeight INT
) ENGINE=InnoDB;


DROP TABLE IF EXISTS Mark;
CREATE TABLE Mark (
  id INT PRIMARY KEY AUTO_INCREMENT,
  damage INT,
  hp INT,
  mana INT,
  x INT,
  y INT,
  teamId INT,
  markModelId INT
) ENGINE=InnoDB;


ALTER TABLE Actions ADD
  CONSTRAINT FK_Actions_id_Game
  FOREIGN KEY (gameID)
  REFERENCES Game(id);

ALTER TABLE Actions ADD
  CONSTRAINT FK_Actions_id_Mark
  FOREIGN KEY (teamMarkId)
  REFERENCES Mark(id);

ALTER TABLE Mark ADD
  CONSTRAINT FK_Mark_id_MarkModel
  FOREIGN KEY (markModelId)
  REFERENCES MarkModel(id);

ALTER TABLE Mark ADD
  CONSTRAINT FK_Mark_id_Team
  FOREIGN KEY (teamId)
  REFERENCES Team(id);

ALTER TABLE Game ADD
  CONSTRAINT FK_Game_team1Id_Team
  FOREIGN KEY(team1Id)
  REFERENCES Team(id);

ALTER TABLE Game ADD
  CONSTRAINT FK_Game_team2Id_Team
  FOREIGN KEY(team2Id)
  REFERENCES Team(id);

ALTER TABLE Game ADD
  CONSTRAINT FK_Game_winnerId_Team
  FOREIGN KEY(winnerId)
  REFERENCES Team(id);
