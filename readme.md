## CloudSchool

[![Codeship Status for hrshadhin/school-management-system](https://app.codeship.com/projects/09010350-b97f-0136-1477-5a7589b245e6/status?branch=master)](https://app.codeship.com/projects/312233)
[![license](https://img.shields.io/badge/license-AGPL-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![php](https://img.shields.io/badge/php-7.2-brightgreen.svg?logo=php)](https://www.php.net)
[![laravel](https://img.shields.io/badge/laravel-6.x-orange.svg?logo=laravel)](https://laravel.com)

```
                       _____  _                    _   _____        _                    _
                      / ____|| |                  | | / ____|      | |                  | |
                     | |     | |  ___   _   _   __| || (___    ___ | |__    ___    ___  | |
                     | |     | | / _ \ | | | | / _` | \___ \  / __|| '_ \  / _ \  / _ \ | |
                     | |____ | || (_) || |_| || (_| | ____) || (__ | | | || (_) || (_) || |
                      \_____||_| \___/  \__,_| \__,_||_____/  \___||_| |_| \___/  \___/ |_|
```

A comprehensive, modular School Management System built with Laravel and PHP 7

## Index

- [Have a Query?](#have-a-query)
- [Demo](#demo)
  - [Enterprise Edition (EE)](#enterprise-edition)
- [Features](#features)
- [Installation](#installation)
  - [Installing dependencies](#installing-dependencies)
  - [Download and setup](#download-and-setup)
  - [Use the app](#use-the-app)
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

1. **Additional Modules**: We've added several enterprise-grade modules including:
   - Online Learning
   - Fee Management
   - Library Management
   - Mobile App Integration
   - Advanced Reporting
   - Communication System
   - Timetable Management
   - Transportation Management
   - Health Records Management

2. **Enhanced User Experience**:
   - Improved UI/UX design
   - More intuitive navigation
   - Responsive design for all devices
   - Dark mode support

3. **Performance Optimizations**:
   - Improved database queries
   - Caching mechanisms
   - Reduced load times
   - Better resource utilization

4. **Security Enhancements**:
   - Advanced permission system
   - Improved data validation
   - Enhanced encryption
   - Regular security updates

5. **Comprehensive Documentation**:
   - Detailed user guides
   - API documentation
   - Developer documentation
   - Video tutorials

6. **Licensing System**:
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

#### Employees Management Module
- Comprehensive Employee Management
- Work Outside Tracking
- Leave Management
- Performance Monitoring

#### SMS and Email Module
- SMS Gateway Setup
- Email & SMS Templating
- Attendance Notifications
- Custom Notification Templates

#### ID Card Management Module
- ID Card Templates
- Employee & Student ID Card Printing
- Bulk/Mass ID Card Printing

#### Online Admission Module
- Online Student Applications
- Application Processing
- Admission Status Tracking

#### Online Documents Module
- Online Admit Cards
- Online Payslips
- Document Management

#### Notice Board Module
- School Announcements
- Important Notifications
- Event Announcements

#### Accounting Module
- Account Management
- Budget Management
- Account Heads
- Income/Expense Tracking

#### Student Billing Module
- Student Invoice Generation
- Fee Management
- Payment Tracking

#### Payroll Module
- Salary Templates
- Employee Salary Payment
- Payroll Processing

#### Hostel Management Module
- Hostel Administration
- Room Allocation
- Collection Management

#### Library Management Module
- Book Inventory
- Book Issuing
- Fine Collection
- Library Reports

#### Academic Calendar Module
- Calendar Management
- Event Scheduling
- Calendar Printing

#### Bulk Communication Module
- Bulk SMS Sending
- Bulk Email Sending
- Targeted Communications

#### Advanced Reporting Module
- Enhanced Reports
- Data Visualization
- Custom Report Generation

#### Website Management Module
- Dynamic Front Website
- Content Management
- Website Administration

#### Photo Gallery Module
- School Photo Management
- Gallery Organization
- Image Uploads

#### Event Management Module
- School Event Planning
- Event Calendar
- Event Notifications

#### Analytics Module
- Google Analytics Integration
- Website Traffic Analysis
- User Behavior Tracking

## Installation

[:arrow_up: Back to top](#index)

#### Installing dependencies

- PHP >= 7.2
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- MySQL >= 5.6 `OR` MariaDB >= 10.1
- [hrshadhin/laravel-userstamps](https://github.com/hrshadhin/laravel-userstamps.git) [**Already Installed**]
- NodeJS, npm, webpack

#### Download and setup

- Clone the repo

  **For Windows run below commands before cloning the Repo.**

  ```
  git config --global core.eol lf
  git config --global core.autocrlf false
  ```

  ```
  $ git clone https://github.com/danny-dis/school-management-system.git school-system
  ```

- change directory
  ```
  $ cd school-system
  ```
- Copy sample `env` file and change configuration according to your need in ".env" file and create Database
  ```
  $ cp .env.example .env
  ```
- Install php libraries
  ```
  $ composer install
  ```
- Setup application

  - Method 1: By one command

    ```
    # setup system with out demo data
    $ php artisan fresh-install

    # setup system with demo data
    $ php artisan fresh-install --with-data
     # OR
    $ php artisan fresh-install -d
    ```

  - Method 2: Step by step

    ```
    $ php artisan storage:link
    $ php artisan key:generate --ansi

    # Create database tables and load essential data
    $ php artisan migrate
    $ php artisan db:seed

    # Load module permissions
    $ php artisan db:seed --class ModulePermissionSeeder

    # Load demo data
    $ php artisan db:seed --class DemoSiteDataSeeder
    $ php artisan db:seed --class DemoAppDataSeeder

    # Clear all caches
    $ php artisan view:clear
    $ php artisan route:clear
    $ php artisan config:clear
    $ php artisan cache:clear
    ```

- Install frontend(css,js) dependency libraries and bundle them
  ```
  $ npm install
  $ npm run backend-prod
  $ npm run frontend-prod
  ```
- Start development server
  ```
  $ php artisan serve
  ```

#### Use the app

[:arrow_up: Back to top](#index)

- Website: [http://localhost:8000](http://localhost:8000)
- App login: [http://localhost:8000/login](http://localhost:8000/login)

  | Username   | Password |
  | ---------- | :------- |
  | superadmin | super99  |
  | admin      | demo123  |

### Module Management

To enable or disable modules:
1. Login as superadmin
2. Go to Module Management from the sidebar
3. Enable or disable modules as needed

Note: Some modules have dependencies on other modules. You cannot enable a module if its dependencies are not enabled, and you cannot disable a module if other enabled modules depend on it.

## Documentation

[:arrow_up: Back to top](#index)

- Comprehensive documentation is available in the `/docs` directory
- User guides for each module are provided
- API documentation for developers
- Installation and configuration guides

## Timeline

- [Check Here](CHANGELOG.md)

## Screenshot

[:arrow_up: Back to top](#index)

- ![Dashboard](../assets/screenshots/ce/dashboard.png?raw=true)
- **[More...](../assets/screenshots/ce/showme.md)**

## Contributing

[:arrow_up: Back to top](#index)

Don't be shy to make some PR's here :smile:

#### To-do

- [ ] Add **unit & integration**. Like real quick!
- [ ] Add shortcut link for create things. i.e: `student`, `attendance`, `marks` etc
- [ ] Add new UI theme version **AdminLTE**
- [ ] Update Jquery with datetime picker library

#### Contributers

A big shout out to all the contributers, more specifically to these guys

- [H.R. Shadhin](https://github.com/hrshadhin)
- [Ashutosh Das](https://github.com/pyprism)
- [order4adwriter](https://github.com/order4adwriter)
- [Zahid Irfan](https://github.com/zahidirfan)
- [Oshane Bailey](https://github.com/b4oshany)

## Issues

[:arrow_up: Back to top](#index)

- If you faced any problems, first check previous issue list. If doesn't exists then create a new one.
- You can report the bugs at the [issue tracker](https://github.com/hrshadhin/school-management-system/issues)
- If you discover a security vulnerability within CloudSchool app, please send an e-mail to [sos@cloudschoolbd.com](mailto:sos@cloudschoolbd.com). All security vulnerabilities will be promptly addressed.

## License

[:arrow_up: Back to top](#index)

Copyright (c) the respective developers and maintainers, as shown by the AUTHORS file.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published
by the Free Software Foundation, either version 3 of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

All Frameworks and libraries are distributed with it's own license.

**As it is a free(free as in freedom) software. To keep the credit for this works, you should
not remove application footer information text**

**Why AGPL? [Read Here](https://www.gnu.org/licenses/why-affero-gpl.html)**

