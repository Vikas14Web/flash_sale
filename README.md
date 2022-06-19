# Flash Sale

This is a small and initial setup for any flash sale in a shop
There is no complex user management, its purely how the product is up to sale and its order process.

Feature includes:

- Product CRUD
- Flash sale with cached list page
- Buy for flash sale

## Installation

Install the setup using composer 

```bash
composer install
````

## Configuration

Configure the Setup

```
# .env
DATABASE_URL="mysql://user:user_password@127.0.0.1:3306/flashSale?serverVersion=5.7&charset=utf8mb4"
```

```bash
bin/console d:s:u --dump-sql --force
````

## API details

### Product list

API: /shop/products/flash

### Buy a product

API: /shop/products/buy/{id}

