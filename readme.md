## Zophlic School Management System

[![license](https://img.shields.io/badge/license-AGPL-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![php](https://img.shields.io/badge/php-8.1-brightgreen.svg?logo=php)](https://www.php.net)
[![laravel](https://img.shields.io/badge/laravel-10.x-orange.svg?logo=laravel)](https://laravel.com)
[![vue](https://img.shields.io/badge/vue-3.x-green.svg?logo=vue.js)](https://vuejs.org)
[![tailwind](https://img.shields.io/badge/tailwind-3.x-blue.svg?logo=tailwindcss)](https://tailwindcss.com)

```
                     _____              _     _ _
                    |__  /___  _ __ ___| |__ | (_) ___
                      / // _ \| '_ \___| '_ \| | |/ __|
                     / /| (_) | |_) |  | | | | | | (__
                    /____\___/| .__/   |_| |_|_|_|\___|
                             |_|
```

A comprehensive, enterprise-grade School Management System built with Laravel 10 and Vue 3

## Index

- [Features](#features)
- [Installation](#installation)
  - [Installing dependencies](#installing-dependencies)
  - [Download and setup](#download-and-setup)
  - [Use the app](#use-the-app)
- [Module Management](#module-management)
- [Documentation](#documentation)
- [Changelog/Timeline](#timeline)
- [Screenshot](#screenshot)
- [Contributing](#contributing)
  - [To-do](#to-do)
  - [Contributers](#contributers)
- [Issues](#issues)
- [License](#license)

## Differences from Original Project

[:arrow_up: Back to top](#index)

This enhanced version by Zophlic includes several improvements over the original project:

1. **Modern Architecture**: We've modernized the codebase with:
   - RESTful API backend with Laravel 10
   - Single Page Application (SPA) frontend with Vue 3
   - Repository pattern for data access
   - Service layer for business logic
   - Form request validation
   - API resources for consistent responses
   - JWT authentication

2. **Additional Modules**: We've added several enterprise-grade modules including:
   - Online Learning
   - Fee Management
   - Library Management
   - Mobile App Integration
   - Advanced Reporting
   - Communication System
   - Timetable Management
   - Transportation Management
   - Health Records Management

3. **Enhanced User Experience**:
   - Improved UI/UX design
   - More intuitive navigation
   - Responsive design for all devices
   - Dark mode support

4. **Performance Optimizations**:
   - Improved database queries
   - Caching mechanisms
   - Reduced load times
   - Better resource utilization

5. **Security Enhancements**:
   - Advanced permission system
   - Improved data validation
   - Enhanced encryption
   - Regular security updates

6. **Comprehensive Documentation**:
   - Detailed user guides
   - API documentation
   - Developer documentation
   - Video tutorials

7. **Licensing System**:
   - Module-based licensing
   - Subscription management
   - License validation

## Features

[:arrow_up: Back to top](#index)

### Modular Architecture
The system features a modular architecture that allows you to enable or disable specific features based on your needs. Each module provides a set of related features that can be managed independently through the Module Management interface.

### Available Features

#### Core Features
- Academic Year Management
- Academic Calendar Setup
- Institute Setup
- Class & Section Management
- Subject & Teacher Management
- Student Admission
- Student Attendance
- Exam & Grading Rules
- Marks & Result Management
- Student Promotion
- Employees Management
- Employees Attendance
- Employees Leave
- User & Role Management with Permission Grid (ACL)
- User-wise Dashboard
- Report Settings
- Reports

#### Student Portal Module
- Personal Dashboard for Students
- Attendance Records
- Grades and Results
- Subject Information
- Profile Management

#### Parent Portal Module
- Dashboard for Parents
- Children's Academic Progress
- Attendance Monitoring
- Grade Viewing
- Profile Management

#### Online Learning Module
- Course Content Management
- Assignment Submission
- Online Classes
- Learning Resources

#### Fee Management Module
- Fee Type Management
- Invoice Generation
- Payment Tracking
- Fee Reports

#### Library Module
- Book Inventory Management
- Book Issuing and Returns
- Fine Management
- Library Reports

#### Mobile App Integration Module
- Student Mobile App
- Parent Mobile App
- Teacher Mobile App
- Admin Mobile App

#### Advanced Reporting Module
- Custom Report Builder
- Data Visualization
- Export to Multiple Formats
- Scheduled Reports

#### Communication Module
- Internal Messaging
- Email Notifications
- SMS Notifications
- Announcement System

#### Timetable Module
- Class Schedule Management
- Teacher Schedule
- Room Allocation
- Conflict Detection

#### Transportation Module
- Vehicle Management
- Route Management
- Student Transport Assignment
- Transport Fee Integration

#### Health Records Module
- Student Health Profiles
- Medical Visit Records
- Vaccination Records
- Allergy Management

## Installation

[:arrow_up: Back to top](#index)

### Installing dependencies

#### Server Requirements
- PHP >= 8.1
- MySQL >= 8.0 or MariaDB >= 10.3
- Composer
- Node.js >= 16.0
- NPM >= 8.0

#### PHP Extensions
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD
- Zip

### Download and setup

```bash
# Clone the repository
git clone https://github.com/zophlic/school-management-system.git

# Change directory
cd school-management-system

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Build assets
npm run build

# Create a copy of the .env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database_name
# DB_USERNAME=your_database_username
# DB_PASSWORD=your_database_password

# Run database migrations and seed the database
php artisan migrate --seed

# Create symbolic link for storage
php artisan storage:link

# Start the development server
php artisan serve
```

#### Use the app

[:arrow_up: Back to top](#index)

- Website: [http://localhost:8000](http://localhost:8000)
- App login: [http://localhost:8000/login](http://localhost:8000/login)

  | Username   | Password |
  | ---------- | :------- |
  | admin      | admin123 |

### Module Management

[:arrow_up: Back to top](#index)

To enable or disable modules:
1. Login as admin
2. Go to Module Management from the sidebar
3. Enable or disable modules as needed

Note: Some modules have dependencies on other modules. You cannot enable a module if its dependencies are not enabled, and you cannot disable a module if other enabled modules depend on it.

## API Documentation

[:arrow_up: Back to top](#index)

The system provides a comprehensive RESTful API for integration with other systems or for building custom frontends. The API is built using Laravel's API resources and follows RESTful conventions.

### Authentication

The API uses JWT (JSON Web Tokens) for authentication. To authenticate, send a POST request to `/api/login` with your credentials:

```json
{
  "username": "your_username",
  "password": "your_password"
}
```

The response will include a token that should be included in the Authorization header of subsequent requests:

```
Authorization: Bearer your_token_here
```

### Available Endpoints

#### Students
- `GET /api/students` - Get all students
- `GET /api/students/{id}` - Get a specific student
- `POST /api/students` - Create a new student
- `PUT /api/students/{id}` - Update a student
- `DELETE /api/students/{id}` - Delete a student

#### Teachers
- `GET /api/teachers` - Get all teachers
- `GET /api/teachers/{id}` - Get a specific teacher
- `POST /api/teachers` - Create a new teacher
- `PUT /api/teachers/{id}` - Update a teacher
- `DELETE /api/teachers/{id}` - Delete a teacher

#### Classes
- `GET /api/classes` - Get all classes
- `GET /api/classes/{id}` - Get a specific class
- `POST /api/classes` - Create a new class
- `PUT /api/classes/{id}` - Update a class
- `DELETE /api/classes/{id}` - Delete a class

For a complete list of endpoints and detailed documentation, visit the API documentation at `/api/documentation` after installation.

## Documentation

[:arrow_up: Back to top](#index)

Comprehensive documentation is available in the `docs` directory. This includes:

- User Guide
- Administrator Guide
- Developer Guide
- API Documentation

## Timeline

[:arrow_up: Back to top](#index)

See [CHANGELOG.md](CHANGELOG.md) for a detailed timeline of changes.

## Screenshot

[:arrow_up: Back to top](#index)

![Dashboard](screenshots/dashboard.png)

## Contributing

[:arrow_up: Back to top](#index)

Contributions are welcome! Please feel free to submit a Pull Request.

### To-do

[:arrow_up: Back to top](#index)

- [ ] Implement more advanced reporting features
- [ ] Add more payment gateways
- [ ] Improve mobile app integration
- [ ] Add more language options
- [ ] Implement AI-based features

### Contributers

[:arrow_up: Back to top](#index)

- [Zophlic Team](https://github.com/zophlic)

## Issues

[:arrow_up: Back to top](#index)

If you discover any issues, please report them [here](https://github.com/zophlic/school-management-system/issues).

## License

[:arrow_up: Back to top](#index)

This project is licensed under the AGPL License - see the [LICENSE](LICENSE) file for details.
