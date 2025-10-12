# .strataphp-modules File Specification

## Purpose
The `.strataphp-modules` file allows repository owners to specify which directories contain StrataPHP modules when multiple modules or non-module content exists in the same repository.

## File Location
Place the `.strataphp-modules` file in the **root directory** of your repository.

## Format
Simple text file with one module path per line:

```
# Comments start with #
# Specify relative paths to module directories

blog/
src/modules/user-management/
modules/contact-form/
```

## Examples

### Single Module Repository
```
# Main module is in the src directory
src/
```

### Multiple Modules Repository
```
# Multiple modules in a modules directory
modules/blog/
modules/user-management/
modules/contact-form/
```

### Mixed Repository Structure
```
# Module is in a specific subdirectory
packages/strataphp-blog/
```

## Benefits

1. **Clear Module Location**: Eliminates guesswork about which folders contain modules
2. **Multiple Modules**: Supports repositories with multiple modules
3. **Mixed Content**: Works with repositories containing documentation, tests, and other non-module content
4. **GitHub Integration**: Works seamlessly with GitHub repository imports
5. **Future-Proof**: Extensible format for additional metadata

## Auto-Detection Fallback

If no `.strataphp-modules` file is found, StrataPHP will:

1. Scan the repository for valid module directories (containing `index.php` with proper metadata)
2. Skip common non-module directories (`.git`, `node_modules`, `vendor`, `tests`, `docs`)
3. Score modules based on completeness (controllers, models, views, etc.)
4. Select the highest-scoring module if multiple are found

## Module Validation

All specified directories must contain:
- `index.php` file returning valid module metadata array
- Required metadata fields: `name`, `slug`, `version`, `description`