## AaxisTest Local Deployment Guide

This guide provides step-by-step instructions on how to deploy the AaxisTest application locally using Docker and Docker Compose.

### Prerequisites
- Docker installed on your machine.
- Docker Compose installed on your machine.

### Steps to Deploy

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/jcrojasv/AaxisTest.git

2. **Navigate to the Project Directory:**
   ```bash
   cd AaxisTest
3. **Configure Environment Variables:**
   ```bash
   mv .env-example .env
4. **Run Docker Compose:**
  ```bash
  docker-compose up -d
  ```
5. **Install dependencies**
  ```bash
   docker-compose exec app composer install
  ```
6. **Run Migrations:**
   ```bash
   docker-compose exec app php bin/console doctrine:migrations:migrate
   ```
7. **Access the Application:**
  ```bash
  docker-compose exec app symfony server:start
  ```
  * Open your web browser and navigate to http://localhost:8000.
  * The AaxisTest application should be up and running!


### How to run unit tests ###
1. **Create the test database:**
  ```bash
  docker-compose exec app php bin/console doctrine:database:create --env=test
  ```
2. **Run migrations:**
  ```bash
  docker-compose exec app php bin/console doctrine:migrations:migrate --env=test
  ```
3. **Execute tests:**
  ```bash
  docker-compose exec app php bin/phpunit
  ```

### How to consume the API with POSTMAN ###
1. **Import the collecction located in the root path of the repository:**
   - Use the file named "RESTful API AaxisTest.postman_collection.json"
> Make sure to follow the steps in the README file for detailed instructions on creating users and listing them.

### How to Create Users ###
- **Execute the following command to create a user:**
   ```bash
   docker-compose exec app php bin/console app:user-create <username> <password> <roles>
   ``````
- **Available Roles:**
> - ROLE_ADMIN
> - ROLE_USER

### How to List Users ###
- **Execute the following command to list users:**
   ```bash
   docker-compose exec app php bin/console app:user-list
   ``````