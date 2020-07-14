1) Clone the repository.
2) Create folder named "packages" in laravel project root
3) Copy cloned repository inside "packages" folder
4) Add following in laravel project root composer.json after "keywords" :
    "repositories": [
        {
            "type": "path",
            "url": "packages/axilweb/phonebook",
            "options": {
                "symlink": true
            }
        }
    ],

5) Add following in laravel project root composer.json inside "autoload" in psr
  "Axilweb\\Phonebook\\": "packages/axilweb/phonebook/src"

  Full example :
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Axilweb\\Phonebook\\": "packages/axilweb/phonebook/src"
        },
        "classmap": [
            "database/seeds",
            "database/factories"
        ]
    },

6) Add following in laravel project root composer.json inside require
  "axilweb/phonebook": "@dev"

7) Run composer update.

8) Run php artisan vendor:publish --provider="Axilweb\Phonebook\PhonebookServiceProvider"

9) Run composer dump-autoload

10) Create a db. Setup db from env. Then run php artisan migrate

11) Run php artisan db:seed --class=PhonebookTableSeeder

12) Hit base_path_of_your_app/phonebookdatatable
    It will load the phonebook
