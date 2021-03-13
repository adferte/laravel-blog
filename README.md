# Technical Test in Laravel

Simple project for a technical test in Laravel. The objective of this assignment is to give us an idea of how you interpret a brief, approach a problem and structure your application.



## Documentation


### Technology

To approach this assignment we would like you to use PHP and Laravel Framework since this is our main stack.
Apart from that, feel free to apply any other acquired knowledge or skill at your discretion.


### Scope

A customer approached us to build a web blogging platform.

The homepage will show all the blog posts to everyone visiting the web. Any user will be able to register in the platform, login to a private area to see the posts he created and, if they want, create new ones. They won't be able to edit or delete them.

The blog posts will only contain a title, description and publication date. The users should be able to sort them by publication_date.

Also, the customer is using another blogging platform and she wants us to auto import the posts created there and add them to our new blogging platform, for that reason, she provided us the following REST api endpoint that returns the new posts. 

The posts from this feed should be saved under a designated, system-created user, 'admin'.

Our customer is a very popular blogger, who generates between 2 and 3 posts an hour. The site which powers the feed linked above is a very popular one (several million visitors a month), and we are expecting a similar level of traffic on our site. One of our goals is to minimise the strain put on our system during traffic peaks, while also minimising the strain we put on the feed server.



## Project insight


### Database Schema

Given the scope and requirements of the project, the database schema is very simple: a table for users and a table for posts, with a **one-to-many relation** user-post. 

![Database Schema](db_schema.png "Database Schema")


### Getting started

* Clone the repository and open the project:
```
git clone https://github.com/adferte/laravel-blog.git
```

* Install all composer and npm vendor packages:
```
composer install
npm install
```

* Copy **.env.example** to **.env** in the project root folder

* Modify database parameters in **.env** file to match those of an existing database with no tables

* Add new key **POSTS_API_URL** to **.env** with the URL of the client's API

* Generate an app key to be included in **.env** file:
```
php artisan key:generate
```

* Execute migration to generate all database structure, including the creation of a system admin user
```
php artisan migrate
```

NOTE: You can set the admin user's password, email and name adding specific keys to the .env file, but it has default values defined at **config/app.php**. 

* Compile front end files
```
npm run prod
```

* Create a development server and go to the URL it returns
```
php artisan serve
```

You should be able to see the app in execution :) 


### Fetching client's API

For this part, I've developed a new artisan command called fetch-client-posts. If executed, it will fetch posts from the clients API and insert them in our database associated to the default user (admin).

It's been developed in a way that, were to be an error in the API (be it not receiving a JSON or not having proper structure) we would discard the entire batch for consistency sake.

Given the requirements, it is set to execute every 10 minutes via cron at Kernel.php to try to capture new posts not too long after the client has created them in the other platform. This timing could be modified if needed to meet the criteria the client provides.

To start the cron execution you can run:
```
php artisan schedule:run
```
The way Laravel is built, **schedule:run** will not run a command set to be executed every 10 minutes if not executed in a XX:X0 minute, so to be able to test this locally anytime, I've put an environment check and execute it every minute on environments different from production.

In a more easy fashion and for the sake of testing, we could just execute the new command - either way works:
```
php artisan fetch-client-posts
```

### Register and login

In order to speed up the development time, I've used the Laravel-provided auth package which creates a complete scaffolding of a secure login/register system.

You can register an new account and log in, having a unique email associated with it, but guest users can also visit the page.


### Reading posts

Main page is **/posts**. It provides a basic, paginated view of all posts existing at the moment. If you are a logged in user, you can click My Posts on the top right to see a filtered view with only the post you have created.

The filter is sent via query string parameter **?mine=1**. This filter in inaccessible to guest users, and even if they manually type it onto the URL, it will be ignored and will show all posts anyways. 


### Creating posts

One more options provided for registered users is a post creation form. It is not accessible to guest users and it will redirect them to the login page if they try to manually access.

This creation form provides two inputs for title and body of a post, and both are required to create one. When sending the form, it will create the post and associate to the currently logged-in user.


### Protecting against traffic peaks

As a mean of protection against traffic peaks, I have set up a caching system that serves current posts without consulting the database.

Data is cached separately depending on the user trying to the get all the posts or his own posts. These cached data update every 10 minutes, or when new posts are created or fetched from the client's API, whatever happens first.
