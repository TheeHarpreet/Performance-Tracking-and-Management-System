
# Performance Tracking and Management System

The system helps the clients track and manage employees work such as the an employee's work output and performance by digitizing performance management, including research publications. 

## Badges

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
![GitHub stars](https://img.shields.io/github/stars/TheeHarpreet/Performance-Tracking-and-Management-System?style=social)
![GitHub forks](https://img.shields.io/github/forks/TheeHarpreet/Performance-Tracking-and-Management-System?style=social)
![GitHub issues](https://img.shields.io/github/issues/TheeHarpreet/Performance-Tracking-and-Management-System)

## Features

- User Authentication – Secure login, signup, and logout system.
- Performance Dashboard – Displays user performance metrics and reports.
- Work Submission – Allows users to submit work and upload various media types.
- Supervisor Feedback – Users can review feedback and track revisions.
- Search & Filtering – Enables management to find specific submissions using keywords, dates, or categories.
- Role-Based Access Control – Different permissions for research officers, supervisors, and admins.
- Automated Notifications – Alerts users about feedback, deadlines, or system updates.
- Data Encryption & Security – Protects sensitive information, including login credentials and submitted work.
- Activity Logging – Tracks user actions for accountability and auditing.
- Report Generation – Generates reports on performance, submissions, and feedback trends.
- User Profile Management – Allows users to update personal and professional details.
- Multi-Media Support – Supports text, images, PDFs, and other file formats for submission.


## 🚀 Local Deployment
### Prerequisites
- XAMPP/WAMP (Apache, PHP, MySQL)
- Web Browser (Chrome, Firefox, etc.)
- Git (optional)

📌 Setup Steps

1. Clone the Repository

``` bash
git clone  https://github.com/TheeHarpreet/Performance-Tracking-and-Management-System.git
cd Performance-Tracking-and-Management-System
```
(Or download & extract the ZIP file)

2. Move to Server Directory
- XAMPP: Place in ```htdocs/```
- WAMP: Place in ```www/```

3. Start Server
- Open XAMPP/WAMP and start Apache & MySQL

4. Set Up Database
- Open ```phpMyAdmin (http://localhost/phpmyadmin)```
- Create a database (e.g., performance_tracking)
- Import the ``` .sql ```file

5. Update Database Config
Edit ```includes/db.php:```

``` php
Copy
Edit
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "performance_tracking";
```
6. Run the Application
Open a browser and go to:

``` arduino
http://localhost/Performance-Tracking-and-Management-System/
```


## 🔧 Technologies Used
List of tools used in the project:

- 💻 Frontend: HTML, CSS, JavaScript
- 🖥️ Backend: PHP, MySQL
- 📡 Server: Apache (via XAMPP/WAMP)
## 🔄 Usage Guide
### 👩‍🔬 Research Officer
➡️ Logs in → Submits work → Assigns co-author (optional) → Reviews feedback → Views performance

### 👨‍🏫 Supervisor
➡️ Logs in → Reviews submitted work → Provides feedback → Sends work back if revisions needed → Submits work to manager → Views research officer performance

### 👨‍💼 Manager
➡️ Logs in → Views received work → Tracks employee performance

### 🔧 Admin
➡️ Manages user roles → Assigns job roles → Deletes accounts → Oversees system activity


## 🚀 Future Improvements
✔️ Make system public by deploying on a server

✔️ Enhance UI for better user experience (modern design, improved navigation)

✔️ Add email notifications for feedback and submission status

