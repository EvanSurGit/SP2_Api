-- ======================
-- Création de la base
-- ======================
CREATE DATABASE IF NOT EXISTS groupe2;
USE groupe2;

-- ======================
-- TABLE categories
-- ======================
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(100) NOT NULL,
  description TEXT,
  path_image VARCHAR(255)
);

INSERT INTO categories (id, libelle, description, path_image) VALUES
(1, 'Animaux', 'Puzzles representant des animaux', 'images/categories/animaux.jpg'),
(2, 'Paysages', 'Beaux paysages naturels', 'images/categories/paysages.jpg'),
(3, 'Villes', 'Puzzles de villes celebres', 'images/categories/villes.jpg'),
(4, 'Art', 'Reproductions d\'oeuvres celebres', 'images/categories/art.jpg'),
(5, 'Enfants', 'Puzzles adaptes aux enfants', 'images/categories/enfants.jpg');

-- ======================
-- TABLE users
-- ======================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  admin TINYINT(1) DEFAULT 0
);

INSERT INTO users (id, name, email, password, admin) VALUES
(1, 'mael', 'm@m', '$2y$12$xDX9lavhq3TsXo9Mg.VUveWkPh9O7ENlDMj6reMoiGHXTVokmfhmq', 0),
(3, 'dalil', 'd@d', '$2y$12$02tOwr/HJ6AElE5DxgCVW.VhUKAp8cT.T/treNhVrB/dh/A.p/BMu', 0),
(5, 'a', 'a@a', '$2y$12$ZVYbOX/oUUib/5MUed1Cu.RmAT1xyZpwtFdXTAAK0fnHia/i8i/.q', 0),
(6, 'elie', 'e@e', '$2y$12$zPHIAWJZ0dvPlbqCL2rxD.5Eupqp2wZUDhMZJ06SVwVcosnG0JdWq', 0),
(7, 'clement', 'c@c', '$2y$12$04UFQGDDIMt8gcyuVteyGOEZQsRVwRzfO2/Wxj5gB/lSHYzhRQGQy', 1),
(8, 'ClementPasAdmin', 'cpa@cpa', '$2y$12$Mped91.aBore3BouUTRum.LC3US09gUQEZ3i.FwFu8Na7SdoLHFWC', 0);

-- ======================
-- TABLE puzzles
-- ======================
CREATE TABLE puzzles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  description TEXT,
  path_image VARCHAR(255),
  prix DECIMAL(6,2),
  stock INT,
  categorie_id INT,
  FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

INSERT INTO puzzles VALUES
(13, 'Lion de la Savane', 'Puzzle 1000 pieces representant un lion majestueux', 'images/puzzles/logo.png', 19.99, 15, 1),
(14, 'Chat Mignon', 'Puzzle 500 pieces avec un adorable chat', 'images/puzzles/chat.jpg', 14.99, 40, 1),
(15, 'Montagnes au coucher du soleil', 'Puzzle 1500 pieces paysage montagneux', 'images/puzzles/montagnes.jpg', 24.99, 15, 2),
(16, 'Paris Tour Eiffel', 'Puzzle 1000 pieces representant la Tour Eiffel', 'images/puzzles/paris.jpg', 21.99, 20, 3),
(17, 'La Nuit Etoilee', 'Puzzle inspire du celebre tableau de Van Gogh', 'images/puzzles/nuit_etoilee.jpg', 18.99, 30, 4),
(18, 'Puzzle Pat patrouille', 'Puzzle enfant 100 pieces Pat patrouille', 'images/puzzles/pat_patrouille.jpg', 9.99, 50, 5),
(21, 'alizee la queen', 'ma beauté', 'images/puzzles/zizé.png', 999.99, 1, 2),
(22, 'BurjKhalifa', 'oui', 'images/puzzles/burj.png', 258.00, 1, 3),
(23, 'Sleepoverz', 'Le meilleur groupe...', 'images/puzzles/sleepoverz.png', 15.00, 130, 4),
(24, 'elie t\'es trop chou', '15454', 'images/puzzles/elie.png', 58.00, 200, 1);

-- ======================
-- TABLE paniers
-- ======================
CREATE TABLE paniers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total DECIMAL(10,2) DEFAULT 0,
  status VARCHAR(20) DEFAULT 'open',
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO paniers VALUES
(26, 1, 2000.00, 'completed'),
(27, 1, 0.00, 'completed'),
(28, 1, 522.00, 'completed'),
(29, 1, 0.00, 'completed'),
(30, 1, 25.00, 'open'),
(31, 3, 0.00, 'open');

-- ======================
-- TABLE orders
-- ======================
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  date_commande DATETIME,
  total DECIMAL(10,2),
  status VARCHAR(20) DEFAULT 'en_attente',
  created_at DATETIME,
  updated_at DATETIME,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO orders VALUES
(8, 1, '2026-03-19 14:38:55', 2000.00, 'en_attente', '2026-03-19 14:38:55', '2026-03-19 14:38:55'),
(9, 1, '2026-03-19 15:13:22', 522.00, 'en_attente', '2026-03-19 15:13:22', '2026-03-19 15:13:22');

-- ======================
-- TABLE order_puzzle
-- ======================
CREATE TABLE order_puzzle (
  order_id INT,
  puzzle_id INT,
  quantite INT DEFAULT 1,
  prix DECIMAL(10,2) DEFAULT 0,
  created_at DATETIME,
  updated_at DATETIME,
  PRIMARY KEY (order_id, puzzle_id),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (puzzle_id) REFERENCES puzzles(id) ON DELETE CASCADE
);

INSERT INTO order_puzzle VALUES
(8, 21, 2, 1000.00, '2026-03-19 14:38:55', '2026-03-19 14:38:55'),
(9, 24, 9, 58.00, '2026-03-19 15:13:22', '2026-03-19 15:13:22');

-- ======================
-- TABLE delivery_adresses
-- ======================
CREATE TABLE delivery_adresses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  adresse TEXT,
  ville VARCHAR(100),
  code_postal VARCHAR(20),
  pays VARCHAR(100),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO delivery_adresses VALUES
(1, 1, '12 jean', 'rive de vier', '42800', 'France'),
(2, 3, '40 RUE', 'Rive de gier', '42800', 'France'),
(3, 5, '1 test', 'test', '1000', 'France');
