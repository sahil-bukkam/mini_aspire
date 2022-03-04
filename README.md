# Mini aspire App API

clone the REPO


.env already present in the repo 


create 2 databases: one main database and other test database for running the php unit test cases

    main database: aspire

    test database: aspire_test


run mysql commands

    create database aspire;

    create database aspire_test;


change database variables in .env as per requirement

    example

    DB_CONNECTION=mysql

    DB_HOST=127.0.0.1

    DB_PORT=3306

    DB_DATABASE=aspire

    DB_USERNAME=root

    DB_PASSWORD=root@123

    DB_DATABASE_TEST=aspire_test


no need to run composer install as vendor folder already included in the repo


run below command to migrate in main database

    php artisan migrate


run below command to start the server

    php artisan serve


run below command to view all applicable routes and their methods

    php artisan route:list 


    GET|HEAD  | api/loans | loans.index  | App\Http\Controllers\LoanController@index  | api,auth:api

    POST | api/loans | loans.store | App\Http\Controllers\LoanController@store  | api,auth:api

    GET|HEAD  | api/loans/{loan} | loans.show | App\Http\Controllers\LoanController@show | api,auth:api 

    PUT|PATCH | api/loans/{loan}  loans.update | App\Http\Controllers\LoanController@update  | api,auth:api
    
    POST | api/loans/{loan}/installments | | App\Http\Controllers\InstallmentController@store | api,auth:api

    POST | api/login  | | App\Http\Controllers\AuthController@login | api

    POST | api/logout | | App\Http\Controllers\AuthController@logout | api

    POST | api/register | | App\Http\Controllers\AuthController@register | api


To see api documentation, go through postman collection file

    mini aspire laravel apis postman collection.postman_collection.json


To review Test cases, please refer tests folder


to run the test cases execute below command

    ./vendor/bin/phpunit

# Brief Documentation

Mini aspire app is build for its customer as well as for its admin user.


Both the users ie. customer as well as admin can register on this app (pages may be diffent)

users are differentiated based on their type flag ie. 1-> admin , 2-> customer


Both the users can login through same page and In the response we get type parameter based on which user can be redirected.


Logout is common for both the users


Only customer can request for loan ie. user type 2

consider loan term is in weeks eg. 20 weeks, 10 weeks.


Customer can see only loans which he has requested where as admin can see all the loans of all customers


Only admin can approve/reject loan request placed by the customer

admin cannot the status of the loan application where installments are already started


Customers can pay their weekly installments (date validation are not applied as mentioned in the requirement)

It only considers number of weekly installments to be paid.

Customer can only pay installment after the loan gets approved

similar validations are applied while paying installment such as if total amount is already paid.





