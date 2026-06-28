# loove-project

# You need Docker Desktop to use this project #

## Launching docker containers
While being at the root of the project, type the following in the terminal :

    docker compose up --build

## Add a file named '.env' to the root of the project (next to the docker files)
.env file example ( you can copy and paste the following ): 

    DB_ROOT_PASSWORD=root
    DB_NAME=loove_app
    DB_USER=loove_user
    DB_PASSWORD=loove_password

    ROOT_PATH="/var/www/html/"
    APP_URL="http://localhost/views"

    STRIPE_SECRET_KEY="sk_test_51TmC5VGbfvYQKJFYnGP90ZBpZhNBQJv4MtIllQAZhbXEFXuVBr88B88Y1HigIvaMjFoQ6tWie7RjUDQxqRvgWpmn00bBGiwW6b"

### This 'secret key' is a testing key

## There is no interface given to check the database. You have to use a software like MySql Workbench
How to config database in a DB software ( using previous .env values)

Connection name: love_app
Hostname: 127.0.0.1
Port: 3306
Username: root ( or loove_user )
Password: root ( or loove_password ) 

