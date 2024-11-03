# CS 195 Portal

This repository contains the main project files used for the implementation of a "CS 195 Portal". This project is being developed for the accomplishment of the CS 191/CS 192 subject of the University of the Philippines - Diliman.

## Setting up project

### Cloning the repository

To get started, first clone the repository to your local device:

```bash
git clone git@github.com:ehrelevant/cs195-portal.git
```

> [!IMPORTANT]
> For the succeeding steps, it is assumed that you are using a **Linux environment** (i.e. Virtual Machine, Windows Subsystem for Linux) to develop the application.

### Installing requirements

To run the project locally, the following tools are required:

1. [`docker 27.2.x`](https://www.docker.com/)
2. [`php 8.3.x`](https://www.php.net/)
3. [`composer 2.7.x`](https://getcomposer.org/)
4. [`pnpm 9.12.x`](https://pnpm.io/)

#### Installing `php 8.3.x`

Note that this project currently uses `php 8.3.x`. For debian/ubuntu, the following may be used to install the appropriate version:

```bash
sudo apt install php8.3
```

For older versions of debian/ubuntu, these `apt` repositories may not be available. In this case, run the following:

```bash
sudo apt-get update && sudo apt-get upgrade
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.3
```

You may verify your `php` version by running:

```bash
php --version
```

### Installing Dependencies

To install the frontend dependencies of the application, run:

```bash
# Install the project dependencies
pnpm install
```

To install the backend dependencies, run the following:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### Running the application

After installing all the dependencies, you may run the application with the following command:

```bash
composer run-script dev
```

Afterwards, the server should be available at `localhost`.

## Linting & Formatting

Before pushing or submitting a pull request to the repository, be sure to run the formatters and linters. This is done to ensure that the codebase remains clean and consistent.

```bash
# Fix code formatting with Prettier.
pnpm format

# Checks code with Prettier & ESLint.
pnpm lint
```
