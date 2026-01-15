# Restaurant Menu Management System

A comprehensive web-based restaurant menu management system built with PHP and MySQL. This application provides a beautiful, user-friendly interface for displaying restaurant menus to customers, along with a powerful admin dashboard for managing menu items, categories, and restaurant settings.

## Features

### Public Features
- **Home Page**: Beautiful hero section with customizable background, restaurant information, and opening hours
- **Menu Display**: Interactive menu page with category-based navigation
- **Item Details**: Detailed view of menu items with ingredients and pricing
- **Contact Page**: Contact information with social media links and location map
- **Responsive Design**: Mobile-friendly interface that works on all devices
- **Dual Currency Support**: Display prices in LBP (Lebanese Pounds) or USD

### Admin Features
- **User Authentication**: Secure login system with admin privileges
- **Dashboard**: Centralized control panel for all administrative functions
- **Category Management**: 
  - Add, edit, and delete menu categories
  - Upload category icons and images
  - Custom ordering of categories
- **Item Management**:
  - Add, edit, and delete menu items
  - Upload item images
  - Set prices in LBP and USD
  - Add ingredients/descriptions
  - Custom ordering of items within categories
- **Settings Management**:
  - Configure restaurant name, logo, and description
  - Set contact information (phone, email, address)
  - Configure social media links (Instagram, Facebook)
  - Upload custom backgrounds for different pages
  - Set opening hours and location map link
  - Configure Telegram bot integration (chat ID and bot token)

## Technology Stack

- **Backend**: PHP 7.2+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome
- **Server**: Apache (XAMPP recommended)

## Requirements

- PHP 7.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server
- PHP extensions: mysqli, session, file upload support

## Installation

### 1. Clone or Download the Project

Place the project files in your web server directory:
- **XAMPP**: `C:\xampp\htdocs\rest_menu`
- **WAMP**: `C:\wamp64\www\rest_menu`
- **Linux**: `/var/www/html/rest_menu`

### 2. Database Setup

1. Open phpMyAdmin or your MySQL client
2. Create a new database named `menu`
3. Import the database schema:
   - Use `DB/updated_menu.sql` for the latest schema (recommended)
   - Or use `DB/menu.sql` for the original schema

The database will include:
- `categories` table for menu categories
- `items` table for menu items
- `users` table for admin accounts
- `settings` table for restaurant configuration

### 3. Database Configuration

Edit `connection.php` and update the database credentials:

```php
$dbHost = 'localhost';
$dbUsername = 'root';      // Your MySQL username
$dbPassword = '';          // Your MySQL password
$dbName = 'menu';          // Database name
```

### 4. File Permissions

Ensure the following directories are writable for image uploads:
- `bgs/` - For background images and logos
- `items/` - For menu item images

On Linux/Mac:
```bash
chmod 755 bgs/
chmod 755 items/
```

### 5. Default Admin Account

The default admin credentials are:
- **Username**: `user1`
- **Password**: `123456`

**⚠️ Important**: Change the default password immediately after first login for security!

## Project Structure

```
rest_menu/
├── bgs/                    # Background images and logos
├── items/                  # Menu item images
├── style/                  # CSS stylesheets
│   ├── index.css
│   ├── menu.css
│   ├── dashboard.css
│   ├── login.css
│   └── ...
├── JS/                     # JavaScript files
│   ├── index.js
│   ├── menu.js
│   └── ...
├── DB/                     # Database SQL files
│   ├── menu.sql
│   └── updated_menu.sql
├── index.php              # Home page
├── menu.php               # Menu display page
├── ingredients.php        # Item details page
├── contact.php            # Contact page
├── login.php              # Admin login
├── dashboard.php          # Admin dashboard
├── connection.php         # Database connection
├── header.php             # Site header
├── footer.php             # Site footer
├── addItem.php            # Add menu item
├── addCategory.php        # Add category
├── editItem.php           # Edit menu item
├── editCategory.php       # Edit category
├── editSettings.php       # Restaurant settings
├── viewItems.php          # View all items
├── viewCategories.php     # View all categories
├── deleteItem.php         # Delete menu item
├── deleteCategory.php     # Delete category
└── README.md              # This file
```

## Usage

### Accessing the Application

1. Start your web server (XAMPP/WAMP/LAMP)
2. Start MySQL service
3. Open your browser and navigate to:
   ```
   http://localhost/rest_menu/
   ```

### Admin Access

1. Navigate to the login page or click on admin login
2. Use the default credentials (or your custom admin account)
3. Access the dashboard to manage menu items and settings

### Adding Menu Items

1. Log in to the admin dashboard
2. Click "Add Item"
3. Fill in the item details:
   - Item name
   - Category selection
   - Price (LBP and/or USD)
   - Ingredients/description
   - Upload item image
   - Set display order
4. Save the item

### Managing Categories

1. From the dashboard, click "Add Category" or "View Categories"
2. Add/edit categories with:
   - Category name
   - Category icon/image
   - Display order
3. Categories appear as tabs on the menu page

### Configuring Restaurant Settings

1. From the dashboard, click "Settings"
2. Configure:
   - Restaurant information (name, description, logo)
   - Contact details (phone, email, address)
   - Social media links
   - Opening hours
   - Background images for different pages
   - Telegram bot integration (optional)

## Database Schema

### Categories Table
- `cat_id` - Primary key
- `cat_name` - Category name
- `cat_picture` - Category image path
- `cat_icon` - Category icon path
- `Order` - Display order

### Items Table
- `item_id` - Primary key
- `item_name` - Item name
- `item_category` - Category name
- `item_pricelbp` - Price in Lebanese Pounds
- `item_priceusd` - Price in USD
- `Ingredients` - Item description/ingredients
- `item_pic` - Item image path
- `Order` - Display order within category

### Users Table
- `user_id` - Primary key
- `username` - Login username
- `userpassword` - Login password (plain text - consider hashing)
- `isAdmin` - Admin flag (1 = admin, 0 = regular user)

### Settings Table
- `id` - Primary key
- `restaurant_name` - Restaurant name
- `restaurant_logo` - Logo image path
- `home_bg` - Home page background
- `menu_bg` - Menu page background
- `contact_bg` - Contact page background
- `restaurant_email` - Email address
- `restaurant_phone` - Phone number
- `restaurant_address` - Physical address
- `restaurant_maps` - Google Maps link
- `restaurant_description` - Restaurant description
- `opening_hours` - Opening hours text
- `opening_title` - Opening hours title
- `whatsapp_number` - WhatsApp contact
- `instagram_url` - Instagram profile URL
- `facebook_url` - Facebook page URL
- `chat_id` - Telegram chat ID
- `bot_token` - Telegram bot token

## Security Considerations

⚠️ **Important Security Notes:**

1. **Password Storage**: Currently, passwords are stored in plain text. Consider implementing password hashing (e.g., `password_hash()` and `password_verify()`)

2. **SQL Injection**: The code uses prepared statements in most places, but review all database queries

3. **File Upload**: Implement file type validation and size limits for uploaded images

4. **Session Security**: Ensure proper session management and timeout

5. **Admin Access**: Restrict admin pages to authenticated admin users only

6. **Default Credentials**: Change default admin password immediately

## Customization

### Styling
- Modify CSS files in the `style/` directory
- Each page has its own CSS file for easy customization
- Use `index.css` for home page styling
- Use `menu.css` for menu page styling

### Adding Features
- The modular structure makes it easy to add new features
- Follow the existing pattern for database operations
- Use prepared statements for all database queries

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `connection.php`
- Ensure MySQL service is running
- Check that the database `menu` exists

### Image Upload Issues
- Check directory permissions (`bgs/` and `items/`)
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Ensure directories exist and are writable

### Session Issues
- Ensure PHP sessions are enabled
- Check session save path permissions
- Clear browser cookies if login issues persist

## Support

For issues or questions:
1. Check the database connection settings
2. Verify file permissions
3. Review PHP error logs
4. Ensure all required PHP extensions are installed

## License

This project is provided as-is for restaurant menu management purposes.

## Version

Current version includes:
- Category management with icons
- Item management with images
- Settings configuration
- Responsive design
- Dual currency support (LBP/USD)

---

**Note**: This is a PHP-based application designed for local server deployment. For production use, ensure proper security measures are implemented, including password hashing, input validation, and secure file upload handling.
