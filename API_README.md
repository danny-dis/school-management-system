# School Management System API

This document provides an overview of the School Management System API, its architecture, and how to use it.

## API Overview

The School Management System API is a RESTful API built with Laravel that provides endpoints for managing various aspects of a school, including students, teachers, classes, attendance, exams, and more.

## API Versioning

The API uses versioning to ensure backward compatibility as new features are added. The current version is v1.

### Base URL

```
https://your-domain.com/api/v1
```

## Authentication

The API uses token-based authentication with Laravel Sanctum. To access protected endpoints, you need to include the token in the Authorization header of your requests.

```
Authorization: Bearer {your_token}
```

### Getting a Token

To get a token, you need to authenticate using the login endpoint:

```
POST /api/v1/login
Content-Type: application/json

{
    "username": "your_username",
    "password": "your_password"
}
```

## Response Format

All API responses follow a standard format:

```json
{
    "success": true|false,
    "message": "Success or error message",
    "data": { ... } // Response data or null
}
```

## Error Handling

When an error occurs, the API returns an appropriate HTTP status code along with an error message:

```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... } // Validation errors (optional)
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse. The current limits are:

- Public endpoints: 30 requests per minute
- Protected endpoints: 60 requests per minute

Rate limit information is included in the response headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

## Caching

The API implements caching for certain endpoints to improve performance. Cache information is included in the response headers:

```
X-Cache: HIT|MISS
X-Cache-TTL: 300
```

## Security

The API implements several security measures:

- CORS protection
- Security headers
- Request logging
- Rate limiting
- Token-based authentication

## API Documentation

For detailed API documentation, please refer to the [API Documentation](https://your-domain.com/api-docs-v1.html).

## Available Endpoints

### Authentication

- `POST /api/v1/login` - Authenticate a user and get an access token
- `POST /api/v1/logout` - Revoke the current access token
- `GET /api/v1/app-info` - Get information about the application

### Users

- `GET /api/v1/user` - Get information about the authenticated user
- `POST /api/v1/change-password` - Change the authenticated user's password

### Students (Admin)

- `GET /api/v1/admin/students` - Get a list of all students
- `POST /api/v1/admin/students` - Create a new student
- `GET /api/v1/admin/students/{id}` - Get details of a specific student
- `PUT /api/v1/admin/students/{id}` - Update a student
- `DELETE /api/v1/admin/students/{id}` - Delete a student
- `GET /api/v1/admin/students/class/{classId}` - Get students by class
- `GET /api/v1/admin/students/section/{sectionId}` - Get students by section

### Student (Frontend)

- `GET /api/v1/student/profile` - Get the authenticated student's profile
- `POST /api/v1/student/profile` - Update the authenticated student's profile
- `GET /api/v1/student/attendance` - Get the authenticated student's attendance
- `GET /api/v1/student/subjects` - Get the authenticated student's subjects
- `GET /api/v1/student/results` - Get the authenticated student's results
- `GET /api/v1/student/fees` - Get the authenticated student's fees
- `GET /api/v1/student/books` - Get the authenticated student's books
- `GET /api/v1/student/timetable` - Get the authenticated student's timetable

### Teachers (Admin)

- `GET /api/v1/admin/teachers` - Get a list of all teachers
- `POST /api/v1/admin/teachers` - Create a new teacher
- `GET /api/v1/admin/teachers/{id}` - Get details of a specific teacher
- `PUT /api/v1/admin/teachers/{id}` - Update a teacher
- `DELETE /api/v1/admin/teachers/{id}` - Delete a teacher

### Teacher (Frontend)

- `GET /api/v1/teacher/profile` - Get the authenticated teacher's profile
- `POST /api/v1/teacher/profile` - Update the authenticated teacher's profile
- `GET /api/v1/teacher/classes` - Get the authenticated teacher's classes
- `GET /api/v1/teacher/students` - Get the authenticated teacher's students
- `GET /api/v1/teacher/attendance` - Get the authenticated teacher's attendance
- `POST /api/v1/teacher/attendance` - Save attendance
- `GET /api/v1/teacher/exams` - Get the authenticated teacher's exams
- `GET /api/v1/teacher/marks-form` - Get the marks form
- `POST /api/v1/teacher/marks` - Save marks

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
