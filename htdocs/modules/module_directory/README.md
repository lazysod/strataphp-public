# ModuleDirectory Module

A module_directory module for StrataPHP framework.

## Features

- Create, read, update, delete module_directory
- RESTful routes
- Clean MVC structure
- Bootstrap-styled views
- API endpoints

## Installation

This module was generated using the StrataPHP module generator.

## Database

Create the required table:

```sql
CREATE TABLE module_directory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Routes

- `GET /module_directory` - List all items
- `GET /module_directory/create` - Show create form
- `POST /module_directory/create` - Store new item
- `GET /module_directory/{id}` - Show single item
- `GET /module_directory/{id}/edit` - Show edit form
- `POST /module_directory/{id}/edit` - Update item
- `POST /module_directory/{id}/delete` - Delete item
- `GET /api/module_directory` - API endpoint

## Customization

1. Modify the model in `models/ModuleDirectory.php`
2. Update views in `views/` directory
3. Add custom routes in `routes.php`
4. Update database schema as needed

## License

Same as StrataPHP framework.