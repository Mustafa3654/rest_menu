# Restaurant Menu Management System

A comprehensive, premium web-based restaurant menu management system built with PHP and MySQL. This application features an elegant **Olive Green & Cream White theme**, persistent **Dark Mode** toggle, active category border glow animations, a user-friendly public interface for customers, and a powerful, centralized admin dashboard.

---

## 🌟 Features

### 🍽️ Public Features
- **Modern Landing Page**: Beautiful, responsive hero section with dynamic background media, customizable headers, highlights, a photo gallery slider, and restaurant opening hours.
- **Interactive About Page**: Dedicated about page featuring the restaurant's legacy, configurable principles/values card grid, and a chef showcase segment.
- **Interactive Menu**: Multi-category menu view with active tab transitions, items grid with details, and an integrated shopping cart.
- **Flexible Cart & Checkout System**: 
  - Persistent shopping cart stored via `localStorage`.
  - Interactive item controls directly on the menu cards.
  - Checkout ordering routed through **WhatsApp** or **SMS** based on admin configuration.
  - Automated combo warning notifications (notifying customers that combo items require an hour preparation time).
- **Persistent Dark Mode**: Simple sun/moon icon toggle in the navigation bar. Remembers the user's choice across visits using `localStorage`.
- **Olive Green Design System**: A premium, curated palette consisting of Olive Green (`#42522B`), Cream White (`#F7F5EA`), Dark Charcoal (`#2B2B2A`), and Light Khaki (`#CBB58B`) accents.
- **Glowing Hover Effects**: Interactive glowing border animations for active categories, buttons, and links.
- **Item Details**: Dedicated view showing detailed ingredients, item description, and pricing structure.
- **Interactive Contact Page**: Reach out via a contact form, easily access location maps, phone number, email, and social media handles. Message submissions can send automated alerts.
- **Fluid Responsiveness**: Works perfectly across desktops, tablets, and smartphones.

### 🛡️ Admin Features
- **Secure Authentication**: Protected administrator login gateway with session-based security, CSRF protection, and automatic on-the-fly password hashing upgrade for legacy accounts.
- **Central Dashboard**: Comprehensive overview for editing your categories, items, and platform parameters.
- **Category Management**:
  - Add, edit, delete, and re-order menu categories.
  - Upload custom category images and specific visual icons.
- **Menu Item Management**:
  - Full CRUD operations for menu items.
  - Custom image upload, pricing configurations, and description details.
  - Set custom ordering so specific dishes highlight at the top.
- **CSV Import & Export**:
  - Export all menu items to a CSV file (with Excel UTF-8 BOM support).
  - Bulk import or update menu items via a CSV file, including category validation.
- **System Settings Configuration**:
  - Customize restaurant branding, name, description, and logo.
  - Manage contact detail values (address, email, phone, and maps embedding).
  - Configure background media and banners for individual views (Home, Menu, Contact, About).
  - Configure landing page sliders/gallery, about text, chef bios, and values grid (icons, titles, descriptions).
  - Set custom opening hour displays.
  - Configure Telegram bot credentials to route customer inquiries directly to your Telegram chat.

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.2+ (Supports MySQLi prepared queries)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3 (Custom properties/variables), Vanilla JavaScript
- **Icons**: Font Awesome v5/v6 CDN
- **Server Environment**: Apache (fully compatible with local XAMPP/WampServer installs via `.htaccess` rewrites)

---

## 📋 System Requirements

- PHP 7.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server with rewrite module (`mod_rewrite`) enabled
- Required PHP Extensions: `mysqli`, `session`, `file uploads` enabled

---

## 🚀 Installation & Setup

### Step 1: Copy Files to Web Root

Copy the entire project folder to your local server's directory:
- **XAMPP**: `C:\xampp\htdocs\rest_menu\`
- **WampServer**: `C:\wamp64\www\rest_menu\`
- **Linux/Mac**: `/var/www/html/rest_menu/`

### Step 2: Database Setup

1. Start your Apache and MySQL database server.
2. Open phpMyAdmin: [http://localhost/phpmyadmin]
3. Create a new database named `menu` (UTF-8 Unicode collation recommended).
4. Go to **Import**, select `Databases/menu.sql` (sample database) or `Databases/empty_menu.sql` (fresh database), and click **Go** to complete database import.

### Step 3: Database Connection Configuration

Edit **[includes/connection.php]** to match your local server credentials. The project automatically determines the base URL dynamically:

```php
$dbHost = 'localhost';
$dbUsername = 'root';      // Your MySQL username
$dbPassword = '';          // Your MySQL password
$dbName = 'menu';          // Database name
```

### Step 4: Configure Write Permissions

Ensure your server has write privileges on the folders designated for uploaded assets:
- `admin/bgs/` - Logo and background images
- `admin/pics/` - Gallery and vibe images
- `items/` - Menu item thumbnails

On Linux or Mac environments:
```bash
chmod 755 admin/bgs/
chmod 755 admin/pics/
chmod 755 items/
```

### Step 5: Default Admin Credentials

Access the dashboard and log in using the pre-configured credentials:
- **Username**: `admin`
- **Password**: `admin2`

> [!WARNING]
> Ensure you change the password immediately in the database or admin interface after initial setup for production security.

---

## 📂 Project Structure

```text
rest_menu/
├── .htaccess               # Apache configuration for security and clean URL redirections
├── README.md               # System documentation
├── index.php               # Public homepage / landing page
├── about.php               # Public legacy & story page
├── menu.php                # Public interactive menu page with cart system
├── ingredients.php         # Dedicated menu item details page
├── contact.php             # Contact and location page with form and Telegram notifications
├── login.php               # Secure admin login gateway
├── logout.php              # Session destroyer script
│
├── admin/                  # Admin panel modules
│   ├── dashboard.php       # Central admin panel landing page
│   ├── addCategory.php     # Create new categories
│   ├── editCategory.php    # Update existing categories
│   ├── deleteCategory.php  # Delete categories
│   ├── viewCategories.php  # Category listing and management
│   ├── addItem.php         # Create new menu items
│   ├── editItem.php        # Update existing menu items
│   ├── deleteItem.php      # Delete menu items
│   ├── viewItems.php       # Menu items listing, CSV triggers, and management
│   ├── editSettings.php    # General website parameters, banners, values, & branding setup
│   ├── editTelegram.php    # Telegram bot configuration management
│   ├── exportItems.php     # Export menu items to CSV format
│   ├── importItems.php     # Bulk import/update menu items from CSV format
│   ├── manageGallery.php   # Gallery and slider settings
│   ├── bgs/                # Admin-specific background settings templates
│   └── pics/               # Admin-specific picture library
│
├── includes/               # Common shared templates & logic
│   ├── auth.php            # Admin session authentication and CSRF security handlers
│   ├── connection.php      # MySQL database connection configuration and dynamic base URL setup
│   ├── header.php          # Site layout top navigation header component
│   └── footer.php          # Site layout footer component
│
├── Databases/              # Database SQL scripts
│   ├── menu.sql            # Core database schema with sample data
│   └── empty_menu.sql      # Fresh database schema structure
│
├── JS/                     # Interactive scripts
│   ├── theme.js            # Persistent Light/Dark mode toggler logic
│   ├── index.js            # Home page gallery slider interactions
│   ├── menu.js             # Menu category filters and active styling
│   ├── cart.js             # Client-side order/cart logic & WhatsApp/SMS checkout
│   ├── editSettings.js     # Admin dashboard settings validation/handlers
│   └── login.js            # Admin authentication helpers
│
├── style/                  # Modular styling system
│   ├── theme.css           # Core design system tokens (colors, dark mode, glows)
│   ├── index.css           # Home/landing page styling rules
│   ├── about.css           # About page styling rules
│   ├── menu.css            # Interactive menu listing & cart styling
│   ├── dashboard.css       # Core administrator panel styling
│   ├── login.css           # Admin authentication styling
│   ├── editSettings.css    # Admin edit settings view styling
│   ├── add.css             # Page addition component styles
│   ├── admin-shared.css    # Shared styling components for admin views
│   ├── admin_form.css      # Styling for category & item editing forms
│   ├── contact.css         # Location & contact information styling
│   ├── footer.css          # Public footer styling
│   └── view.css            # View listing layout styling
│
└── items/                  # Uploaded menu item images
```

---

## 🎨 Styling & Color Customization

The system features a centralized palette configuration. Rather than chasing colors through individual page stylesheets, global tokens are set inside [style/theme.css](file:///d:/xampp/htdocs/rest_menu/style/theme.css):

### Active Theme variables (`:root` light theme):
```css
:root {
    --olive-green: #42522B;      /* Brand accent color */
    --cream-white: #F7F5EA;      /* Cozy soft background */
    --dark-charcoal: #2B2B2A;    /* Primary text */
    --light-khaki: #CBB58B;      /* Delicate borders & shadows */
}
```

### Persistent Dark Mode variables:
```css
body.dark-mode {
    --bg-color: #1a1f11;         /* Deep olive-infused dark base */
    --card-bg: #2a2f1a;          /* Contrasted card panels */
    --text-color: #f7f5ea;       /* High-readability light cream text */
    --border-color: #42522b;
    --accent-blue: #cbb58b;      /* Warm golden accent highlight */
}
```

---

## 🔒 Security Best Practices

1. **CSRF Protection**: State-changing POST requests inside the admin panel are protected by a random token verification check generated per-session.
2. **On-The-Fly Password Hashing**: The login system automatically upgrades plain-text password credentials to modern PHP secure password hashes (`PASSWORD_DEFAULT` using bcrypt) on successful login.
3. **SQL Injection Security**: Always use MySQLi prepared queries containing parameter binding for all dynamic parameters in both public and admin files.
4. **Upload Filtering**: Enforce mime-type checks and size limits on image file uploads in `admin/addItem.php`, `admin/addCategory.php`, and `admin/editSettings.php`.

---

## 🔍 Troubleshooting

### ❌ Database Connection Failure
- Verify all database parameters (host, user, pass, database name) inside **[includes/connection.php](file:///d:/xampp/htdocs/rest_menu/includes/connection.php)**.
- Ensure the local MySQL service is active in your control panel.

### ❌ Uploaded Images Do Not Render
- Check file system directory permissions on both the `admin/bgs/`, `items/` and `admin/pics/` directories.
- Ensure the standard PHP values `upload_max_filesize` and `post_max_size` inside `php.ini` allow file transfers up to the size of your images.

### ❌ Dark Mode State Resets
- Ensure your web browser has `localStorage` permissions enabled. The state resides inside a persistent client-side database key named `theme`.
