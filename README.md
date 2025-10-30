# 📚 Librarian Pro: A Web-Based Library Management System

Librarian Pro is a comprehensive Library Management System (LMS) developed using PHP and Bootstrap. It provides a simple yet powerful interface for librarians to manage books and members, and for members to browse the catalog and track their borrowing history.

---

## 📖 Table of Contents

- [About The Project](#-about-the-project)
- [Key Features](#-key-features)
- [Tech Stack](#-built-with)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation & Setup](#installation--setup)
- [Usage](#-usage)
- [Database Schema](#-database-schema)
- [Project Structure](#-project-structure)
- [License](#-license)
- [Contact](#-contact)

---

## 🏛️ About The Project

The goal of Librarian Pro is to digitize and simplify the core operations of a library. This system replaces manual record-keeping with an efficient, web-based solution. It's built on a classic server-side stack (PHP/MySQL), making it reliable and easy to deploy. The project is designed with two primary user roles in mind: the **Librarian (Admin)** who manages the system, and the **Member (User)** who interacts with the library's collection.

---

## ✨ Key Features

### For Librarians (Admin Panel):
*   **Secure Admin Login:** A dedicated and protected login for library staff.
*   **Dashboard:** An overview of key stats, such as total books, total members, and books currently issued.
*   **Book Management (CRUD):**
    *   Add new books to the catalog with details like ISBN, title, author, and genre.
    *   Update existing book information.
    *   Manage the number of available copies.
*   **Member Management (CRUD):**
    *   Register new library members.
    *   View and update member details.
*   **Issue & Return Management:**
    *   An intuitive interface to issue books to members.
    *   Process book returns and automatically update availability.
*   **Fine Calculation:** The system automatically calculates and displays fines for overdue books.
*   **View Borrowing Records:** Access a complete history of all transactions.

### For Members (User Portal):
*   **Secure Member Login:** Members can log in to access their personal dashboard.
*   **Book Catalog:** Search, browse, and filter the entire library collection.
*   **View Book Availability:** Check if a book is available or currently on loan.
*   **Personal Borrowing History:** View a list of all books they have borrowed, including issue dates and return dates.
*   **Check Due Dates & Fines:** Easily see when books are due and if any fines are owed.

---

## 🛠️ Built With

This project is built using a reliable and standard set of web technologies:

**Frontend:**
*   [**HTML5 & CSS3**](https://developer.mozilla.org/en-US/docs/Web)
*   [**Bootstrap**](https://getbootstrap.com/) - For a responsive and mobile-first user interface.
*   [**JavaScript / jQuery**](https://jquery.com/) - For client-side validation and dynamic elements.

**Backend:**
*   [**PHP**](https://www.php.net/) - For all server-side processing, logic, and database interactions.
*   [**MySQL**](https://www.mysql.com/) - The relational database used to store all library data.

**Development Environment:**
*   [**XAMPP / WAMP**](https://www.apachefriends.org/) - A local server stack for running Apache, MySQL, and PHP.

---

## ⚙️ Getting Started

Follow these steps to get a local copy of the system up and running.

### Prerequisites

You will need a local web server environment that supports PHP and MySQL.
*   **XAMPP** is a recommended free and open-source cross-platform web server solution. [Download XAMPP](https://www.apachefriends.org/index.html).

### Installation & Setup

1.  **Clone the repository:**
    Clone this project into your local server's main web directory.
    *   For **XAMPP**, this is typically the `htdocs` folder (`C:\xampp\htdocs`).
    ```sh
    git clone https://github.com/hathisathissara/library-management/new/main?filename=README.md C:/xampp/htdocs/library-system
    ```

2.  **Start Apache & MySQL:**
    Open the XAMPP Control Panel and start the **Apache** and **MySQL** services.

3.  **Create the Database:**
    a. Open your browser and navigate to `http://localhost/phpmyadmin/`.
    b. Create a new database and name it `lms_db` (or a name of your choice).
    c. Select the newly created database and click on the **Import** tab.
    d. Click "Choose File" and select the `.sql` file provided in this project (e.g., `database/lms_db.sql`).
    e. Click "Go" to import the database structure and sample data.

4.  **Configure the Database Connection:**
    a. Find the database connection file in the project (e.g., `includes/config.php`).
    b. Open the file and edit the credentials to match your local setup.
    ```php
    <?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');      // Default XAMPP username
    define('DB_PASSWORD', '');          // Default XAMPP password is empty
    define('DB_NAME', 'lms_db');        // The name of the database you created
    ?>
    ```

5.  **Run the Application:**
    You're all set! Open your browser and go to `http://localhost/library-system`.

---

## 👨‍💻 Usage

*   Navigate to `http://localhost/library-system` to access the main login page.
*   Log in with the appropriate credentials for each role.
*   **Librarian/Admin Login:**
    *   **Username:** `admin`
    *   **Password:** `admin123`
*   **Member Login:**
    *   **Username:** `member01`
    *   **Password:** `pass123`

---

## 🗄️ Database Schema

The core database tables that power the system include:

| Table        | Description                                                  |
|--------------|--------------------------------------------------------------|
| `books`      | Stores all book information: ISBN, title, author, copies, etc. |
| `members`    | Stores details of all registered library members.            |
| `admins`     | Stores credentials for librarians/administrators.            |
| `loans`      | Tracks every book issuance: which book, which member, dates. |
| `fines`      | Records any fines incurred by members for overdue books.     |
| `categories` | Stores book genres or categories for filtering.              |

---

## 🌳 Project Structure
