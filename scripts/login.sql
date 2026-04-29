CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    roles ENUM('etudiant', 'admin') DEFAULT 'etudiant'
);

INSERT INTO utilisateurs(email, mot_de_passe, roles) VALUES
('admin@gmail.com', 'admin123', 'admin'),
('test@gmail.com', 'test123', 'etudiant');