# 📝 SQL REFERENCE - Database Statements

SQL commando's die je nodig hebt.

---

## DATABASE NIVEAU

```sql
-- Database aanmaken
CREATE DATABASE product_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Database kiezen
USE product_crud;

-- Database bekijken
SHOW DATABASES;

-- Database verwijderen (VOORZICHTIG!)
DROP DATABASE product_crud;

-- Alle tabellen tonen
SHOW TABLES;
```

---

## TABEL STRUCTUUR

```sql
-- Categorie tabel
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product tabel MET foreign key
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    quantity INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel schema tonen
DESCRIBE products;
SHOW CREATE TABLE products;
```

---

## CREATE (Toevoegen)

```sql
-- 1 categorie toevoegen
INSERT INTO categories (name, description) 
VALUES ('Elektronica', 'Elektronische apparaten');

-- Multiple categorieën
INSERT INTO categories (name, description) VALUES 
('Boeken', 'Verschillende boeken'),
('Kleding', 'Kleren en accessoires');

-- Product toevoegen (hangt van categorie 1 af)
INSERT INTO products (category_id, name, description, price, quantity)
VALUES (1, 'Laptop', 'Gaming laptop i7', 999.99, 10);

-- Multiple producten
INSERT INTO products (category_id, name, description, price, quantity) VALUES
(1, 'Mouse', 'Draadloze muis', 25.00, 50),
(1, 'Keyboard', 'Mechanisch keyboard', 150.00, 20),
(2, 'PHP Handboek', 'Leer PHP', 49.99, 5);

-- Last ID opvragen
SELECT LAST_INSERT_ID();
```

---

## READ (Lezen)

```sql
-- Alle categorieën
SELECT * FROM categories;

-- Alle producten
SELECT * FROM products;

-- Met joins (producten MET categorie naam)
SELECT p.*, c.name as category_name 
FROM products p
LEFT JOIN categories c ON p.category_id = c.id;

-- 1 categorie (ID 1)
SELECT * FROM categories WHERE id = 1;

-- 1 product (ID 5)
SELECT * FROM products WHERE id = 5;

-- Producten van categorie 1
SELECT * FROM products WHERE category_id = 1;

-- Producten goedkoper dan €50
SELECT * FROM products WHERE price < 50;

-- Producten met laag voorraad
SELECT * FROM products WHERE quantity < 5;

-- Producten gesorteerd
SELECT * FROM products ORDER BY name ASC;

-- Producten omgekeerd gesorteerd
SELECT * FROM products ORDER BY price DESC;

-- Producten met LIMIT
SELECT * FROM products LIMIT 10;

-- ZOEKEN in beschrijving
SELECT * FROM products WHERE description LIKE '%Laptop%';

-- COUNT
SELECT COUNT(*) FROM products;

-- Producten per categorie
SELECT category_id, COUNT(*) as aantal 
FROM products 
GROUP BY category_id;

-- AVG prijs
SELECT AVG(price) as gemiddelde_prijs FROM products;

-- MAX prijs
SELECT MAX(price) as duurste FROM products;

-- MIN prijs
SELECT MIN(price) as goedkoopste FROM products;
```

---

## UPDATE (Wijzigen)

```sql
-- Prijs van product 1 wijzigen
UPDATE products SET price = 899.99 WHERE id = 1;

-- Naam van categorie wijzigen
UPDATE categories SET name = 'Computers' WHERE id = 1;

-- Voorraad verlagen
UPDATE products SET quantity = quantity - 5 WHERE id = 3;

-- Voorraad verhogen
UPDATE products SET quantity = quantity + 10 WHERE id = 2;

-- Multiple velden
UPDATE products 
SET price = 1299.99, quantity = 15 
WHERE id = 1;

-- Alle producten van categorie 1 korting geven (10%)
UPDATE products 
SET price = price * 0.90 
WHERE category_id = 1;

-- Updated_at timestamp
UPDATE products SET updated_at = NOW() WHERE id = 5;
```

---

## DELETE (Verwijderen)

```sql
-- 1 product verwijderen
DELETE FROM products WHERE id = 5;

-- Alle producten van categorie 1
DELETE FROM products WHERE category_id = 1;

-- Voorzichtigheid: eerst checken
SELECT * FROM products WHERE id = 5;
-- Dan delete
DELETE FROM products WHERE id = 5;

-- Categorie verwijderen (producten gaan ook weg via CASCADE)
DELETE FROM categories WHERE id = 1;

-- Alle producten goedkoper dan €20 weg
DELETE FROM products WHERE price < 20;

-- TRUNCATE (all data verwijderen, ID reset)
TRUNCATE TABLE products;
```

---

## UTILITY COMMANDO'S

```sql
-- Database info
SELECT DATABASE();
SELECT VERSION();
SELECT USER();

-- Tabel größe
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.tables
WHERE table_schema = 'product_crud';

-- Alle indexes
SHOW INDEXES FROM products;

-- Foreign keys tonen
SELECT * FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'products' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Engine info
SHOW TABLE STATUS FROM product_crud;
```

---

## PHPMYADMIN SQL QUERIES

In PHPMyAdmin SQL tab:

```sql
-- 1. Database aanmaken
CREATE DATABASE product_crud CHARACTER SET utf8mb4;

-- 2. Importeer dit SQL
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100),
    description LONGTEXT,
    price DOUBLE,
    quantity INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 3. Test data
INSERT INTO categories VALUES 
(NULL, 'Elektronica', 'Tech spullen', NOW()),
(NULL, 'Boeken', 'Lectuur', NOW());

INSERT INTO products VALUES 
(NULL, 1, 'Laptop', 'Gaming', 999.99, 5, NOW()),
(NULL, 1, 'Mouse', 'Wireless', 25.00, 100, NOW()),
(NULL, 2, 'PHP Book', 'Programmeren', 49.99, 10, NOW());
```

---

## BACKUP & RESTORE

### Backup naar bestand
```bash
# SQL dump naar file
mysqldump -u root -p product_crud > backup.sql

# Enter password when asked
```

### Restore van bestand
```bash
# Restore van SQL file
mysql -u root -p product_crud < backup.sql
```

### PHPMyAdmin Export/Import
1. Select database
2. Export tab → Download
3. Later: Import tab → Upload bestand

---

## COMMON SQL ERRORS & FIXES

| Error | Oorzaak | Fix |
|-------|---------|-----|
| "Unknown table" | Tabel niet aangemaakt | Create tabel eerst |
| "Unknown column" | Kolom typo | Check exact naam |
| "Duplicate entry" | Unieke waarde dubbel | Andere waarde |
| "Foreign key constraint" | Category niet bestaan | Category eerst aanmaken |
| "Access denied" | Verkeerd wachtwoord | Check credentials |

---

## PHPMYADMIN INTERFACE

### Data Toevoegen
1. Select tabel
2. Insert tab
3. Vul waarden in
4. Klik Go

### Data Wijzigen
1. Select tabel
2. Klik edit icon (potlood)
3. Wijzig waarden
4. Klik Go

### Data Verwijderen
1. Select tabel
2. Select rows (checkbox)
3. Klik Delete
4. Confirm

### Query Runnen
1. SQL tab
2. Plak query
3. Klik Go

---

## CHEAT: Direct SQL voor het project

### Alles resetten
```sql
DROP DATABASE IF EXISTS product_crud;
CREATE DATABASE product_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE product_crud;
```

### Tabellen maken (BEFORE migraties!)
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT,
    created_at DATETIME IMMUTABLE NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    quantity INT DEFAULT 0,
    created_at DATETIME IMMUTABLE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME IMMUTABLE NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Test data
```sql
INSERT INTO categories (name, description, created_at) VALUES 
('Elektronica', 'Elektronische apparaten', NOW()),
('Boeken', 'Verschillende boeken', NOW()),
('Kleding', 'Mode en accessoires', NOW());

INSERT INTO products (category_id, name, description, price, quantity, created_at) VALUES
(1, 'Laptop Dell XPS', 'Krachtige laptop i7, 16GB RAM', 1299.99, 5, NOW()),
(1, 'Draadloze Muis', 'Ergonomische bluetooth muis', 29.99, 50, NOW()),
(2, 'PHP voor Beginners', 'Lesboeek PHP programmeren', 39.99, 20, NOW()),
(3, 'T-shirt XL', 'Katoenen t-shirt zwart', 19.99, 100, NOW());
```

### Check data
```sql
SELECT p.id, p.name, p.price, c.name as categorie
FROM products p
LEFT JOIN categories c ON p.category_id = c.id;
```

---

Veel sterkte! 🎓
