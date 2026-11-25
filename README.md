# QR-Based Student Attendance Management System

A web-based attendance management system that uses QR codes to make student attendance recording faster, easier and more reliable.

## Project Overview

The QR-Based Student Attendance Management System is designed to help lecturers manage student attendance electronically. Lecturers can create course attendance sessions and generate QR codes for students to scan. Students submit their full names and index numbers through an attendance form, while lecturers can view attendance records and control session availability.

## Objectives

- Reduce manual attendance recording.
- Make attendance collection faster and more convenient.
- Prevent duplicate attendance submissions.
- Allow lecturers to manage attendance sessions.
- Provide accessible attendance records.

## Features

- Lecturer registration and login.
- Lecturer dashboard.
- Attendance session creation.
- QR code generation.
- Student attendance submission.
- Duplicate attendance prevention.
- Attendance records viewing.
- Freeze and unfreeze attendance sessions.
- Automated application tests.

## Technologies Used

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- XAMPP
- PHPUnit
- Git and GitHub

## Installation and Setup

### 1. Clone the repository

```bash
git clone https://github.com/nsoh-robert/qr-attendance-system.git
```

### 2. Move the project into XAMPP

Place the project folder inside:

```text
C:\xampp\htdocs\
```

### 3. Start XAMPP

Start Apache and MySQL from the XAMPP Control Panel.

### 4. Create the database

Open phpMyAdmin:

http://localhost/phpmyadmin

Create a database named:

```text
qr_attendance_db
```

Import the database SQL file from:

```text
database/qr_attendance_db.sql
```

### 5. Configure the database

Check the database connection settings in:

```text
config/database.php
```

Make sure the host, database name, username and password match your local MySQL configuration.

### 6. Install dependencies

Open the project terminal and run:

```bash
composer install
```

### 7. Open the application

Visit:

http://localhost/qr-attendance/

## How to Use the System

1. Register a lecturer account.
2. Log in to the application.
3. Create an attendance session for a course.
4. Generate the session QR code.
5. Students scan the QR code.
6. Students enter their full names and index numbers.
7. Lecturers view the attendance records.
8. Lecturers can freeze or unfreeze attendance sessions.

## Automated Testing

The project uses PHPUnit to test important application functions.

Run the tests using:

```bash
vendor/bin/phpunit tests
```

Current recorded result: **16 tests, 18 assertions passed.**

These automated tests cover attendance, authentication and registration helper functions.

## Project Structure

```text
qr-attendance-system/
├── assets/
├── config/
├── tests/
├── attend.php
├── attendance-count.php
├── create-session.php
├── dashboard.php
├── freeze.php
├── index.php
├── login.php
├── logout.php
├── qr-code.php
├── records.php
├── register.php
├── test-session.php
├── unfreeze.php
├── composer.json
├── composer.lock
├── .gitignore
└── README.md
```

## Project Team

1. Nsoh Robert
2. Adusei Benedict
3. Appiah Abigail
4. Nkansah Joseph Boampong
5. Opoku Emmanuel

## Current Project Status

The system has been developed and tested locally using XAMPP. Online deployment is not yet configured.

## Future Improvements

- Online hosting and remote access.
- Attendance report exports.
- QR code expiry controls.
- Additional security improvements.

## GitHub Repository

https://github.com/nsoh-robert/qr-attendance-system

---

**Project:** QR-Based Student Attendance Management System  
**Date:** November 2026