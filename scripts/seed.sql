-- SEMESTRES
INSERT INTO semestre (nom) VALUES
('SEMESTRE 3'),
('SEMESTRE 4');


-- OPTIONS
INSERT INTO options (nom, responsable) VALUES
('Développement', 'Razafijoelina Tahina'),
('Base de Donnees et Reseaux', 'Rakotomalala Vahatriniana'),
('Web et Design', 'Rabenanahary Rojo')


-- LISTE DES MATIERES
INSERT INTO liste_matiere (code_matiere, Nom_matiere) VALUES
('INF201', 'Programmation orientée objet'),
('INF202', 'Bases de données objets'),
('INF203', 'Programmation système'),
('INF208', 'Réseaux informatiques'),
('MTH201', 'Méthodes numériques'),
('ORG201', 'Bases de gestion'),
('INF207', 'Eléments Algorithmique'),
('INF210', 'Mini-projet de développement'),
('INF204', 'Système Information géographique'),
('MTH203', 'MAO'),
('MTH206', 'Optimisation');


-- MATIERES : Semestre 3
INSERT INTO matiere (id_semestre, id_option, code_matiere, coefficient) VALUES
(1, 1, 'INF201', 6),
(1, 1, 'INF202', 6),
(1, 1, 'INF203', 4),
(1, 1, 'INF208', 6),
(1, 1, 'MTH201', 4),
(1, 1, 'ORG201', 4);


-- MATIERES : Semestre 4
INSERT INTO matiere (id_semestre, id_option, code_matiere, coefficient) VALUES
(2, 1, 'INF207', 6),
(2, 1, 'INF210', 10),
(2, 1, 'INF204', 6),
(2, 1, 'MTH203', 4),
(2, 1, 'MTH206', 4);


-- ETUDIANT (exemple avec un seul étudiant)
INSERT INTO etudiant
(nom, id_option, id_semestre, id_matiere, note, credit, resultat)
VALUES
('Etudiant 1', 1, 1, 1, 10.5, 6, 'P'),
('Etudiant 1', 1, 1, 2, 14, 6, 'B'),
('Etudiant 1', 1, 1, 3, 11, 4, 'P'),
('Etudiant 1', 1, 1, 4, 10, 6, 'P'),
('Etudiant 1', 1, 1, 5, 6.5, 4, 'Comp.'),
('Etudiant 1', 1, 1, 6, 13, 4, 'AB'),

('Etudiant 1', 1, 2, 7, 9.5, 6, 'Comp.'),
('Etudiant 1', 1, 2, 8, 12.2, 10, 'AB'),
('Etudiant 1', 1, 2, 9, 12, 6, 'AB'),
('Etudiant 1', 1, 2, 10, 11.33, 4, 'P'),
('Etudiant 1', 1, 2, 11, 12.25, 4, 'AB');