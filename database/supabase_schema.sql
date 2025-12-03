-- Create ENUM types
CREATE TYPE user_level AS ENUM ('admin', 'normal_user');
CREATE TYPE record_status AS ENUM ('good', 'broken', 'not taken', 'pending', 'fixing', 'decline');

-- Table structure for table products
CREATE TABLE products (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  photo VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- Table structure for table users
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT DEFAULT NULL,
  divisi VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  password_user VARCHAR(255) NOT NULL,
  username VARCHAR(100) NOT NULL,
  badge VARCHAR(30) DEFAULT NULL,
  level user_level DEFAULT 'normal_user'
);

-- Table structure for table records
CREATE TABLE records (
  id_records SERIAL PRIMARY KEY,
  id_users INT DEFAULT NULL,
  id_products INT DEFAULT NULL,
  status record_status NOT NULL DEFAULT 'good',
  no_serial VARCHAR(255) NOT NULL,
  no_inventaris VARCHAR(255) NOT NULL,
  note_record TEXT NOT NULL,
  record_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_records_users FOREIGN KEY (id_users) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT fk_records_products FOREIGN KEY (id_products) REFERENCES products (id) ON DELETE SET NULL
);

-- Table structure for table repair
CREATE TABLE repair (
  id_repair SERIAL PRIMARY KEY,
  id_user INT NOT NULL,
  id_record INT NOT NULL,
  note TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_repair_users FOREIGN KEY (id_user) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_repair_records FOREIGN KEY (id_record) REFERENCES records (id_records) ON DELETE CASCADE
);

-- Dumping data for table products
INSERT INTO products (id, name, photo, description) VALUES
(4, 'Keyboard Logitech', 'Logitech-K120.png', 'Office Keyboard'),
(5, 'FantechATOM63', 'FantechATOM63.png', 'Keyboard Gaming'),
(6, 'FantechKatanaSVX9s', 'FantechKatanaSVX9s.jpg', 'Mouse Gaming'),
(7, 'FantechVx6 Phantom ii', 'FantechVx6Phantomii.jpg', 'Mouse Gaming'),
(8, 'Gamen Titan V', 'gamenTitanV.jpg', 'Keyboard Gaming'),
(9, 'Logitech B100', 'LogitechB100.jpg', 'Office Mouse'),
(10, 'Logitech B175', 'LogitechB175.jpg', 'Office Mouse Wireless'),
(11, 'Redragon P035', 'RedragonP035.jpg', 'keyboard hand rest'),
(12, 'Rexus MX5.2', 'RexusMX5,2.jpg', 'Gaming'),
(13, 'Rexus Mx10RGB', 'RexusMx10RGB.jpg', 'Gaming Keyboard'),
(15, 'Logitech Mouse B100', 'LogitechB100.jpg', 'Office Mouse'),
(16, 'Logitech B100', 'LogitechB100.jpg', 'Keyboard Office Wired');

-- Dumping data for table users
INSERT INTO users (id, name, age, divisi, email, description, photo, password_user, username, badge, level) VALUES
(18, 'admin', 20, 'Layanan TI', 'admin@gmail.com', 'Mr.Admin', 'profile.jpg', '$2y$10$ZdWSNZm/a6aB4BWkbUavPOb1EWuens9ENzzAWBiEAv7KhJZLLCeH2', 'admin', '12-34', 'admin'),
(20, 'Im0somn1s', 20, 'Layanan TI', 'Im0somn1s@gmail.com', 'Mahasiswa', 'man.jpg', '$2y$10$EmSyorc5Rv6xQhYtdBKzgunx.46M0kUGNHCyYNQEOq.l7KC6a1oqK', 'Im0somn1s', '12-34', 'normal_user'),
(21, 'Harjay', 22, 'Layanan TI', 'harjay@gmail.com', 'Mahasiswa', 'woman.jpg', '$2y$10$NMR2ag2l2sbK3RToVKFlDunmD0LAYydT5aeiRIHHX0AlqbLWUKnVi', '123', '12-34', 'normal_user'),
(22, 'Elda', 19, 'Layanan TI', 'elda@gmail.com', 'Mahasiswa', 'woman.jpg', '$2y$10$6hw218NMYzZka8nnaqoLOuFXQNEqj8sFZZjtj5nuakWTq1SAXqWvq', 'elda', '12-34', 'normal_user'),
(24, 'arif', 20, 'Layanan TI', 'arif@gmail.com', 'mahasiswa', 'man.jpg', '$2y$10$sB97eTVKPITvUpnUeAKOMuK9gsIDLMkgGOjVhYraY8sLJDVldDBSi', 'arif', '12-34', 'normal_user'),
(25, 'azel', 20, 'Layanan TI', 'azel@gmail.com', 'Pegawai Pusri', 'woman.jpg', '$2y$10$jDN08WKkYr44qEwBhJiaC.lQnmN941XUOwOnbeFGY9WBUos3loepS', 'azel', '12-34', 'normal_user'),
(26, 'hembang', 20, 'Layanan TI', 'hembang@gmail.com', 'Pegawai Pusri', 'profile.jpg', '$2y$10$pLZpIVIyy3g2jMSbSCY00e1pxYfGaiqwEda/xwltt6stiw5V0rxMW', 'hembang', '12-34', 'normal_user'),
(27, 'test', 20, 'Layanan TI', 'test@gmail.com', 'Pegawai Kontrak', 'man.jpg', '$2y$10$KK63diDTvBswAyHIr8ZFiOnd1Cdgq3utyPB9CH/sqiNx/etOkWm3e', 'test', '12-34-45', 'normal_user'),
(28, 'wijaya', 20, 'Layanan TI', 'wijaya@gmail.com', 'Kontrak 1 tahun 2024-2025', 'man.jpg', '$2y$10$kwV8wyIAt1K91uIO5833XuBnnMNfAyY2XyZLjgSWV6O8bN2j6xIvC', 'wijaya', '12-34-56', 'normal_user');

-- Dumping data for table records
INSERT INTO records (id_records, id_users, id_products, status, no_serial, no_inventaris, note_record, record_time) VALUES
(24, 28, 9, 'good', '2348PUBG123', '555DAWQ2', '34', '2025-01-31 14:20:35'),
(25, 28, 13, 'fixing', '2348FF', '343TES', '', '2025-01-29 20:04:58'),
(27, 28, 10, 'not taken', '2348FFF', '54TE', 'Sudah selesai silakan ambil', '2025-02-19 08:16:12'),
(39, 27, 15, 'not taken', '65EDFF', '5TEDFED', 'Silakan Ambil sudah diperbaiki', '2025-01-29 20:04:38'),
(40, 27, 5, 'good', '4ERDW', 'DSFGHTJ', 'ga bisa', '2025-01-29 20:09:07'),
(42, 26, 11, 'good', '35EF', 'RT43WR', '', '2025-01-29 19:58:59'),
(43, 22, 4, 'good', 'HI67', '34RE', '', '2025-01-29 20:00:42'),
(44, 27, 4, 'decline', '23REFED', '3E2RWE12', 'tidak bisa diperbaiki', '2025-02-12 05:50:03');

-- Dumping data for table repair
INSERT INTO repair (id_repair, id_user, id_record, note, created_at) VALUES
(70, 27, 39, '', '2025-01-27 15:29:05'),
(73, 28, 25, '', '2025-01-30 03:03:28');

-- Reset sequences to max id (important for PostgreSQL)
SELECT setval('products_id_seq', (SELECT MAX(id) FROM products));
SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));
SELECT setval('records_id_records_seq', (SELECT MAX(id_records) FROM records));
SELECT setval('repair_id_repair_seq', (SELECT MAX(id_repair) FROM repair));
