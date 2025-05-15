Clone the repo
and run your shell/terminal
update Composer

create a ".env" file and add your database config data

Build the DB: create a Database named "what_you_want" and add db info to .env
# generate key
php artisan key:generate
# migrate database
Migrate all the Tables: php artisan migrate
# if you want to test run this following command
php artisan test
# run server
php artisan serve
# test api throw postman or others
you should use bearer token in authorization
# what you check?
1.User Registration
2.Login
3.Create Task
4.Task List
5.Assign Task
6.Edit task
