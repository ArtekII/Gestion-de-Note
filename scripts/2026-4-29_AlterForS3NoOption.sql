
ALTER TABLE semestre
  ADD COLUMN annee VARCHAR(20) NOT NULL DEFAULT 'L2';

ALTER TABLE matiere
  MODIFY COLUMN id_option INT NULL;

ALTER TABLE etudiant
  MODIFY COLUMN id_option INT NULL,
  MODIFY COLUMN note DECIMAL(5,2) NOT NULL;

UPDATE etudiant SET id_option = NULL WHERE id_semestre = 1;
UPDATE matiere SET id_option = NULL WHERE id_semestre = 1;
