# HTML Sanitization Documentation

## Overview

The StrataPHP CMS includes a robust HTML sanitization system designed to prevent XSS attacks while preserving rich content formatting. The system is specifically designed to work with TinyMCE and other WYSIWYG editors.

## Security Features

### ✅ XSS Protection
- Removes all `<script>` tags and JavaScript code
- Blocks dangerous event handlers (`onload`, `onclick`, etc.)
- Prevents CSS-based JavaScript injection
- Filters dangerous URL protocols (`javascript:`, `data:`, etc.)

### ✅ Content Preservation
- Preserves safe HTML formatting (bold, italic, headings, lists)
- Maintains proper paragraph structure
- Keeps safe CSS styling (colors, fonts, spacing)
- Preserves images, links, and tables

## Implementation

### HtmlSanitizer Class

Located at: `htdocs/app/HtmlSanitizer.php`

**Key Methods:**

1. **`sanitize($html)`** - Full HTML sanitization for rich content
2. **`stripAll($input)`** - Removes all HTML (for titles, meta descriptions)
3. **`sanitizeRichContent($html)`** - Alias for `sanitize()` for clarity
4. **`plainTextToHtml($text)`** - Converts plain text to safe HTML paragraphs

### Integration with CMS

The Page model automatically sanitizes all content:

- **Title, Excerpt, Meta Fields**: Strip all HTML using `stripAll()`
- **Content Field**: Preserve safe HTML using `sanitizeRichContent()`
- **Status/Template**: Validate against allowed values
- **Numeric Fields**: Cast to appropriate types

## Allowed HTML Tags

### Text Formatting
- `<p>`, `<br>`, `<hr>`, `<strong>`, `<b>`, `<em>`, `<i>`, `<u>`
- `<strike>`, `<s>`, `<sub>`, `<sup>`, `<small>`, `<mark>`

### Headings
- `<h1>` through `<h6>`

### Lists
- `<ul>`, `<ol>`, `<li>`

### Links and Media
- `<a>` (with safe href, title, target, rel attributes)
- `<img>` (with safe src, alt, title, width, height, style attributes)

### Tables
- `<table>`, `<thead>`, `<tbody>`, `<tfoot>`, `<tr>`, `<th>`, `<td>`

### Semantic Elements
- `<div>`, `<span>`, `<blockquote>`, `<pre>`, `<code>`
- `<section>`, `<article>`, `<aside>`, `<header>`, `<footer>`, `<main>`
- `<figure>`, `<figcaption>`

## Allowed CSS Properties

Safe styling properties include:
- Colors: `color`, `background-color`
- Typography: `font-size`, `font-weight`, `font-style`, `font-family`
- Text: `text-align`, `text-decoration`
- Spacing: `margin`, `padding` (and specific sides)
- Borders: `border` (and specific sides)
- Dimensions: `width`, `height`, `max-width`, `max-height`
- Layout: `display`, `float`, `clear`

## Usage Examples

### Automatic Sanitization (Page Model)
```php
$page = new Page($config);
$page->create([
    'title' => 'My Page Title',  // Will be stripped of HTML
    'content' => '<p>Rich <strong>content</strong> with <script>alert("xss")</script></p>',  // Script removed, formatting preserved
    'meta_description' => 'Safe description'  // HTML stripped
]);
```

### Manual Sanitization
```php
use App\HtmlSanitizer;

// For rich content (TinyMCE output)
$safeHtml = HtmlSanitizer::sanitizeRichContent($userInput);

// For plain text fields
$safeText = HtmlSanitizer::stripAll($userInput);

// Convert plain text to HTML
$htmlContent = HtmlSanitizer::plainTextToHtml($plainText);
```

## TinyMCE Integration

The sanitizer is designed to work seamlessly with TinyMCE:

1. **Compatible**: All TinyMCE formatting options are preserved
2. **Secure**: Dangerous code injected by users is removed
3. **Flexible**: Supports tables, lists, styling, and media

### Recommended TinyMCE Configuration
```javascript
tinymce.init({
    selector: 'textarea#content',
    plugins: 'lists link image table code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
    menubar: false,
    height: 400,
    // Content will be automatically sanitized on save
});
```

## Future Enhancements

### Planned Features
1. **Image Upload Integration**: Secure image upload with validation
2. **Custom Tag Whitelist**: Per-user or per-role tag permissions
3. **Content Security Policy**: Browser-level XSS protection
4. **HTML Validation**: Ensure valid HTML structure

### Security Considerations
- Regular security reviews of allowed tags/attributes
- Monitoring for new XSS vectors
- User training on safe content practices
- Backup and restore capabilities for content

## Testing

The sanitizer has been tested against common XSS attacks:
- ✅ Script injection via `<script>` tags
- ✅ Event handler injection (`onload`, `onclick`)
- ✅ CSS-based JavaScript injection
- ✅ Dangerous URL protocols
- ✅ Malformed HTML handling
- ✅ Unicode and encoding attacks

## Performance

- **Efficient**: Uses PHP's native DOMDocument for parsing
- **Memory Safe**: Processes content without excessive memory usage
- **Fast**: Suitable for real-time content processing
- **Scalable**: Can handle large content without issues