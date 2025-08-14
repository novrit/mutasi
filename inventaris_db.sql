CREATE DATABASE IF NOT EXISTS db_inventaris;
USE db_inventaris;
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE, password_hash VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS inventaris (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_barang VARCHAR(100),
  barang VARCHAR(200),
  merek VARCHAR(100),
  ukuran VARCHAR(50),
  bahan VARCHAR(100),
  tahun VARCHAR(10),
  pabrik VARCHAR(100),
  rangka VARCHAR(100),
  mesin VARCHAR(100),
  polisi VARCHAR(50),
  nomor VARCHAR(50),
  harga DECIMAL(15,2),
  jumlah INT,
  nilai DECIMAL(15,2),
  kondisi VARCHAR(50),
  ruangan VARCHAR(100),
  sub_ruangan VARCHAR(200),
  gambar VARCHAR(255)
);
-- sample data
INSERT INTO inventaris (no_barang,barang,merek,ukuran,bahan,tahun,pabrik,nomor,harga,jumlah,nilai,kondisi,ruangan,sub_ruangan) VALUES
('INV-001','Kursi Kantor','Futura','M','Kain','2022','PT Futura','001',750000,10,7500000,'Baik','REKTORAT','R. Rektor'),
('INV-002','Laptop','Lenovo','14"','Metal','2023','Lenovo','002',9500000,2,19000000,'Sangat Baik','FST','R. Lab Teknik Sipil'),
('INV-003','Meja','Olympic','meja1.jpg','L','Kayu','2021','Olympic','003',500000,5,2500000,'Baik','FE','R. Dekan');
