# best_people_of_internet
##Тестовое задание "Сайт «Лучшие люди Интернета»"
## Install ##

1. Clone or download git repository

    > git clone https://github.com/DrMom161/best_people_of_internet.git

2. Install packages

    > composer update
    
3. Create database structure from dump **~/database/best_people_of_internet.sql**
  
4. Create file **~/config/autoload/local.php** with database credentials
    ```php
    <?php
    return [
        'db' => [
            'username' => 'SET_YOUR_DATABASE_USERNAME',
            'password' => 'SET_YOUR_DATABASE_PASSWORD',
        ],
    ];
    ```
5. Start web server, for example:
    > php -S localhost:8000 -t public public/index.php
## Usage ##

Open your local site. If you've used server from example open [http://localhost:8000](http://localhost:8000)