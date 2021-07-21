# Snowtricks

Snowtricks is a community website about sharing and commenting on snowboard tricks


## Key Features
- Add new tricks or modify existing ones in a collaborative manner
- Talk about them with other users
- Customize your profile with a picture and a description

## Requirements

- PHP 7.4.3 or higher
- Composer
- NodeJs
- Yarn   
- Symfony 5.2
- Apache or Nginx

## Installation
Check that the Symfony requirements are met
```
 composer require symfony/requirements-checker
```
Clone the repository 
```
git clone https://github.com/Tavrin/oc-project-6-snowtricks.git
```

Go inside the new directory and install the dependencies
```
composer install --no-dev --optimize-autoloader
```

Create an .env.local file into the root folder and add the environment type (prod in this case) as well as th mailer dsn
```
APP_ENV=prod
MAILER_DSN=smtp://email_address:password@host:port
```

Configure the .env.local file to set a database url with the environment variable name being DATABASE_URL, preferably in MySQL
```
 DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

Create the database if it doesn't exist yet
```
php bin/console doctrine:database:create
```

Apply the migrations
```
php bin/console doctrine:migrations:migrate
```

Create the fixture data
```
php bin/console doctrine:fixtures:load
```

Install the frontend dependencies
```
yarn install
```

Create a production frontend build
```
 yarn encore production
```

Clear the Symfony cache
```
php bin/console cache:clear
```

Your web application is ready for use, you can login with the following credentials :
> login: admin
>
> mdp: Snowtricks06
