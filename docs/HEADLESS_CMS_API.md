# StrataPHP Headless CMS API Documentation

## Overview
The StrataPHP Headless CMS API allows you to securely fetch published, headless-enabled pages for a specific site using a per-site API key. All endpoints are read-only (GET) and require an API key for access.

---

## Authentication
- **API Key:** Each site has a unique API key (see the `sites` table, `api_key` column).
- **How to use:** Pass the API key as a query parameter: `?api_key=YOUR_API_KEY`

---

## Endpoints

### 1. Get All Headless Pages for a Site
- **Endpoint:** `/api/pages`
- **Method:** GET
- **Query Parameters:**
  - `api_key` (required): The API key for the site.
  - `limit` (optional): Max number of results (default: 20).

**Example:**
```
GET /api/pages?api_key=YOUR_API_KEY&limit=10
```

**Response:**
```
{
  "success": true,
  "pages": [
    {
      "id": 7,
      "site_id": 2,
      "title": "Second site Page",
      "slug": "second-site-page",
      ...
    },
    ...
  ]
}
```


### 2. Get a Single Page by Slug
- **Endpoint:** `/api/pages?api_key=YOUR_API_KEY&slug=SLUG`
- **Method:** GET
- **Query Parameters:**
  - `api_key` (required): The API key for the site.
  - `slug` (required): The page slug.

**Example:**
```
GET /api/pages?api_key=YOUR_API_KEY&slug=second-site-page
```

**Response:**
```
{
  "success": true,
  "pages": [ { ...page object... } ]
}
```

### 3. Get a Single Page by ID
- **Endpoint:** `/api/pages?api_key=YOUR_API_KEY&page_id=PAGE_ID`
- **Method:** GET
- **Query Parameters:**
  - `api_key` (required): The API key for the site.
  - `page_id` (required): The numeric page ID.

**Example:**
```
GET /api/pages?api_key=YOUR_API_KEY&page_id=7
```

**Response:**
```
{
  "success": true,
  "pages": [ { ...page object... } ]
}
```

---

## Error Responses
- **Missing API key:**
  - `{ "success": false, "error": "Missing API key" }`
- **Invalid API key:**
  - `{ "success": false, "error": "Invalid API key" }`
- **No pages found:**
  - `{ "success": true, "pages": [] }`

---

## Notes
- Only pages with `headless_only = 1` and `status = 'published'` are returned.
- All responses are JSON.
- API keys should be kept secret. Rotate keys if compromised.

---

## Example cURL Request
```
curl 'http://localhost:8888/api/pages?api_key=YOUR_API_KEY'
```

---

For further help, contact your StrataPHP administrator.
