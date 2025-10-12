# GoogleAnalytics Module

A google_analytics module for StrataPHP framework.

## Features

- Create, read, update, delete google_analytics
- RESTful routes
- Clean MVC structure
- Bootstrap-styled views
- API endpoints

## Installation

This module was generated using the StrataPHP module generator.

## Database

Create the required table:

```sql
CREATE TABLE google_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Routes

- `GET /google_analytics` - List all items
- `GET /google_analytics/create` - Show create form
- `POST /google_analytics/create` - Store new item
- `GET /google_analytics/{id}` - Show single item
- `GET /google_analytics/{id}/edit` - Show edit form
- `POST /google_analytics/{id}/edit` - Update item
- `POST /google_analytics/{id}/delete` - Delete item
- `GET /api/google_analytics` - API endpoint

## Customization

1. Modify the model in `models/GoogleAnalytics.php`
2. Update views in `views/` directory
3. Add custom routes in `routes.php`
4. Update database schema as needed

## License

Same as StrataPHP framework.