# CS 195 Portal

This repository contains the main project files used for the implementation of a "CS 195 Portal". This project is being developed for the accomplishment of the CS 191/CS 192 subject of the University of the Philippines - Diliman.

## Setting up project

### Cloning the repository

To get started, first clone the repository to your local device:

```bash
# Via ssh
git clone git@github.com:ehrelevant/cs195-portal.git

# Via https
git clone https://github.com/ehrelevant/cs195-portal.git
```

> [!IMPORTANT]
> For the succeeding steps, it is assumed that you are using a **Linux environment** (i.e. Virtual Machine, Windows Subsystem for Linux) to develop the application.

### Installing requirements

To run the project locally, the following tools are required:

1. [`php 8.3.x`](https://www.php.net/)
2. [`composer 2.7.x` or higher](https://getcomposer.org/)
3. [`pnpm 9.12.x` or higher](https://pnpm.io/)

> [!TIP]
> If you are using a Linux environment, you can use the following [convenience script from the Laravel website](https://laravel.com/docs/11.x/installation#installing-php) to quickly install `php` and `composer`.

### Installing Dependencies

To install the frontend dependencies of the application, run:

```bash
# Install frontend project dependencies
pnpm install
```

To install the backend dependencies, run the following:

```bash
# Install backend project dependencies
composer install --ignore-platform-reqs
```

### Running the application

After installing all the dependencies, you may run the application with the following command:

```bash
composer run dev
```

The application will then be available at `http://localhost:8000/`.

## Linting & Formatting

Before pushing or submitting a pull request to the repository, be sure to run the formatters and linters. This is done to ensure that the codebase remains clean and consistent.

```bash
# Fix frontend code formatting with Prettier.
pnpm fmt

# Checks code with Prettier & ESLint.
pnpm lint

# Fix backend code formatting with PHP-CS-Fixer.
composer fmt

# Checks code with PHP-CS-Fixer.
composer lint
```
