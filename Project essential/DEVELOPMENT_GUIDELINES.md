### Our Approach & Key Learnings

Our development process has been a collaborative and iterative one, with a strong emphasis on adhering to the principles of Domain-Driven Design (DDD). We've established a set of development guidelines to ensure consistency and maintainability, which are documented in this file. A key part of our workflow is to ensure that all new components (Controllers, Policies, Resources, etc.) are placed in their respective domain directories.

A significant portion of our recent efforts has been dedicated to configuring the testing environment, specifically for running feature tests with a `testing.sqlite` database in a Laravel Sail setup on Windows. This has been a challenging process, and we've encountered and worked through several issues, including:

*   **Database Connection Errors:** Persistent `QueryException` errors due to the test environment attempting to connect to the MySQL database instead of the configured SQLite database.
*   **Configuration Loading:** Difficulties in ensuring that the `php artisan test` command correctly loads the testing environment configuration from `phpunit.xml` and `.env.testing`.
*   **Syntax Errors:** Accidental introduction of syntax errors in configuration files during our attempts to resolve the database connection issues.

Through persistent problem-solving, we've explored various solutions, including modifying `phpunit.xml`, `.env.testing`, `config/database.php`, and `config/cache.php`. The key takeaway is the importance of a robust and explicit configuration for the testing environment, especially in a complex setup like Laravel Sail on Windows. We also learned the importance of clearing the configuration cache (`php artisan config:clear`) after making changes to configuration files to ensure that the changes are applied.


### Prerequisites

*   PHP >= 8.2
*   Node.js and npm
*   Composer

### Installation

1.  Clone the repository.
2.  Install PHP dependencies: `composer install`
3.  Install front-end dependencies: `npm install`
4.  Copy the `.env.example` file to `.env`: `cp .env.example .env`
5.  Generate an application key: `php artisan key:generate`
6.  Configure your database in the `.env` file.
7.  Run database migrations and seed the database: `php artisan migrate --seed`

### Development

To start the development servers for both the back-end and front-end, run the following command:

```bash
composer run-script dev
```

This will start the Laravel development server on `http://127.0.0.1:8000` and the Vite development server on `http://localhost:5173`.

Or natively without laravel sail, we go:

```bash
php artisan serve;

```

```bash
npm run dev;
```

### Common Issues & Troubleshooting

**404 Errors for Uploaded Images**

If you are seeing 404 errors for images that you know have been uploaded, there are two common configuration issues in a local development environment.

1.  **Storage Link Not Created:** Public files are stored in `storage/app/public`, but accessed via a URL that points to `public/storage`. For this to work, a symbolic link must be created.
    *   **Symptom:** Image URLs like `http://127.0.0.1:8000/storage/your-image.jpg` give a 404 error.
    *   **Fix:** Run the following command:
        ```bash
        php artisan storage:link
        ```

2.  **Incorrect `APP_URL`:** The URL for generated media files is based on the `APP_URL` variable in your `.env` file. If this doesn't match the address of your development server, the links will be broken.
    *   **Symptom:** The page loads, but image URLs point to the wrong address (e.g., `http://localhost` instead of `http://127.0.0.1:8000`).
    *   **Fix:**
        1.  Open your `.env` file.
        2.  Find the `APP_URL` line and ensure it matches your development server address, including the port. For `php artisan serve`, this should be:
            ```
            APP_URL=http://127.0.0.1:8000
            ```
        3.  After saving the `.env` file, clear the configuration cache:
            ```bash
            php artisan config:clear
            ```
        4.  Restart your development server.

### Testing

To run the test suite, use the following command:

```bash
composer test
```

## Development Conventions

*   **Domain-Driven Design:** The application follows a domain-driven design approach, with services and models organized into domains.
*   **Service Layer:** Business logic is encapsulated in service classes.
*   **Blade Components:** The front-end uses Blade components for reusable UI elements.
*   **Tailwind CSS:** The application uses Tailwind CSS for styling.
*   **Alpine.js:** Alpine.js is used for front-end interactivity.
*   **Livewire:** Used for dynamic/real time components spa-like feel.

## Development Guidelines

*   **Domain-Centric Architecture:** Strictly adhere to the domain-driven design for all new files and modifications. Policies, HTTP controllers, form requests, and other related components must be placed within their respective domain directories (e.g., `app/Domains/Listings/Policies`, `app/Domains/Listings/Http/Controllers`, `app/Domains/Listings/Http/Requests`).
*   **Confidence Threshold:** Before making any changes, ensure you have at least 95% confidence in the approach and implementation. If unsure, ask follow-up questions for clarification.
*   **Domain-Driven Design Adherence:** Always prioritize adhering to the established Domain-Driven Design principles and project structure. New features and modifications should fit naturally within the existing domain boundaries.
*   **Master Plan First:** Refer to the 'Master Plan' as the primary source for tasks and priorities. Address items in the plan before considering new additions, unless explicitly instructed otherwise.
*   **Clarification:** If any part of a task or instruction is unclear, ambiguous, or requires further detail, always ask follow-up questions to gain full understanding before proceeding.
*   **Testing:** For every new feature or bug fix, ensure appropriate unit and feature tests are written to verify correctness and prevent regressions. Tests are a permanent part of the codebase.
*   **Code Conventions:** Strictly follow existing code style, formatting, naming conventions, and architectural patterns observed in the project.
*   **Incremental Changes:** Prefer making small, focused, and verifiable changes. Avoid large, sweeping modifications that are difficult to review and debug.
