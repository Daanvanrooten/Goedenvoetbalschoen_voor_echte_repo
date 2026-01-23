-- ================================================
-- MIGRATIE SCRIPT: Activeer alle bestaande gebruikers
-- ================================================
-- Dit script zet is_email_verified = 1 voor alle bestaande gebruikers
-- zodat ze kunnen inloggen zonder email verificatie
--
-- Gebruik dit ALLEEN voor bestaande gebruikers!
-- Nieuwe gebruikers krijgen automatisch het verificatie proces.
-- ================================================

-- Toon huidige status
SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN is_email_verified = 1 THEN 1 ELSE 0 END) as verified_users,
    SUM(CASE WHEN is_email_verified = 0 THEN 1 ELSE 0 END) as unverified_users
FROM users;

-- Activeer alle bestaande gebruikers
UPDATE users 
SET is_email_verified = 1, 
    updated_at = NOW()
WHERE is_email_verified = 0;

-- Verwijder alle openstaande verificatie codes (nu niet meer nodig)
DELETE FROM email_verifications;

-- Toon nieuwe status
SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN is_email_verified = 1 THEN 1 ELSE 0 END) as verified_users,
    SUM(CASE WHEN is_email_verified = 0 THEN 1 ELSE 0 END) as unverified_users
FROM users;

-- Succes bericht
SELECT 'MIGRATIE COMPLEET: Alle gebruikers zijn nu geverifieerd!' as message;
