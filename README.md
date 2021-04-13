<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Gateway and Peripheral Management Module

Simple Laravel+VueJS module to manage Gateway and Peripheral Data.
 

#### System Requirements (Dev values)

- **PHP v7.4 + mongodb_ext.dll x64** *(tested on x7.4.2)*
- **MongoDB Community Server Cluster v4** *(tested on v4.4)*
- **Composer 2** *(tested on v2.0.8)*
- **Node v12** *(tested on v12.16.3)*
- **NPM v6** *(tested on v6.14.4)*

#### Solution Platforms

- **Frontend:** VueJS v2.5.17 + Vuetify v2.4.9
- **Backend:** laravel/framework v8.12

#### Installation Guide
1. Extract the content from the ZIP archive in a folder. 
2. Edit the .env file at the project root and edit the following variables to match your MongoDB Cluster access data: `(MONGO_DB_HOST, MONGO_DB_PORT, MONGO_DB_DATABASE, MONGO_DB_USERNAME, MONGO_DB_PASSWORD)`
3. Install composer dependencies from the command line: `composer install`.
4. Install NPM dependencies from the command line: `npm i`.
5. Compile Vue App with Node: `npm run dev` 
6. Start laravel web server: `php artisan serve`
7. Open browser at `http://localhost:8000/`
8. If you want to populate the Database with randomly generated data, you can use the following command: `php artisan db:seed`. This will create 10 Gateways and 4 Peripherals for each.

After these steps, if everything has been installed accordingly, you should be presented with the landing page that will enable you to manage the Gateways and Peripherlas.

#### Testing
There are 7 Feature Test Cases designed to test the Store, Update and Destroy REST API routes for Gateways and Peripherals. <br>
Testing is done with PHPUnit that comes out-of-the-box with Laravel. <br>
To run the test suite, use the following command: `php artisan test`.

`````<br>
By Fernando Pérez 90fernandopc@gmail.com. 2021.
`````