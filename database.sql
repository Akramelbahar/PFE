create database ests ;
use ests;

CREATE TABLE Utilisateur (
                             id INT PRIMARY KEY AUTO_INCREMENT,  -- Automatic ID generation
                             nom VARCHAR(255) NOT NULL,
                             prenom VARCHAR(255) NOT NULL,
                             email VARCHAR(191) UNIQUE NOT NULL,  -- Reduced length to avoid key size issues
                             motDePasse VARCHAR(255) NOT NULL,
                             dateInscription DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Default current timestamp
                             derniereConnexion DATETIME,
                             status BOOLEAN DEFAULT TRUE  -- Default to active status
);

CREATE TABLE Admin (
                       utilisateurId INT PRIMARY KEY,
                       FOREIGN KEY (utilisateurId) REFERENCES Utilisateur(id) ON DELETE CASCADE
);

CREATE TABLE Chercheur (
                           utilisateurId INT PRIMARY KEY,
                           domaineRecherche VARCHAR(255),
                           bio TEXT,
                           FOREIGN KEY (utilisateurId) REFERENCES Utilisateur(id) ON DELETE CASCADE
);

CREATE TABLE ProjetRecherche (
                                 id INT PRIMARY KEY AUTO_INCREMENT,
                                 titre VARCHAR(255) NOT NULL,
                                 description TEXT,
                                 budget DECIMAL(10,2),
                                 dateDebut DATETIME,
                                 dateFin DATETIME,
                                 chefProjet INT NULL,  -- Allow NULL for ON DELETE SET NULL
                                 dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
                                 FOREIGN KEY (chefProjet) REFERENCES Utilisateur(id) ON DELETE SET NULL
);

CREATE TABLE MembreBureauExecutif (
                                      utilisateurId INT PRIMARY KEY,
                                      role ENUM('President', 'VicePresident', 'GeneralSecretary', 'Treasurer', 'ViceTreasurer', 'Counselor') NOT NULL,
                                      Mandat DECIMAL(10,2),
                                      permissions TEXT,
                                      chercheurId INT NULL,  -- Allow NULL for ON DELETE SET NULL
                                      FOREIGN KEY (utilisateurId) REFERENCES Utilisateur(id) ON DELETE CASCADE,
                                      FOREIGN KEY (chercheurId) REFERENCES Chercheur(utilisateurId) ON DELETE SET NULL
);

CREATE TABLE Evenement (
                           id INT PRIMARY KEY AUTO_INCREMENT,
                           titre VARCHAR(255) NOT NULL,
                           description TEXT,
                           lieu VARCHAR(255),
                           createurId INT NULL,  -- Allow NULL for ON DELETE SET NULL
                           dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
                           projetId INT NULL,    -- Optional foreign key
                           FOREIGN KEY (createurId) REFERENCES Utilisateur(id) ON DELETE SET NULL,
                           FOREIGN KEY (projetId) REFERENCES ProjetRecherche(id) ON DELETE SET NULL
);

CREATE TABLE Seminaire (
                           evenementId INT PRIMARY KEY,
                           date DATETIME,
                           FOREIGN KEY (evenementId) REFERENCES Evenement(id) ON DELETE CASCADE
);

CREATE TABLE Conference (
                            evenementId INT PRIMARY KEY,
                            dateDebut DATETIME,
                            dateFin DATETIME,
                            FOREIGN KEY (evenementId) REFERENCES Evenement(id) ON DELETE CASCADE
);

CREATE TABLE Workshop (
                          evenementId INT PRIMARY KEY,
                          instructorId INT NULL,  -- Allow NULL for ON DELETE SET NULL
                          dateDebut DATETIME,
                          dateFin DATETIME,
                          FOREIGN KEY (evenementId) REFERENCES Evenement(id) ON DELETE CASCADE,
                          FOREIGN KEY (instructorId) REFERENCES Utilisateur(id) ON DELETE SET NULL
);

CREATE TABLE Publication (
                             id INT PRIMARY KEY AUTO_INCREMENT,
                             titre VARCHAR(255) NOT NULL,
                             contenu TEXT,
                             auteurId INT NULL,  -- Allow NULL for ON DELETE SET NULL
                             datePublication DATETIME DEFAULT CURRENT_TIMESTAMP,
                             evenementId INT NULL,  -- Optional foreign key
                             projetId INT NULL,     -- Optional foreign key
                             documents TEXT,        -- String to store serialized array (e.g., JSON)
                             mediaUrl TEXT,
                             FOREIGN KEY (auteurId) REFERENCES Utilisateur(id) ON DELETE SET NULL,
                             FOREIGN KEY (evenementId) REFERENCES Evenement(id) ON DELETE SET NULL,
                             FOREIGN KEY (projetId) REFERENCES ProjetRecherche(id) ON DELETE SET NULL
);

CREATE TABLE Article (
                         publicationId INT PRIMARY KEY,
                         FOREIGN KEY (publicationId) REFERENCES Publication(id) ON DELETE CASCADE
);

CREATE TABLE Livre (
                       publicationId INT PRIMARY KEY,
                       FOREIGN KEY (publicationId) REFERENCES Publication(id) ON DELETE CASCADE
);

CREATE TABLE Chapitre (
                          publicationId INT PRIMARY KEY,
                          LivrePere INT NULL,  -- Allow NULL for ON DELETE SET NULL
                          FOREIGN KEY (publicationId) REFERENCES Publication(id) ON DELETE CASCADE,
                          FOREIGN KEY (LivrePere) REFERENCES Livre(publicationId) ON DELETE SET NULL
);

CREATE TABLE Actualite (
                           id INT PRIMARY KEY AUTO_INCREMENT,
                           titre VARCHAR(255) NOT NULL,
                           contenu TEXT,
                           auteurId INT NULL,  -- Allow NULL for ON DELETE SET NULL
                           datePublication DATETIME DEFAULT CURRENT_TIMESTAMP,
                           mediaUrl TEXT,  -- Consider using JSON for multiple URLs
                           documents TEXT,
                           evenementId INT NULL,  -- Optional foreign key
                           FOREIGN KEY (auteurId) REFERENCES Utilisateur(id) ON DELETE SET NULL,
                           FOREIGN KEY (evenementId) REFERENCES Evenement(id) ON DELETE SET NULL
);

CREATE TABLE Partner (
                         id INT PRIMARY KEY AUTO_INCREMENT,
                         nom VARCHAR(255) NOT NULL,
                         contact VARCHAR(255),
                         logo VARCHAR(255),
                         siteweb VARCHAR(255)
);

CREATE TABLE ProjetPartner (
                               projetId INT,
                               partnerId INT,
                               PRIMARY KEY (projetId, partnerId),
                               FOREIGN KEY (projetId) REFERENCES ProjetRecherche(id) ON DELETE CASCADE,
                               FOREIGN KEY (partnerId) REFERENCES Partner(id) ON DELETE CASCADE
);

CREATE TABLE Participe (
                           id INT PRIMARY KEY AUTO_INCREMENT,
                           projetId INT,
                           utilisateurId INT,
                           role ENUM('chercheur', 'participant'),
                           FOREIGN KEY (projetId) REFERENCES ProjetRecherche(id) ON DELETE CASCADE,
                           FOREIGN KEY (utilisateurId) REFERENCES Utilisateur(id) ON DELETE CASCADE
);

CREATE TABLE IdeeRecherche (
                               id INT PRIMARY KEY AUTO_INCREMENT,
                               titre VARCHAR(255) NOT NULL,
                               description TEXT,
                               proposePar INT NULL,  -- Allow NULL for ON DELETE SET NULL
                               dateProposition DATETIME DEFAULT CURRENT_TIMESTAMP,
                               status ENUM('en attente', 'approuvée', 'refusé'),
                               FOREIGN KEY (proposePar) REFERENCES Utilisateur(id) ON DELETE SET NULL
);

CREATE TABLE Contact (
                         id INT PRIMARY KEY AUTO_INCREMENT,
                         nom VARCHAR(255) NOT NULL,
                         email VARCHAR(191) NOT NULL,  -- Reduced length to avoid key length issues
                         message TEXT,
                         dateEnvoi DATETIME DEFAULT CURRENT_TIMESTAMP
);
