CREATE DATABASE IF NOT EXISTS crafti;
USE crafti;

CREATE TABLE Filiala_Crafti (
    id_filiala INT PRIMARY KEY AUTO_INCREMENT,
    adresa_filiala VARCHAR(255) NOT NULL,
    nr_telefon_filiala VARCHAR(15) NOT NULL
);

CREATE TABLE Eveniment (
    id_eveniment INT PRIMARY KEY AUTO_INCREMENT,
    denumire_eveniment VARCHAR(100) NOT NULL,
    responsabil_eveniment VARCHAR(100) NOT NULL,
    nr_loc_disponibile INT NOT NULL CHECK (nr_loc_disponibile <= 12),
    cost_eveniment DECIMAL(10, 2) NOT NULL
);

CREATE TABLE Filiala_Data_Ora (
    id_filiala_data_ora INT PRIMARY KEY AUTO_INCREMENT,
    data DATE NOT NULL,
    ora TIME NOT NULL,
    id_filiala INT NOT NULL,
    id_eveniment INT NOT NULL,
    FOREIGN KEY (id_filiala) REFERENCES Filiala_Crafti(id_filiala),
    FOREIGN KEY (id_eveniment) REFERENCES Eveniment(id_eveniment)
);

CREATE TABLE Parinte (
    id_parinte INT PRIMARY KEY AUTO_INCREMENT,
    prenume_parinte VARCHAR(50) NOT NULL,
    nr_telefon_parinte VARCHAR(15) NOT NULL
);

CREATE TABLE Copil (
    id_copil INT PRIMARY KEY AUTO_INCREMENT,
    prenume_copil VARCHAR(50) NOT NULL,
    an_nastere_copil YEAR NOT NULL,
    id_parinte INT NOT NULL,
    FOREIGN KEY (id_parinte) REFERENCES Parinte(id_parinte)
);

CREATE TABLE Inscriere_Eveniment (
    id_inscriere INT PRIMARY KEY AUTO_INCREMENT,
    data_inscriere DATETIME NOT NULL,
    id_filiala_data_ora INT NOT NULL,
    id_copil INT NOT NULL,
    FOREIGN KEY (id_filiala_data_ora) REFERENCES Filiala_Data_Ora(id_filiala_data_ora),
    FOREIGN KEY (id_copil) REFERENCES Copil(id_copil)
);

CREATE TABLE Manager (
    id_manager INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    parola VARCHAR(255) NOT NULL
);

-- Date demo
INSERT INTO Eveniment (denumire_eveniment, responsabil_eveniment, nr_loc_disponibile, cost_eveniment)
VALUES ('Scoala de fotbal', 'Denis Crudu', 10, 100.00);

INSERT INTO Parinte (prenume_parinte, nr_telefon_parinte)
VALUES ('Jenea', '06864354');

Insert into Manager (id_manager,login,parola)
VALUES (1,'Jenea','Jenea123')