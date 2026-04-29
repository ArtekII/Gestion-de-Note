
ALTER TABLE semestre
  ADD COLUMN annee VARCHAR(20) NOT NULL DEFAULT 'L2';

ALTER TABLE matiere
  MODIFY COLUMN id_option INT NULL;

ALTER TABLE etudiant
  MODIFY COLUMN id_option INT NULL,
  MODIFY COLUMN note DECIMAL(5,2) NOT NULL;

UPDATE etudiant SET id_option = NULL WHERE id_semestre = 1;
UPDATE matiere SET id_option = NULL WHERE id_semestre = 1;

CREATE TABLE IF NOT EXISTS groupe_optionnel(
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  id_semestre INT NULL,
  id_option INT NULL,
  FOREIGN KEY (id_semestre) REFERENCES semestre(id),
  FOREIGN KEY (id_option) REFERENCES options(id)
);

CREATE TABLE IF NOT EXISTS groupe_optionnel_matiere(
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_groupe_optionnel INT NOT NULL,
  code_matiere VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_groupe_optionnel) REFERENCES groupe_optionnel(id) ON DELETE CASCADE,
  FOREIGN KEY (code_matiere) REFERENCES liste_matiere(code_matiere)
);

INSERT INTO groupe_optionnel (nom, id_semestre, id_option)
SELECT
  'S4 DEV - MAO / Optimisation',
  s.id,
  o.id
FROM semestre s
JOIN options o ON o.nom = 'Développement'
WHERE s.nom = 'SEMESTRE 4'
  AND NOT EXISTS (
    SELECT 1 FROM groupe_optionnel go WHERE go.nom = 'S4 DEV - MAO / Optimisation'
  );

INSERT INTO groupe_optionnel_matiere (id_groupe_optionnel, code_matiere)
SELECT go.id, 'MTH203'
FROM groupe_optionnel go
WHERE go.nom = 'S4 DEV - MAO / Optimisation'
  AND NOT EXISTS (
    SELECT 1
    FROM groupe_optionnel_matiere gm
    WHERE gm.id_groupe_optionnel = go.id AND gm.code_matiere = 'MTH203'
  );

INSERT INTO groupe_optionnel_matiere (id_groupe_optionnel, code_matiere)
SELECT go.id, 'MTH206'
FROM groupe_optionnel go
WHERE go.nom = 'S4 DEV - MAO / Optimisation'
  AND NOT EXISTS (
    SELECT 1
    FROM groupe_optionnel_matiere gm
    WHERE gm.id_groupe_optionnel = go.id AND gm.code_matiere = 'MTH206'
  );
