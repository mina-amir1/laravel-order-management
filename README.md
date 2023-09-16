<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.foodics.com/wp-content/uploads/2021/12/foodics-logo.svg" width="400"></a></p>

This Laravel application can manage orders for a variety of products, including burgers that consist of different
ingredients. The system keeps track of ingredient stock levels and sends email notifications to the merchant when
ingredient stock falls below 50%. Below are the details of how the system works and instructions on how to set it up.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Controller Action](#controller-action)
- [Email Notifications](#email-notifications)
- [Testing](#testing)
- [Sample Request Payload](#sample-request-payload)

<h2 id="installation"> Installation </h2>

To set up the Laravel Order Management System, follow these steps:

1. Clone the repository to your local machine:

   ```bash 
   git clone https://github.com/mina-amir1/laravel-order-management.git
   ```
2. Navigate to the project directory:
      ```bash
      cd laravel-order-management
      ```
3. Install the project dependencies:
    ```bash
    composer install
      ```
4. Create a .env file by copying the example file:
     ```bash
    cp .env.example .env
      ```
5. Generate an application key:
     ```bash
    php artisan key:generate
      ```
6. Configure your database connection in the .env file:
    ```bash   
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
   ```
7. Migrate the database:
    ```shell
    php artisan migrate
   ```
9. Run the development server:
    ```shell
    php artisan serve
   ```
You should now have the Laravel Order Management System up and running locally.

<h2 id="usage"> Usage </h2>

<h3 id="controller-action"> Controller Action </h3>
The system provides a controller action that handles order placement. The action performs the following tasks:

1. Accepts order details from the request payload.
2. Persists the order in the database.
3. Updates the stock of the ingredients used in the ordered products.
4. Sends an email notification to the merchant if any ingredient stock falls below 50%.

To place an order, make a POST request to the /placeOrder route with the order details in the request payload.

<h3 id="email-notifications"> Email Notifications </h3>
The system sends email notifications to the merchant when an ingredient's stock level falls below 50%. It ensures that only a single email is sent for each ingredient, even if multiple orders consume the same ingredient.

<h3 id="testing"> Testing </h3>
The project includes test cases to verify that orders are correctly stored and ingredient stocks are updated as expected as well as sending the low stock notification. You can run the tests using the following command:

```bash
php artisan test
```
<h2 id="sample-request-payload"> Sample Request Payload </h2>
The incoming payload for placing an order may look like this:

```json
{
  "products": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

<p align="center">Developed by Mina Amir @2023 </p> 
