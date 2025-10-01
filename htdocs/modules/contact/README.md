# Contact Module

## Overview
The Contact module provides a complete contact form system with email functionality. Users can submit contact inquiries through a web form, and the system sends email notifications to administrators.

## Features
- Contact form with validation
- CSRF protection for security
- Email sending via PHPMailer
- Customizable form fields (name, email, phone, subject, message)
- Success/error feedback to users
- Professional email templates
- Input sanitization and validation

## Installation
The Contact module is automatically discovered by StrataPHP. To enable:

1. Ensure the module is enabled in your configuration
2. Configure email settings in your `config.php`
3. Routes are automatically loaded from `routes.php`

## Configuration

### Email Settings
Configure your email settings in `config.php`:

```php
'mail' => [
    'host' => 'smtp.gmail.com',
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password',
    'port' => 587,
    'encryption' => 'tls',
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'Your Name',
    'to_email' => 'admin@yourdomain.com'
]
```

### Module Configuration
Enable the contact module in your config:

```php
'modules' => [
    'contact' => [
        'enabled' => true
    ]
]
```

## Usage

### Accessing the Contact Form
- Visit `/contact` to access the contact form
- Can be set as default module to appear at root `/`

### Form Fields
- **Name** (required): User's full name
- **Email** (required): User's email address
- **Phone** (optional): User's phone number
- **Subject** (required): Message subject
- **Message** (required): Message content

## Routes
- `GET /contact` - Display the contact form
- `POST /contact` - Process form submission

## File Structure
```
contact/
├── controllers/
│   └── ContactFormController.php  # Main contact form logic
├── models/
│   └── Contact.php               # Contact data model
├── views/
│   └── contact_form.php          # Contact form template
├── index.php                     # Module entry point
└── routes.php                    # Route definitions
```

## Development

### Customizing the Form
1. Edit `views/contact_form.php` to modify the form layout
2. Update `ContactFormController.php` to handle additional fields
3. Modify validation logic as needed

### Email Templates
The email templates are embedded in the controller. To customize:
1. Edit the email content in `ContactFormController::submit()`
2. Consider moving templates to separate files for easier maintenance

## Security Features
- CSRF token protection against cross-site request forgery
- Input sanitization and validation
- Email header injection prevention
- Secure email sending with authentication

## Dependencies
- StrataPHP framework
- PHPMailer library
- Token class for CSRF protection

## Troubleshooting

### Email Not Sending
1. Verify SMTP settings in configuration
2. Check email provider requires app passwords
3. Ensure firewall allows SMTP connections
4. Check error logs for detailed error messages

### Form Validation Issues
1. Ensure all required fields are filled
2. Check CSRF token is properly generated
3. Verify email format validation

## Extension Ideas
- Database logging of contact submissions
- Auto-responder emails
- File upload capability
- Contact categorization
- Admin dashboard for managing contacts