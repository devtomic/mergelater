-- PostgreSQL test database initialization script
-- This runs automatically on first container start

-- Create test database for Laravel tests
CREATE DATABASE mergelater_testing WITH OWNER = mergelater;

-- Grant all privileges on test database
GRANT ALL PRIVILEGES ON DATABASE mergelater_testing TO mergelater;
