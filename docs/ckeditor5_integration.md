# CKEditor 5 Integration - CMS Rich Text Editor

## Overview

The StrataPHP CMS now uses **CKEditor 5** instead of TinyMCE. CKEditor 5 is completely free, doesn't require an API key, and offers superior features and performance.

## Why CKEditor 5?

### ✅ Advantages over TinyMCE
- **No API Key Required** - Completely free to use
- **Modern Architecture** - Built with modern web standards
- **Better Performance** - Faster loading and more responsive
- **Superior UX** - More intuitive editing experience
- **Mobile Friendly** - Excellent touch support
- **Active Development** - Regular updates and improvements

### 🚀 Features Included

**Core Editing:**
- Rich text formatting (bold, italic, underline, strikethrough)
- Headings (H1-H4)
- Lists (bulleted and numbered)
- Links with "open in new tab" option
- Code blocks and inline code

**Advanced Features:**
- **Image Upload** - Drag & drop or paste images directly
- **Tables** - Full table editing with cell properties
- **Block Quotes** - Professional quote formatting
- **Text Alignment** - Left, center, right, justify
- **Source Editing** - Direct HTML editing when needed

**Security:**
- Content automatically sanitized through our HtmlSanitizer
- Safe upload handling
- XSS protection maintained

## Implementation Details

### Editor Configuration
```javascript
ClassicEditor.create(document.querySelector('#content'), {
    toolbar: [
        'undo', 'redo', 'heading',
        'bold', 'italic', 'underline', 'strikethrough',
        'link', 'uploadImage', 'insertTable', 'blockQuote',
        'bulletedList', 'numberedList', 'outdent', 'indent',
        'alignment', 'code', 'codeBlock', 'sourceEditing'
    ],
    // ... additional configuration
});
```

### Image Upload Integration
- **Upload Endpoint**: `/ajax/upload_image.php` (unchanged)
- **Custom Upload Adapter**: `StrataPHPUploadAdapter` class
- **Supported Formats**: JPEG, PNG, GIF, BMP, WebP, TIFF
- **Security**: Same validation as before

### Backward Compatibility
- All existing content works perfectly
- Same HTML output structure
- Existing upload system unchanged
- Theme integration maintained

## User Experience

### For Content Editors
- **Intuitive Interface**: Clean, modern editing toolbar
- **Drag & Drop Images**: Simply drag images into the editor
- **Real-time Preview**: See exactly how content will appear
- **Mobile Editing**: Works perfectly on tablets and phones
- **Auto-save**: Content automatically saved to textarea

### For Developers
- **Same API**: No changes to backend processing
- **Same Security**: HtmlSanitizer still processes all content
- **Same Upload System**: File handling unchanged
- **Easy Customization**: Simple toolbar configuration

## Migration Notes

### What Changed
- Editor library: TinyMCE → CKEditor 5
- CDN source: Changed to CKEditor CDN
- No API key required
- Improved upload adapter

### What Stayed the Same
- All backend code unchanged
- Upload endpoints identical
- Security processing maintained
- Content storage format preserved
- Theme integration works

## Troubleshooting

### If Editor Doesn't Load
```javascript
// Fallback is automatically provided
// Plain textarea will be shown with error message
```

### Custom Styling
```css
/* Customize editor appearance */
.ck-editor__editable_inline {
    min-height: 400px;
    font-family: inherit;
}
```

### Upload Issues
- Check `/ajax/upload_image.php` endpoint
- Verify admin authentication
- Check file permissions on upload directory

## Performance Benefits

### Loading Speed
- **Faster Initial Load**: More optimized JavaScript
- **Smaller Bundle**: Only includes needed features
- **Better Caching**: Improved browser caching

### Memory Usage
- **Lower RAM Usage**: More efficient memory management
- **Faster Rendering**: Optimized DOM manipulation
- **Better Mobile Performance**: Lightweight on mobile devices

## Future Enhancements

### Planned Features
1. **Plugin Ecosystem**: Easy plugin additions
2. **Custom Styles**: Theme-specific editor styling
3. **Collaboration**: Real-time collaborative editing
4. **Version History**: Content revision tracking

The migration to CKEditor 5 provides a more robust, future-proof editing experience while maintaining all existing functionality and security features!