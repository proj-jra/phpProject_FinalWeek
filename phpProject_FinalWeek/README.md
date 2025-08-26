
# Student Management System

A student management system built with HTML, CSS, and JavaScript.

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

![Landing Page](.screenshots/Screenshot1.png)
![Student Admissions Page](.screenshots/Screenshot2.png)
![Student Management & Search Page](.screenshots/Screenshot3.png)
![Student Edit Page](.screenshots/Screenshot4.png)


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
 - LE SERRAFIM (Source Music)'s 2023 Seasons Greetings for the ID Photos & Mockup information.

   [I do not claim the photos and used as my own. Im just a fan of the group. ]