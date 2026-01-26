-- Voeg telefoonnummer kolom toe aan users tabel
ALTER TABLE `users` 
ADD COLUMN `telefoonnummer` varchar(20) NOT NULL 
AFTER `email`;
