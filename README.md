# AuraEvents: Creative Event & Portfolio Management Website

A premium, state-of-the-art, and highly responsive **Event Planning and Creative Portfolio Management Platform** developed from scratch in **Core PHP, MySQL, HTML5, CSS3, Bootstrap 5, JavaScript, jQuery, DataTables, and CKEditor**.

---

## 🚀 Key Architectural Highlights

### 🎨 Visual & Frontend Styling
* **High-End Styling**: Built utilizing customized color variables, fluid modern typography (Google Fonts Outfit and Playfair Display), responsive flexbox grids, CSS parallax accents, and glassmorphism.
* **Responsive Architecture**: Fits mobile devices, tablets, and full-resolution wide monitors seamlessly.

### 🛡️ Core Security System
* **Prepared Statements**: Prevents SQL injection across the entire system.
* **XSS Prevention**: Custom input sanitizers filter character inputs before database commits.
* **Password Hashing**: Implements PHP's robust `PASSWORD_BCRYPT` framework for administrators.
* **Session Integrity Checkers**: Secure HTTP-only cookies prevent cookie hijacking and secure administrator dashboards.

### ⚡ Strict WebP Image Validator Matrix
To ensure optimal page speed scores, only **WebP (.webp)** format uploads are allowed. Standard formats (PNG, JPG, JPEG) are rejected by client-side JavaScript Promise validation and server-side MIME type check tools:
1. **Blogs Module**: Exactly `1200 x 630` pixels
2. **Events Module**: Exactly `1200 x 700` pixels
3. **Team Module**: Exactly `500 x 500` pixels
4. **Gallery Module**: Exactly `800 x 600` pixels

### 📊 Advanced DataTables Integration
All data tables inside the administrative panel support:
* Live alphanumeric searching
* Instant sorting and pagination
* Export tools (Excel, CSV, PDF, and Print)
* AJAX-driven CRUD operations (Add, edit, view, delete, toggle) with SweetAlert notifications

### 📝 Bulk CSV Importers
Bulk csv data ingestion models are mapped order-by-order for Blogs, Events, Gallery, and Team profiles, generating secure slugs dynamically from titles.

---

## 📂 File Structure

```text
c:\Users\rc420\event managent\
├── config/
│   └── db.php                  # Database connection, sanitation, WebP image validators, session check
├── includes/
│   ├── header.php              # Shared frontend navigation and page header
│   └── footer.php              # Shared frontend footer and scripts
├── assets/
│   ├── css/
│   │   ├── style.css           # Premium Custom frontend styling
│   │   └── admin.css           # Sleek custom styling for admin panel (supports Dark Mode)
│   └── js/
│       ├── main.js             # Front-end AJAX contact queries submission handlers
│       └── admin.js            # Visual dynamic scripts for admin validations and AJAX CRUD triggers
├── uploads/                    # Categorized WebP uploads directories (Blogs, Events, Gallery, Team, Logo)
├── admin/
│   ├── includes/
│   │   ├── header.php          # Admin header
│   │   ├── sidebar.php         # Admin Sidebar
│   │   └── footer.php          # Admin footer
│   ├── index.php               # Admin Dashboard (Overview of statistics)
│   ├── login.php               # Admin login portal
│   ├── logout.php              # Admin logout script
│   ├── blogs.php               # Blogs CRUD Interface
│   ├── events.php              # Events CRUD Interface
│   ├── gallery.php             # Gallery CRUD Interface
│   ├── team.php                # Team members CRUD Interface
│   ├── enquiries.php           # User enquiries view
│   ├── settings.php            # Dynamic global configurations page
│   └── ajax/                   # AJAX CRUD handlers
│       ├── blogs_handler.php
│       ├── events_handler.php
│       ├── gallery_handler.php
│       ├── team_handler.php
│       ├── enquiries_handler.php
│       └── settings_handler.php
├── index.php                   # Frontend: Dynamic Home Page
├── about.php                   # Frontend: About Us
├── services.php                # Frontend: Services List
├── contact.php                 # Frontend: Interactive Contact Page
├── blog.php                    # Frontend: Dynamic Blogs List
├── blog-detail.php             # Frontend: SEO Blog Detail page
├── event.php                   # Frontend: Dynamic Events List
├── event-detail.php            # Frontend: SEO Event Detail page
├── gallery.php                 # Frontend: Dynamic Gallery Grid
├── .htaccess                   # URL Rewriting for SEO Friendly URLs
├── database.sql                # Full Database Schema installation SQL
└── README.md                   # Complete developer and user guide
```

---

## 🛠️ Step-by-Step Installation Guide

### 1. Database Setup
1. Open your database administration portal (e.g. PHPMyAdmin).
2. Create a new database named **`creative_events_db`**:
   ```sql
   CREATE DATABASE IF NOT EXISTS `creative_events_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the **`database.sql`** file located in the root directory of this project.

### 2. Configure Settings
* Open **`config/db.php`** using an editor of your choice.
* Verify or modify the host connection constants:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'creative_events_db');
  ```

### 3. Run Local Server
Start a local PHP development server or run in an Apache system (XAMPP / WampServer). To start the built-in server via command line, navigate to the project directory and run:
```bash
php -S localhost:8000
```
Open your browser and navigate to: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Administrative Access Credentials

To log in to the premium admin dashboard panel:
1. Navigate to: [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)
2. Submit the following default seed credentials:
   * **Username**: `admin`
   * **Password**: `admin123`

---

## 📊 CSV Import Schemas

To upload bulk data using CSV sheets, ensure you compile your files with the exact column order mapping listed below (and keep the first header row in place):

### A. Blogs Schema
`Title` | `Short Description` | `Full Description (HTML/Text)` | `Meta Title` | `Meta Keywords` | `Meta Description` | `Status (active/inactive)`

### B. Events Schema
`Title` | `Short Description` | `Full Description (HTML/Text)` | `Event Date (YYYY-MM-DD)` | `Event Time (HH:MM:SS)` | `Location / Venue` | `Meta Title` | `Meta Keywords` | `Meta Description` | `Status (active/inactive)`

### C. Team Members Schema
`Name` | `Designation` | `Biography Description` | `Facebook URL` | `Twitter URL` | `Instagram URL` | `LinkedIn URL` | `Status (active/inactive)`

### D. Gallery Schema
`Title` | `Status (active/inactive)`
