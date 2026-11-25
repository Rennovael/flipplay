# FlipPlay Laravel API

REST API demo built with Laravel 12 for the FlipPlay media catalog and personal collections.

## Requirements

- PHP 8.2 or later
- Composer
- SQLite with the PHP PDO SQLite extension

## Run locally

From this directory:

    composer install
    Copy-Item .env.example .env
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan route:list --path=api
    php artisan serve --host=127.0.0.1 --port=8097

The API base URL is http://127.0.0.1:8097/api. Send Accept: application/json on API requests. For write requests, also send Content-Type: application/json.

The database is SQLite at database/database.sqlite. The demo seed creates:

- Demo user: demo@flipplay.test (user ID 1 on a fresh database)
- Four FlipPlay media records, with the first two attached to that user's collection

## API endpoints

| Method | Endpoint | Purpose | Success status |
| --- | --- | --- | --- |
| GET | /api/media | List media; accepts search (title) and category filters | 200 |
| GET | /api/media/{media} | Show a media record and its users | 200 |
| POST | /api/media | Create media | 201 |
| PATCH | /api/media/{media} | Update supplied media fields | 200 |
| DELETE | /api/media/{media} | Delete media | 204 |
| GET | /api/users/{user}/collection | Show a user's saved media | 200 |
| POST | /api/users/{user}/collection/{media} | Attach media to the user's collection | 201 (200 if already attached) |
| DELETE | /api/users/{user}/collection/{media} | Detach media from the user's collection | 200 |

Invalid request data returns HTTP 422 with a JSON message and field-level errors. A missing media or user returns HTTP 404.

Media titles are required and unique. Categories must be Anime, Manga, Drama, or Light Novel. The API uses Form Requests for validation, Eloquent belongsToMany relationships, eager loading, and API Resources. The users and media resource fields are conditionally included with whenLoaded.

## Postman demo

Import postman/FlipPlay_API.postman_collection.json into Postman. First run the commands above so the demo user and seed data exist, then start the server. Run the collection in order with Postman's Collection Runner:

1. List all media and search by title.
2. Create, show, update, and delete a media record.
3. Attach the new media to the demo user's collection, read the collection, and detach it.
4. Check validation failures for a missing and duplicate title, and a 404 after deletion.

The collection contains assertions for the expected HTTP status and key response fields. Its base_url defaults to http://127.0.0.1:8097; user_id defaults to 1.

## Automated feature checks

Run php artisan test to verify CRUD, title search, loaded relationship resources, collection attach/detach, validation 422 responses, and 404 responses against an in-memory SQLite database.
