# Restaurant Menu Management System

A comprehensive, premium web-based restaurant menu management system built with PHP and MySQL. This application features an elegant **Olive Green & Cream White theme**, persistent **Dark Mode** toggle, active category border glow animations, a user-friendly public interface for customers, and a powerful, centralized admin dashboard.

---

## 🌟 Features

### 🍽️ Public Features
- **Modern Landing Page**: Beautiful, responsive hero section with dynamic background images, restaurant details, and clear opening hours.
- **Interactive Menu**: Multi-category menu view with active tab transitions and order-trigger buttons.
- **Persistent Dark Mode**: Simple sun/moon icon toggle in the navigation bar. Remembers the user's choice across visits using `localStorage`.
- **Olive Green Design System**: A premium, curated palette consisting of Olive Green (`#42522B`), Cream White (`#F7F5EA`), Dark Charcoal (`#2B2B2A`), and Light Khaki (`#CBB58B`) accents.
- **Glowing Hover Effects**: Interactive glowing border animations for active categories, buttons, and links.
- **Item Details**: Dedicated view showing detailed ingredients, item description, and pricing structure.
- **Interactive Contact Page**: Easily access the location map, phone number, email, and social media handles.
- **Fluid Responsiveness**: Works perfectly across desktops, tablets, and smartphones.

### 🛡️ Admin Features
- **Secure Authentication**: Protected administrator login gateway with a robust session-based check.
- **Central Dashboard**: Comprehensive overview for editing your categories, items, and platform parameters.
- **Category Management**:
  - Add, edit, delete, and re-order menu categories.
  - Upload custom category images and specific visual icons.
- **Menu Item Management**:
  - Full CRUD operations for menu items.
  - Custom image upload, pricing configurations, and description details.
  - Set custom ordering so specific dishes highlight at the top.
- **System Settings Configuration**:
  - Customize restaurant branding, name, description, and logo.
  - Manage contact detail values (address, email, phone, and maps embedding).
  - Configure background media for individual views.
  - Set custom opening hour displays.
  - Configure optional Telegram bot triggers for order alerts.

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.2+ (Supports PDO prepared queries)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, Tailwind CSS v4, Vanilla JavaScript
- **Icons**: Font Awesome v6 CDN
- **Build Tool**: Tailwind CSS CLI (run locally, no server-side build needed)
- **Server Environment**: Apache (fully compatible with local XAMPP/WampServer installs)

---

## 📋 System Requirements

- PHP 7.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server with rewrite module enabled
- Required PHP Extensions: `mysqli`, `pdo_mysql`, `session`, `file uploads` enabled

---

## 🚀 Installation & Setup

### Step 1: Copy Files to Web Root

Copy the entire project folder to your local server's directory:
- **XAMPP**: `C:\xampp\htdocs\rest_menu\`
- **WampServer**: `C:\wamp64\www\rest_menu\`
- **Linux/Mac**: `/var/www/html/rest_menu/`

### Step 2: Database Setup

1. Start your Apache and MySQL database server.
2. Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Create a new database named `menu` (UTF-8 Unicode collation recommended).
4. Go to **Import**, select `DB/updated_menu.sql` (recommended for latest schema adjustments) or `DB/menu.sql`, and click **Go** to complete database import.

### Step 3: Database Connection Configuration

Edit **[includes/connection.php](file:///d:/xampp/htdocs/rest_menu/includes/connection.php)** to match your local server credentials:

```php
$dbHost = 'localhost';
$dbUsername = 'root';      // Your MySQL username
$dbPassword = '';          // Your MySQL password
$dbName = 'menu';          // Database name
```

### Step 4: Configure Write Permissions

Ensure your server has write privileges on the folders designated for uploaded assets:
- `bgs/` - Logo and background images
- `items/` - Menu item thumbnails

On Linux or Mac environments:
```bash
chmod 755 bgs/
chmod 755 items/
```

### Step 5: Default Admin Credentials

Access the dashboard and log in using the pre-configured credentials:
- **Username**: `user1`
- **Password**: `123456`

> [!WARNING]
> Ensure you change the password immediately in the database or admin interface after initial setup for production security.

---

## 📂 Project Structure

```text
rest_menu/
├── .htaccess               # Apache configuration for security and redirections
├── README.md               # System documentation
├── index.php               # Public homepage
├── menu.php                # Public interactive menu page
├── ingredients.php         # Dedicated menu item details page
├── contact.php             # Contact and location page
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
│   ├── viewItems.php       # Menu items listing and management
│   ├── editSettings.php    # General website parameters & branding setup
│   ├── editTelegram.php    # Telegram bot configuration management
│   ├── exportItems.php     # Export menu items data
│   ├── importItems.php     # Import menu items data
│   ├── manageGallery.php   # Gallery and slider settings
│   ├── bgs/                # Admin-specific background settings templates
│   └── pics/               # Admin-specific picture library
│
├── includes/               # Common shared templates & logic
│   ├── auth.php            # Admin session authentication security handler
│   ├── connection.php      # MySQL database connection configuration
│   ├── header.php          # Site layout top navigation header component
│   └── footer.php          # Site layout footer component
│
├── DB/                     # Database SQL scripts
│   ├── menu.sql            # Core database schema
│
├── JS/                     # Interactive scripts
│   ├── theme.js            # Persistent Light/Dark mode toggler logic
│   ├── index.js            # Home page interactions
│   ├── menu.js             # Menu category filters and active styling
│   └── cart.js             # Client-side order/cart logic
│
├── style/                  # Single CSS output
│   └── tailwind.css        # Compiled Tailwind CSS (all styles)
│
├── src/                    # Tailwind source (build only, not deployed)
│   └── input.css           # Theme config, components, animations
│
├── bgs/                    # Background images and restaurant logos
└── items/                  # Uploaded menu item images
```

---

## 🎨 Styling & Color Customization

The system uses **Tailwind CSS v4** with all custom theme values defined in [src/input.css](src/input.css) via the `@theme` directive.

### Custom Theme Tokens:
```css
@theme {
  --color-olive: #42522B;
  --color-cream: #F7F5EA;
  --color-khaki: #CBB58B;
  --color-charcoal: #2B2B2A;
  --font-poppins: "Poppins", sans-serif;
  --font-inter: "Inter", sans-serif;
}
```

### Persistent Dark Mode:
Dark mode is class-based — toggling the `dark` class on `<html>` via `localStorage`. Tailwind's `dark:` variant handles all overrides.

### Rebuilding CSS (after theme changes):
```bash
npm run build     # one-time build
npm run watch     # auto-rebuild on changes
```
Run locally on your machine, then upload the generated `style/tailwind.css` to your server. No build step is needed on the server.

---

## 🔒 Security Best Practices

1. **Hashing Passwords**: The default configuration stores passwords directly. It is highly recommended to implement PHP `password_hash()` and `password_verify()` inside `login.php` for production environments.
2. **SQL Injection Security**: Always use PDO/MySQLi prepared queries containing parameter binding for all dynamic parameters.
3. **Upload Filtering**: Enforce mime-type checks and size limits on image file uploads in `admin/addItem.php` and `admin/addCategory.php`.

---

## 🔍 Troubleshooting

### ❌ Database Connection Failure
- Verify all database parameters (host, user, pass, database name) inside **[includes/connection.php](file:///d:/xampp/htdocs/rest_menu/includes/connection.php)**.
- Ensure the local MySQL service is active in your control panel.

### ❌ Uploaded Images Do Not Render
- Check file system directory permissions on both the `bgs/` and `items/` directories.
- Ensure the standard PHP values `upload_max_filesize` and `post_max_size` inside `php.ini` allow file transfers up to the size of your images.

### ❌ Dark Mode State Resets
- Ensure your web browser has `localStorage` permissions enabled. The state resides inside a persistent client-side database key named `theme`.
