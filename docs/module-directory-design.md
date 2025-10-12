# StrataPHP Module Directory System Design

## Database Schema

### modules_directory
- id (int, primary key)
- name (varchar)
- slug (varchar, unique)
- description (text)
- repository_url (varchar)
- author_id (int, foreign key)
- category_id (int, foreign key)
- status (enum: pending, approved, rejected, deprecated)
- version (varchar)
- downloads (int, default 0)
- rating (decimal)
- featured (boolean, default false)
- submitted_at (timestamp)
- approved_at (timestamp)
- approved_by (int, foreign key to users)
- last_updated (timestamp)

### module_authors
- id (int, primary key)
- username (varchar, unique)
- display_name (varchar)
- email (varchar)
- github_username (varchar)
- website (varchar)
- bio (text)
- verified (boolean, default false)
- is_core_team (boolean, default false)
- created_at (timestamp)

### module_categories
- id (int, primary key)
- name (varchar)
- slug (varchar)
- description (text)
- icon (varchar)
- sort_order (int)

### module_reviews
- id (int, primary key)
- module_id (int, foreign key)
- user_id (int, foreign key)
- rating (int, 1-5)
- review_text (text)
- created_at (timestamp)

### module_downloads
- id (int, primary key)
- module_id (int, foreign key)
- user_ip (varchar)
- user_agent (text)
- downloaded_at (timestamp)

## Submission Workflow

1. **Public Submission Form**
   - Repository URL (GitHub, GitLab, etc.)
   - Category selection
   - Author information (if not registered)
   - Description/screenshots

2. **Automated Validation**
   - Repository accessibility check
   - Module structure validation
   - Security scan (no malicious code)
   - Metadata verification

3. **Manual Review Queue**
   - Code quality assessment
   - Functionality testing
   - Documentation review
   - Security audit

4. **Approval/Rejection**
   - Automated notification to author
   - Public listing if approved
   - Feedback provided if rejected

## Author Authentication System

### Trust Levels
- **Core Team** (you/StrataPHP) - Green badge, auto-approve
- **Verified Authors** - Blue badge, fast-track review
- **Community Authors** - Standard review process
- **New Contributors** - Enhanced scrutiny

### Verification Process
- GitHub account verification
- Previous contribution history
- Community reputation
- Manual verification for high-trust status