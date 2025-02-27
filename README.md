# Task Management API - Kava Up LLC
This project is a Laravel-based Task Management API designed to handle project and task creation, 
updates, and notifications. Users can create multiple projects, add tasks under each project,
and receive alerts when tasks are nearing their deadlines. 

This README will walk you through
the project’s setup, architecture, and design considerations.

## Table of Contents
1. [Roadmap](#roadmap)
2. [Requirements](#requirements)
2. [Setup](#setup)
3. [Architecture](#architecture)
4. [Design Considerations](#design-considerations)
5. [Features](#features)
6. [Database Schema](#database-schema)

## Roadmap
- [x] Setup Laravel project
- [x] Work in the Docker setup
- [X] Add Readme file
- [ ] Write tests
- [ ] Create database schema
- [ ] Implement project and task creation
- [ ] Implement task notifications
- [ ] Prepare containerized deployment

## Requirements
### Development
To run this project locally, I have created a Docker container that will run the project using docker-compose.
Make sure that you have the following installed:
- [Docker Desktop](https://www.docker.com/products/docker-desktop)

The Docker-compose services available for now are:
- kava-app
  - The Laravel application
- kava-nginx
  - For serving the Laravel application
- kava-mysql
  - For the database

There is a makefile with some useful commands to run the project.

You can run the following command to start the project, the first time you run this command 
it will take a while to build the images, the project will be available at http://localhost:8000
```bash
make start
```

To stop the project, you can run:
```bash
make stop
```

The list of available commands can be found in the Makefile.


If you prefer to run the project without Docker, you will need to have the following installed:
- PHP >= 8.2
- Composer
- MySQL
and follow up the setup instructions in Laravel documentation page.

### Deployment
TODO: Add deployment requirements, work in the Docker file setup and deployment instructions.

## Setup
1. Clone the repository
```bash
git clone git@github.com:hugo-delarosa/kava-up-code-challenge.git
cd task-management-api
```
2. Create a `.env` file
```bash
cp .env.example .env
```
modify the `.env` file to match your local environment settings.

3. Start the project
```bash
make start
```
or
```bash
docker-compose up -d
```

4. Run the migrations
```bash
make migrate
```

or
```bash
docker-compose exec kava-app php artisan migrate
```

## Architecture
The project is built using the Laravel framework, a PHP-based web application framework. 
I have decided to use Laravel Sanctum for API authentication, as it provides a simple way to authenticate users
and issue API tokens.

The project is structured in the following way:
- `app/Http/API/Controllers/`: Contains the controllers for the API endpoints.
- `app/Models/`: Contains the models for the database tables.
- `database/migrations/`: Contains the database migrations.
- `routes/api.php`: Contains the API routes.

## Design Considerations
- **Database Schema**: I have designed the database schema to be simple and easy to understand. 
  The schema consists mainly of two tables: `projects` and `tasks`. Each project can have multiple tasks.
- **API Endpoints**: I have designed the API endpoints to be RESTful and easy to use. 
  The API endpoints are designed to be self-explanatory and easy to understand.
- **Authentication**: I have used Laravel Sanctum for API authentication. 
  This allows users to authenticate using API tokens and access the API endpoints securely.
- **Notifications**: I have implemented task notifications using Laravel's built-in notification system. 
  Users will receive notifications when tasks are nearing their deadlines.

## Features
- **Projects**: Users can create projects and add tasks under each project.
- **Tasks**: Users can create tasks under each project.
  - **Task Assignments**: Users can assign tasks to other users.
  - **Task Due Dates**: Users can set due dates for tasks.
  - **Task Completion**: Users can mark tasks as completed.
  - **Task Deletion**: Users can delete tasks.
  - **Task Status**: Users can view the status of tasks (e.g., pending, completed). For now the available statuses are `to_do`, `in_progress` and `completed`.
- **Task Notifications**: Users will receive notifications when tasks are nearing their deadlines.
- **API Authentication**: Users can authenticate using API tokens.
- **API Endpoints**: Users can interact with the API using RESTful endpoints.

## Database Schema
The database schema consists of the following tables:
Note: refer to [Laravel's available column types](https://laravel.com/docs/12.x/migrations#available-column-types) for more information on the column types.

The desciption of the tables and columns are as follows:
- <column_name>: <column_type> - <column_description>

### Users Table
The `users` table contains the following columns:
- `id`: `id` - The user ID.
- `name`: `string` - The user name.
- `email`: `string` - The user email.
- `email_verified_at`: `timestamp` - The user email verification date.
- `password`: `string` - The user password.
- `remember_token`: `string` - The user remember token.
- `created_at`: `timestamp` - The user creation date.
- `updated_at`: `timestamp` - The user last update date.


### Projects Table
The `projects` table contains the following columns:
- `id`: `id` -  The project ID.
- `name`: `string` - The project name.
- `description`:`text` - The project description. 
- `created_at`: `timestamp` - The project creation date.
- `updated_at`:`timestamp` -  The project last update date. 
- `user_id`:`foreignId` - The user ID of the project owner. 
- `deleted_at`: `timestamp` - The project deletion date. I have considered using soft deletes to keep track of deleted projects.
- `completed_at`: `timestamp` - The project completion date. I have considered adding a completion date to track when a project is completed.
- `due_date`: `dateTime` The project due date. I have considered adding a due date to track when a project is due.

### Tasks Table
The `tasks` table contains the following columns:
- `id`: `id` - The task ID.
- `name`: `string` - The task name.
- `description`:`text` - The task description.
- `created_at`: `timestamp` - The task creation date.
- `updated_at`:`timestamp` - The task last update date.
- `project_id`:`foreignId` - The project ID of the task.
- `user_id`:`foreignId` - The user ID of the task owner.
- `assigned_to`:`foreignId` - The user ID of the task assignee.
- `due_date`: `dateTime` - The task due date.
- `status`: `enum` - The task status. For now the available statuses are `to_do`, `in_progress` and `completed`.
- `deleted_at`: `timestamp` - The task deletion date. I have considered using soft deletes to keep track of deleted tasks.
- `completed_at`: `timestamp` - The task completion date. I have considered adding a completion date to track when a task is completed.

##
