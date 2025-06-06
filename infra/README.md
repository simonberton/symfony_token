Docker with 3 containers.

one for php 8.2, another for mysql, another for ngingx

Edit your hosts files and add the following line:

``127.0.0.1 local.aaxis.com``

After cloning the repository:
Go to infra folder

``cd infra``

Lets boot up the server:

```docker-compose up --build -d```

When done you will have 3 images:
- aaxis-php
- aaxis-dbserver
- aaxis-web

Create Database and user for access

```bash
docker exec -it aaxis-dbserver mysql
CREATE DATABASE aaxis;
GRANT ALL ON aaxis.* TO 'root'@'%' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
exit
```

Configure Symfony

```bash

docker exec -it aaxis-php bash
composer install
php bin/console d:s:u --force
chmod -R 777 var/cache
php bin/console create-user
```

Now we have created a user for the API.
It's credentials
api as username
test as password

For creating a token you must call first via POST the following url: /local.aaxis.com/api-login
with this parameters as raw BODY with JSON type:

{
"username": "api",
"password": "test"
}
This will return a token.

![img.png](img.png)


Now copy that token and use it calling the API as a Bearer Token as such:

![img_1.png](img_1.png)

## 🛍️ Products API

This API allows you to create, update, delete, and list products.

### 🔗 Base URL

```
http://local.aaxis.com/api/products
```

---

### 📦 Create Product

**Endpoint:**  
`POST /api/products`

**Request Body:**
```json
{
  "name": "Rexona",
  "sku": "123"
}
```

---

### ✏️ Update Product

**Endpoint:**  
`PUT /api/products/{id}`  
(e.g., `/api/products/1`)

**Request Body:**
```json
{
  "id": 1,
  "name": "Rexona",
  "sku": "123"
}
```

---

### ❌ Delete Product

**Endpoint:**  
`DELETE /api/products/{id}`  
(e.g., `/api/products/1`)

---

### 📋 List Products

**Endpoint:**  
`GET /api/products`

**Response Example:**
```json
[
  {
    "id": 2,
    "name": "Rexona",
    "sku": "123"
  },
  {
    "id": 3,
    "name": "Rexona 2",
    "sku": "1233"
  }
]
```

---

### 📘 Notes

- `sku` should be unique per product.
- When updating a product, include the `id` in both the URL and body.


