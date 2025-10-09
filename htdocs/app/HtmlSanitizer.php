<?php
namespace App;

/**
 * HTML Sanitizer for CMS Content
 * 
 * Designed to work with TinyMCE output while maintaining security.
 * Whitelist approach - only allows specifically permitted tags and attributes.
 */
class HtmlSanitizer
{
    /**
     * Allowed HTML tags and their permitted attributes
     */
    private static $allowedTags = [
        // Text formatting
        'p' => [],
        'br' => [],
        'hr' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'strike' => [],
        's' => [],
        'sub' => [],
        'sup' => [],
        'small' => [],
        'mark' => [],
        
        // Headings
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        
        // Lists
        'ul' => [],
        'ol' => ['start', 'type'],
        'li' => [],
        
        // Links and media
        'a' => ['href', 'title', 'target', 'rel'],
    'img' => ['src', 'alt', 'title', 'width', 'height', 'style', 'class'],
        
        // Tables
        'table' => ['border', 'cellpadding', 'cellspacing', 'style'],
        'thead' => [],
        'tbody' => [],
        'tfoot' => [],
        'tr' => [],
        'th' => ['colspan', 'rowspan', 'scope'],
        'td' => ['colspan', 'rowspan'],
        
        // Blocks
        'div' => ['class', 'style'],
        'span' => ['class', 'style'],
        'blockquote' => ['cite'],
        'pre' => [],
        'code' => [],
        
        // Semantic
        'section' => [],
        'article' => [],
        'aside' => [],
        'header' => [],
        'footer' => [],
        'main' => [],
        'figure' => [],
        'figcaption' => [],
    ];

    /**
     * Allowed CSS properties (for style attributes)
     */
    private static $allowedCssProperties = [
        'color',
        'background-color',
        'font-size',
        'font-weight',
        'font-style',
        'font-family',
        'text-align',
        'text-decoration',
        'margin',
        'margin-top',
        'margin-bottom',
        'margin-left',
        'margin-right',
        'padding',
        'padding-top',
        'padding-bottom',
        'padding-left',
        'padding-right',
        'border',
        'border-top',
        'border-bottom',
        'border-left',
        'border-right',
        'width',
        'height',
        'max-width',
        'max-height',
        'display',
        'float',
        'clear',
    ];

    /**
     * Dangerous protocols to block in URLs
     */
    private static $dangerousProtocols = [
        'javascript:',
        'vbscript:',
        'data:',
        'file:',
    ];

    /**
     * Sanitize HTML content
     */
    public static function sanitize($html)
    {
        if (empty($html)) {
            return '';
        }

        // Create DOMDocument for parsing
        $dom = new \DOMDocument('1.0', 'UTF-8');
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with UTF-8 encoding
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Clear libxml errors
        libxml_clear_errors();
        
        // Sanitize the document
        self::sanitizeNode($dom);
        
        // Get the cleaned HTML
        $sanitized = $dom->saveHTML();
        
        // Remove the XML encoding declaration we added
        $sanitized = str_replace('<?xml encoding="UTF-8">', '', $sanitized);
        
        return trim($sanitized);
    }

    /**
     * Recursively sanitize DOM nodes
     */
    private static function sanitizeNode(\DOMNode $node)
    {
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($node->nodeName);
            
            // Remove disallowed tags
            if (!isset(self::$allowedTags[$tagName])) {
                // Replace with text content or remove entirely
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
                return;
            }
            
            // Clean attributes
            $allowedAttrs = self::$allowedTags[$tagName];
            $attributesToRemove = [];
            
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attribute) {
                    $attrName = strtolower($attribute->name);
                    $attrValue = $attribute->value;
                    
                    // Check if attribute is allowed
                    if (!in_array($attrName, $allowedAttrs)) {
                        $attributesToRemove[] = $attrName;
                        continue;
                    }
                    
                    // Sanitize specific attributes
                    switch ($attrName) {
                        case 'href':
                        case 'src':
                            if (self::isDangerousUrl($attrValue)) {
                                $attributesToRemove[] = $attrName;
                            }
                            break;
                            
                        case 'style':
                            $cleanStyle = self::sanitizeCss($attrValue);
                            if (empty($cleanStyle)) {
                                $attributesToRemove[] = $attrName;
                            } else {
                                $attribute->value = $cleanStyle;
                            }
                            break;
                            
                        case 'target':
                            // Only allow safe target values
                            if (!in_array($attrValue, ['_blank', '_self', '_parent', '_top'])) {
                                $attributesToRemove[] = $attrName;
                            }
                            break;
                    }
                }
                
                // Remove unsafe attributes
                foreach ($attributesToRemove as $attrName) {
                    if ($node instanceof \DOMElement) {
                        $node->removeAttribute($attrName);
                    }
                }
            }
        }
        
        // Process child nodes
        if ($node->hasChildNodes()) {
            $children = [];
            foreach ($node->childNodes as $child) {
                $children[] = $child;
            }
            
            foreach ($children as $child) {
                self::sanitizeNode($child);
            }
        }
    }

    /**
     * Check if URL uses dangerous protocol
     */
    private static function isDangerousUrl($url)
    {
        $url = strtolower(trim($url));
        
        foreach (self::$dangerousProtocols as $protocol) {
            if (strpos($url, $protocol) === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Sanitize CSS in style attributes
     */
    private static function sanitizeCss($css)
    {
        if (empty($css)) {
            return '';
        }
        
        $cleanRules = [];
        $rules = explode(';', $css);
        
        foreach ($rules as $rule) {
            $rule = trim($rule);
            if (empty($rule)) {
                continue;
            }
            
            $parts = explode(':', $rule, 2);
            if (count($parts) !== 2) {
                continue;
            }
            
            $property = trim(strtolower($parts[0]));
            $value = trim($parts[1]);
            
            // Check if property is allowed
            if (!in_array($property, self::$allowedCssProperties)) {
                continue;
            }
            
            // Basic value sanitization
            if (self::isSafeCssValue($value)) {
                $cleanRules[] = $property . ': ' . $value;
            }
        }
        
        return implode('; ', $cleanRules);
    }

    /**
     * Check if CSS value is safe
     */
    private static function isSafeCssValue($value)
    {
        $value = strtolower($value);
        
        // Block dangerous CSS
        $dangerous = ['expression', 'javascript:', 'vbscript:', '@import', 'behavior'];
        
        foreach ($dangerous as $danger) {
            if (strpos($value, $danger) !== false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Quick sanitization for form input (removes all HTML)
     */
    public static function stripAll($input)
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize for TinyMCE output (allows rich formatting)
     */
    public static function sanitizeRichContent($html)
    {
        return self::sanitize($html);
    }

    /**
     * Convert plain text to safe HTML (for non-WYSIWYG input)
     */
    public static function plainTextToHtml($text)
    {
        if (empty($text)) {
            return '';
        }
        
        // Escape HTML first
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // Convert line breaks to paragraphs
        $paragraphs = preg_split('/\n\s*\n/', $escaped);
        $processedParagraphs = [];
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Convert single line breaks to <br> within paragraphs
                $paragraph = nl2br($paragraph);
                $processedParagraphs[] = '<p>' . $paragraph . '</p>';
            }
        }
        
        return implode("\n", $processedParagraphs);
    }
}