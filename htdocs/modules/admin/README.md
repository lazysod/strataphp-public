# Admin Links Management Documentation

This guide explains how to use and extend the Admin Links Management features in the Strata Framework.

## Features
- Add, edit, delete, and reorder links from the admin panel
- FontAwesome icon auto-detection for popular domains
- NSFW marking and badge support
- Public users must confirm before visiting NSFW links

## Usage

### Adding a Link
- Go to **Admin > Links > Add Link**
- Fill in the title, URL, and (optionally) FontAwesome icon
- Check the **NSFW?** box if the link is not safe for work
- Click **Add Link**

### Editing a Link
- Go to **Admin > Links > Edit** for the desired link
- Update any field and toggle the NSFW status
- Click **Save Changes**

### Deleting a Link
- Click **Delete** next to any link in the list
- Confirm deletion in the dialog

### Reordering Links
- Use the up/down arrows in the list to change link order

### NSFW Support
- NSFW links show a badge in admin and public views
- Public users must confirm before visiting NSFW links (JS confirmation dialog)

### Icon Auto-Detection
- If the icon field is left blank, the system will auto-detect a FontAwesome icon based on the link's domain

## Extending
- The controller is in `modules/admin/controllers/AdminLinksController.php`
- The model is in `modules/admin/models/Links.php`
- Views are in `modules/admin/links/views/`
- You can add new fields, validation, or custom logic as needed

## Example Code

See the source files for implementation details and customization options.

---

For more information, see the main project README.
