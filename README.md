# Project Setup

## Prerequis

- PHP >= 8.1
- Composer

## Installation
1. **Clone the repository:**

    ```sh
    git clone <repository-url>
    cd <repository-directory>
    ```

2. **Install PHP dependencies:**

    ```sh
    composer install
    ```

3. **Copy the [.env](http://_vscodecontentref_/0) file:**

```sh
cp .env.example .env
```

4. **Configure your [.env](http://_vscodecontentref_/1) file:**

    Update the database and other configurations in the [.env](http://_vscodecontentref_/2) file as needed.

5. **Run database migrations:**

    ```sh
    php artisan migrate
    ```

6. **Run the development server:**

    ```sh
    php artisan serve
    ```