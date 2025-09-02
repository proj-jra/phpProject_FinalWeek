
# Student Management System

A student management system built with HTML, CSS, and PHP.

## 🏫 Overview

This web application is designed to do the following things:

- Register new students' information & Photo (Limited to 2mb for now.)
- View, search, and filter registered students
- Edit or delete student records

## 📁 Project Structure

```
phpProject_FinalWeek/
├── form.html                # Student admissions form (main registration page)
├── index.html               # Landing/home page
├── styles.css               # CSS styles for the project
├── insert.php               # Handles form submission and inserts student data
├── display_records.php      # Displays/searches student records
├── update_student.php       # Updates student information
├── delete_student.php       # Deletes a student record
├── update_year_levels.php   # Utility script for fixing year level data
├── uploads/                 # (Expected) Directory for uploaded student 
│   └── photos/              # Subdirectory for photo files

```


## Screenshots

### Landing Page
<img width="1737" height="1010" alt="Screenshot1" src="https://github.com/user-attachments/assets/75d840a3-b396-48b1-9331-6410e4d1e060" />

### Student Admissions Page
<img width="412" height="906" alt="Screenshot2" src="https://github.com/user-attachments/assets/a012477b-655c-4a61-a2a0-e1a0f7a24c63" />

### Student Management & Search Page
<img width="713" height="838" alt="Screenshot3" src="https://github.com/user-attachments/assets/baf9f2f2-0550-417e-8903-a9ca0a7d65fe" />

### Student Edit Page
<img width="397" height="884" alt="Screenshot4" src="https://github.com/user-attachments/assets/07e11e17-1025-43c6-ab55-821a1a344bd4" />


## Setup & Deployment (Locally)

1. Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/.

2. Start XAMPP Services
Make sure to start Apache and MySQL

3. Create the Database

Open http://localhost/phpmyadmin/ in your browser.
Create a new database named school_db.
Create a students table with columns matching the fields used in the forms (e.g., id, full_name, dob, gender, course, year_level, contact_number, email, photo, created_at).

```
-- Create the database
CREATE DATABASE IF NOT EXISTS school_db;
USE school_db;

-- Create the students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    course VARCHAR(50) NOT NULL,
    year_level ENUM('1', '2', '3', '4') NOT NULL,
    contact_number VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    photo VARCHAR(255), -- stores filename or path, can be NULL if no photo uploaded
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
4. Set Folder Permissions
Ensure the photos directory is writable by PHP for photo uploads.

5. Access the App
use localhost. This should open in your default browser.

## Acknowledgements

 - [readme.so](readme.so) for helping me out with making this readme
 - LE SERRAFIM (Source Music)'s (2023 Seasons Greetings) for the ID Photos & other Mockup information.

   [I do not claim the photos and other data used as my own. Im just a fan of the group.]
