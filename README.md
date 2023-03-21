# Bulls-media test task

### How to use it:
* Please set google sheets to parse in `config/google_sheets_parsing.php`. The example structure is provided there. Please note that all your sheets must be public.
* No API keys needed.
* Field types available list is provided [here](https://laravel.com/docs/9.x/migrations#available-column-types).
* Fields modifiers `additional_modifiers` described [here](https://laravel.com/docs/9.x/migrations#column-modifiers).

### How to run
* Download the project from GitHub
* Set up your .env file, configure database credentials there. Example file is `.env.example`.
* Run `php artisan serve` in project root.
* Open your localhost url in browser (i.e. `http://127.0.0.1:8000`).
* Run GET request on `/api/parse-sheets` route (i.e. `http://127.0.0.1:8000/api/parse-sheets`).
