# API Module

## Overview
The API module provides REST API endpoints for the StrataPHP framework. Currently includes a jokes API with endpoints for retrieving random jokes and specific joke content.

## Features
- RESTful API endpoints with standard HTTP methods
- JSON response formatting
- Error handling with proper HTTP status codes
- Parameter validation
- Extensible base API controller
- API helper utilities

## Endpoints

### Jokes API (v1)
- `GET /api/v1/jokes/random` - Get a random joke
- `GET /api/v1/jokes/{id}` - Get a specific joke by ID

## Installation
The API module is automatically discovered by StrataPHP. To enable:

1. Ensure the module is enabled in your configuration
2. Routes are automatically loaded from `routes.php`

## Configuration
No specific configuration required. Uses standard StrataPHP module loading.

## Usage

### Getting a Random Joke
```bash
curl https://yourdomain.com/api/v1/jokes/random
```

Response:
```json
{
  "id": 1,
  "joke": "Why did the chicken cross the road? To get to the other side!"
}
```

### Getting a Specific Joke
```bash
curl https://yourdomain.com/api/v1/jokes/1
```

## File Structure
```
api/
├── config/           # API configuration files
├── controllers/      # API controllers
│   ├── ApiController.php     # Base API controller
│   ├── ApiHelper.php         # API utilities
│   └── JokesApiController.php # Jokes API implementation
├── routes.php        # API route definitions
└── views/           # API response templates (if needed)
```

## Development

### Adding New API Endpoints
1. Create a new controller extending `ApiController`
2. Define routes in `routes.php`
3. Use `ApiHelper` for parameter validation
4. Return JSON responses using `$this->json()`

### Error Handling
The API uses standard HTTP status codes:
- 200: Success
- 400: Bad Request
- 404: Not Found
- 500: Internal Server Error

## Dependencies
- StrataPHP framework
- No external dependencies

## Security Notes
- Always validate input parameters
- Use proper error handling to avoid exposing sensitive information
- Consider rate limiting for production deployments