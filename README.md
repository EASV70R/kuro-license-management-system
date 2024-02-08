# Kuro License Management System

## Introduction
Kuro is a comprehensive, cloud-based license management system designed to help software developers and companies manage their software licenses efficiently and securely. Built on the LAMP stack, Kuro offers a stable and user-friendly interface for license and organization management.

## Features
- **License Management**: Simplify the creation, retrieval, updating, and removal of software licenses, with additional features for license assignment to users.
- **Organization Management**: Manage organizations with functionalities to add, edit, and remove, ensuring uniqueness in names.
- **User Management**: Comprehensive user administration including role assignments, license management, and user-specific registration.
- **Security**: High-priority security features including role-based access control, secure PDO database interactions, and password hashing with BCRYPT.

## Technology Stack
- **Backend**: PHP with the LAMP stack (Linux, Apache, MySQL, PHP)
- **Frontend**: Bootstrap for a responsive and intuitive user interface
- **Security**: PDO for database interactions, BCRYPT for password hashing, and role-based access control for enhanced security.

## Getting Started
1. **Prerequisites**: Ensure you have a LAMP (or WAMP for Windows) environment set up with PHP, Apache, and MySQL.
2. **Installation**:
   - Clone the repository: `git clone https://github.com/EASV70R/kuro-license-management-system.git`
   - Import the `kurosql.sql` file into your MySQL database to set up the schema.
   - Configure the `database.php` file with the necessary database and environment settings.
3. **Running the Application**:
   - Start your Apache server and MySQL service.
   - Access the application through your web browser at `localhost/<your-installation-directory>`.

## Contributing
Contributions are welcome! Please fork the repository, make your changes, and submit a pull request for review.

## License
This project is licensed under the [MIT License](LICENSE).
