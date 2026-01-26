-- Voeg telefoonnummer kolom toe met default waarde voor bestaande users
ALTER TABLE `users` 
ADD COLUMN `telefoonnummer` varchar(20) NOT NULL DEFAULT '' 
AFTER `email`;
