# science-project-api (WIP)

Backend build with Laravel.

### About

The idea for this project is a science focused knowledge base for popular topics. The goal is to provide a plattform for science-only based knowledge and therefore an opinion-free plattform for all different kinds of topics.

### Functions

-   Authentication system with JWT tokens
-   User profiles
-   User provided content
-   Sorting functionality for user content
-   Upload functionality (including options for Amazon S3)
-   Comment and Like system
-   Chat system for users

### Used Plugins

-   cviebrock/eloquent-taggable
-   fruitcake/laravel-cors
-   grimzy/laravel-mysql-spatial
-   intervention/image
-   league/flysystem-aws-s3-v3
-   tymon/jwt-auth
-   barryvdh/laravel-debugbar

### How it looks

![alt text](https://i.imgur.com/mRhAIPL.gif 'register')

![alt text](https://i.imgur.com/oVscxmj.gif 'verify email')

![alt text](https://i.imgur.com/34d0cvY.gif 'login')

### Instructions

```bash
# install dependencies
$ composer install

# make .env file -> create a database -> put database data inti .env
cp .env.example .env

# generate an app encryption key
$ php artisan key:generate

# migrate the database
$ php artisan migrate

# start server
$ php artisan serve
```

For detailed explanation on how things work, check out [Laravel docs](https://laravel.com/docs/7.x).
