# CRM API

1. Clone the repository and run `cd crm-api`.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and run `php artisan key:generate`.
4. Configure the database in `.env`, then run `php artisan migrate`.
5. Run `php artisan serve`.
## Lead Shortlist

### Add unit to shortlist

**POST** `/api/leads/{lead}/shortlist/{unit}`

Adds a unit to the lead's shortlist. Adding the same unit more than once does not create duplicates.

### List shortlist

**GET** `/api/leads/{lead}/shortlist`

Returns the units linked to the lead using `UnitResource`.

### Remove unit from shortlist

**DELETE** `/api/leads/{lead}/shortlist/{unit}`

Removes the unit from the lead's shortlist.