create table semestre(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL
);

create table options(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    responsable VARCHAR(50) NOT NULL
);

create table liste_matiere(
    id INT PRIMARY KEY AUTO_INCREMENT,
    code_matiere VARCHAR(20) NOT NULL,
    Nom_matiere VARCHAR(50) NOT NULL
);

create table matiere(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_semestre INT NOT NULL,
    id_option INT NOT NULL,
    code_matiere VARCHAR(20) NOT NULL,
    coefficient INT NOT NULL,

    FOREIGN KEY (id_semestre) REFERENCES semestre(id),
    FOREIGN KEY (id_option) REFERENCES options(id),
    FOREIGN KEY (code_matiere) REFERENCES liste_matiere(code_matiere)
);

create table etudiant(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    id_option INT NOT NULL,
    id_semestre INT NOT NULL,
    id_matiere INT NOT NULL,
    note INT NOT NULL,
    credit INT NOT NULL,
    resultat VARCHAR(20) NOT NULL,

    FOREIGN KEY (id_semestre) REFERENCES semestre(id),
    FOREIGN KEY (id_option) REFERENCES options(id)
);