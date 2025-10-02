# Cms Module

A cms module for StrataPHP framework.

## Features

- Create, read, update, delete cms
- RESTful routes
- Clean MVC structure
- Bootstrap-styled views
- API endpoints

## Installation

This module was generated using the StrataPHP module generator.

## Database

Create the required table:

```sql
CREATE TABLE cms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Routes

- `GET /cms` - List all items
- `GET /cms/create` - Show create form
- `POST /cms/create` - Store new item
- `GET /cms/{id}` - Show single item
- `GET /cms/{id}/edit` - Show edit form
- `POST /cms/{id}/edit` - Update item
- `POST /cms/{id}/delete` - Delete item
- `GET /api/cms` - API endpoint

## Customization

1. Modify the model in `models/Cms.php`
2. Update views in `views/` directory
3. Add custom routes in `routes.php`
4. Update database schema as needed

## License

Same as StrataPHP framework.