# Paws & Hearts Pet Adoption System

A role-based PHP + MySQL web application for pet adoption management.

This project supports separate experiences for:
- Guests
- Adopters
- Shelters
- Admin

## Main Features

- Role-based login and dashboard routing
- Pet listing and pet details pages
- Shelter pet management (add/list/profile views)
- Adoption tracking with status handling
- Messaging between adopters and shelters
- Basic admin management page

## Project Structure

- admin/: Admin pages
- adopter/: Adopter dashboards, applications, profile, messages
- guest/: Public/guest browsing pages
- shelter/: Shelter dashboards, pet management, profile, messages
- setup_adoptions.sql: SQL setup for adoptions table
- setup_messages.sql: SQL setup for messages table and sample data
- create_test_users.sql: Optional test users/messages SQL

## Database Setup

Please follow the full database setup guide in:

- **database__instruction.txt**
<br>
https://github.com/arafat-khan-pathan/Web-Project/blob/main/database__instruction.txt

This file includes:
- Required MySQL setup
- Table creation order
- SQL execution order
- Test credentials
- Verification queries
- Common issue fixes

## How To Run

1. Put the project folder in your web server document root (for example XAMPP htdocs).
2. Create and configure the MySQL database using database__instruction.txt.
3. Open your browser and start from:
   - login__.php

## Important Entry Pages

- login__.php
- guest/index.php
- adopter/index.php
- shelter/index.php
- admin/admin.php

## Notes

- The project appears to use the database name paws_hearts.
- SQL files are written for phpMyAdmin SQL tab execution.
- Run SQL files in the order documented in database__instruction.txt to avoid foreign key errors.

## Recommended Verification

After setup, verify these areas:
- Login for each role
- Pet listing and details pages
- Adopter messaging screens
- Shelter add-pet flow
- Adoption records visibility

## License

No license file is currently included in this repository.
