# TSU Staff Profile Portal

A comprehensive web-based staff management system for Taraba State University, featuring profile management, ID card generation, staff directory, and administrative tools.

![TSU Logo](public/assets/images/tsu-logo.png)

## 🎯 Overview

The TSU Staff Profile Portal is a modern, secure, and feature-rich platform designed to streamline staff management at Taraba State University. It provides a centralized system for staff profiles, ID card generation, directory services, and administrative oversight.

## ✨ Key Features

### 👤 User Features
- **Profile Management**: Comprehensive profile creation and editing
- **Academic Records**: Education, experience, publications, skills, and certifications
- **Privacy Controls**: Public, university-only, or private profile visibility
- **ID Card Access**: View and download official staff ID cards
- **Staff Directory**: Search and browse university staff profiles
- **QR Code Integration**: Profile verification via QR code scanning

### 🔐 Authentication & Security
- **Email Verification**: Secure account activation via TSU email
- **Password Reset**: Self-service password recovery
- **Role-Based Access**: User, Admin, ID Card Manager, Nominal Role
- **CSRF Protection**: Security tokens for all forms
- **Session Management**: Secure session handling with remember-me option

### 👨‍💼 Administrative Features
- **User Management**: Activate, suspend, verify, and delete users
- **Bulk Operations**: Manage multiple users simultaneously
- **Real-Time Search**: Instant user filtering and search
- **Pagination**: Efficient navigation through large user lists
- **Activity Logs**: Track all system activities
- **Analytics Dashboard**: System statistics and insights

### 🆔 ID Card Management
- **Digital ID Cards**: Generate professional staff ID cards
- **QR Code Integration**: Embedded QR codes for verification
- **Bulk Generation**: Create multiple ID cards at once
- **Print & Download**: Export as PDF or print directly
- **Front & Back Design**: Complete ID card with all details
- **Blood Group Display**: Emergency information on cards

### 📊 Nominal Role System
- **Staff List Management**: View and filter staff by various criteria
- **Advanced Filtering**: Gender, staff type, faculty, department, unit, status
- **Data Export**: Export filtered data to CSV or Excel
- **Statistics Dashboard**: Real-time staff statistics
- **Dedicated Access**: Specialized role for HR/administrative tasks

### 🏢 Organizational Management
- **Faculties & Departments**: Manage academic structure
- **Units & Offices**: Non-teaching staff organization
- **Dynamic Dropdowns**: Cascading faculty-department selection
- **Flexible Assignment**: Support for both teaching and non-teaching staff

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+**: Core application logic
- **MySQL 5.7+**: Database management
- **Custom MVC Framework**: Lightweight and efficient
- **PDO**: Secure database interactions

### Frontend
- **Bootstrap 5**: Responsive UI framework
- **JavaScript (ES6+)**: Interactive features
- **Font Awesome 6**: Icon library
- **HTML5 & CSS3**: Modern web standards

### Libraries & Tools
- **PHPMailer**: Email functionality
- **QR Code API**: QR code generation
- **html2canvas**: Client-side rendering
- **jsPDF**: PDF generation
- **JSZip**: Bulk file downloads

## 📋 Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (Apache)
- HTTPS support (recommended)

### PHP Extensions
- PDO
- PDO_MySQL
- mbstring
- openssl
- curl
- gd or imagick (for image processing)
- fileinfo

### Recommended
- Composer (for dependency management)
- SSL certificate
- 512MB+ RAM
- 1GB+ disk space

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/tsu-staff-portal.git
cd tsu-staff-portal
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` with your configuration:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=tsu_staff_portal
DB_USER=your_username
DB_PASS=your_password

# Application Settings
APP_URL=https://staff.tsuniversity.edu.ng
APP_ENV=production

# Email Configuration
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM=noreply@tsuniversity.edu.ng
MAIL_FROM_NAME="TSU Staff Portal"
```

### 4. Database Setup

#### Option A: Complete Setup
```bash
mysql -u root -p < database/setup_database.sql
```

#### Option B: Step-by-Step
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE tsu_staff_portal"

# Run migrations
mysql -u root -p tsu_staff_portal < database/migrations/001_create_tables.sql
mysql -u root -p tsu_staff_portal < database/migrations/002_add_user_role.sql
# ... run all migrations in order

# Seed data
mysql -u root -p tsu_staff_portal < database/seeds/faculties_departments.sql
mysql -u root -p tsu_staff_portal < database/seeds/units_offices.sql
```

### 5. Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod -R 755 public/qrcodes/
```

### 6. Configure Web Server

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 7. Create Admin User
```bash
mysql -u root -p tsu_staff_portal < database/create_admin_user.sql
```

Default admin credentials:
- Email: `admin@tsuniversity.edu.ng`
- Password: `Admin@123` (change immediately after first login)

### 8. Additional Users

#### Create ID Card Manager
```bash
mysql -u root -p tsu_staff_portal < database/create_id_card_manager.sql
```

#### Create Nominal Role User
```bash
mysql -u root -p tsu_staff_portal < database/create_nominal_role_user.sql
```

## 📁 Project Structure

```
tsu-staff-portal/
├── app/
│   ├── Controllers/        # Application controllers
│   ├── Core/              # Core framework files
│   ├── Helpers/           # Helper functions
│   ├── Middleware/        # Authentication & authorization
│   ├── Models/            # Data models
│   └── Views/             # View templates
├── config/                # Configuration files
├── database/              # Database migrations & seeds
├── public/                # Public web root
│   ├── assets/           # CSS, JS, images
│   ├── uploads/          # User uploads
│   └── qrcodes/          # Generated QR codes
├── routes/                # Route definitions
├── storage/               # Application storage
├── .env                   # Environment configuration
├── .htaccess             # Apache configuration
├── composer.json         # PHP dependencies
├── index.php             # Application entry point
└── README.md             # This file
```

## 🔑 User Roles

### 1. User (Staff Member)
- Create and manage personal profile
- Add education, experience, publications, skills
- Control profile visibility
- View staff directory
- Access own ID card

### 2. Admin
- Full system access
- User management (activate, suspend, delete)
- Generate ID cards
- View analytics and activity logs
- Manage faculties, departments, and units
- System settings configuration

### 3. ID Card Manager
- Browse pending ID card requests
- Generate and print ID cards
- View print history
- Bulk ID card operations
- Access generated cards archive

### 4. Nominal Role
- View staff lists with advanced filtering
- Export staff data (CSV/Excel)
- Filter by gender, staff type, faculty, department, unit
- View staff statistics
- Generate reports

## 📖 Usage Guide

### For Staff Members

#### 1. Registration
1. Visit the portal homepage
2. Click "Register" or "Create Account"
3. Enter your TSU email address
4. Choose staff type (Teaching/Non-Teaching)
5. Select faculty/department or unit
6. Create a secure password
7. Verify email with 6-digit code

#### 2. Profile Setup
1. Complete basic information (name, title, designation)
2. Upload profile photo
3. Add professional summary
4. Set profile visibility preference
5. Submit to activate account

#### 3. Adding Information
- **Education**: Add degrees, institutions, years
- **Experience**: Work history and positions
- **Publications**: Research papers, books, articles
- **Skills**: Technical and professional skills
- **Certifications**: Professional certifications
- **Awards**: Honors and recognitions

#### 4. Privacy Settings
- **Public**: Visible to everyone
- **University Only**: Visible to TSU staff only
- **Private**: Limited information, verification only

### For Administrators

#### 1. User Management
1. Navigate to Admin Dashboard → Users
2. Search, filter, or browse users
3. Select users for bulk operations
4. Activate, suspend, verify, or delete accounts

#### 2. ID Card Generation
1. Go to Admin Dashboard → ID Cards
2. Select users for ID card generation
3. Preview cards before generating
4. Download as PDF or print directly

#### 3. System Configuration
1. Access Admin Dashboard → Settings
2. Configure email settings
3. Manage faculties and departments
4. Set system preferences

### For ID Card Managers

#### 1. Browse Requests
1. Login to ID Card Manager dashboard
2. View pending ID card requests
3. Filter by faculty, department, or status

#### 2. Generate Cards
1. Select users from browse page
2. Click "Generate ID Cards"
3. Preview cards
4. Download or print

#### 3. View History
1. Navigate to Print History
2. View all generated cards
3. Regenerate if needed

## 🔒 Security Features

- **Password Hashing**: bcrypt with salt
- **CSRF Protection**: Token-based form security
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: Secure session handling
- **Email Verification**: Required for account activation
- **Role-Based Access Control**: Granular permissions
- **Activity Logging**: Audit trail for all actions
- **HTTPS Enforcement**: Secure data transmission

## 🎨 Customization

### Branding
Edit the following files to customize branding:
- `public/assets/images/tsu-logo.png` - University logo
- `public/assets/images/tsu-building.jpg` - Background image
- `config/app.php` - Application name and settings

### Email Templates
Customize email templates in:
- `app/Helpers/EmailHelper.php`

### ID Card Design
Modify ID card layout in:
- `app/Views/admin/id-card-preview.php`
- `app/Views/admin/id-card-generator.php`

## 🐛 Troubleshooting

### Common Issues

#### 1. Database Connection Error
```
Solution: Check .env file for correct database credentials
```

#### 2. Email Not Sending
```
Solution: Verify SMTP settings in .env and check firewall rules
```

#### 3. File Upload Errors
```
Solution: Check folder permissions (755) for uploads/ and qrcodes/
```

#### 4. 404 Errors
```
Solution: Ensure mod_rewrite is enabled and .htaccess is present
```

#### 5. Session Issues
```
Solution: Check PHP session configuration and storage permissions
```

### Debug Mode
Enable debug mode in `.env`:
```env
APP_ENV=development
APP_DEBUG=true
```

## 📊 Database Migrations

### Running Migrations
```bash
# Run all migrations
php database/run_migrations.php

# Or manually
mysql -u root -p tsu_staff_portal < database/migrations/001_create_tables.sql
```

### Available Migrations
- `001_create_tables.sql` - Initial database schema
- `002_add_user_role.sql` - User roles system
- `003_add_education_display_years.sql` - Education display options
- `004_add_blood_group.sql` - Blood group field
- `005_add_staff_type_and_unit.sql` - Staff categorization
- `006_add_staff_number_unique_constraint.sql` - Unique staff numbers
- `007_add_id_card_manager_role.sql` - ID card manager role
- `add_gender_field.sql` - Gender field for profiles
- `add_profile_views_column.sql` - Profile view tracking
- `add_id_card_generated_column.sql` - ID card generation tracking
- `add_qr_code_to_profiles.sql` - QR code integration

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add comments for complex logic
- Test thoroughly before submitting

## 📝 License

This project is proprietary software developed for Taraba State University. All rights reserved.

## 👥 Authors & Contributors

- **Development Team** - Initial work and ongoing maintenance
- **TSU ICT Department** - Requirements and testing
- **University Administration** - Project oversight

## 📞 Support

For support, please contact:

- **Email**: ict@tsuniversity.edu.ng
- **Website**: https://tsuniversity.edu.ng
- **Portal**: https://staff.tsuniversity.edu.ng

## 🔄 Changelog

### Version 2.0.0 (February 2026)
- Added nominal role system with advanced filtering
- Implemented gender field for profiles
- Enhanced QR code scanning for private profiles
- Added Publications tab to profile management
- Improved admin users page with pagination and real-time search
- Created comprehensive Terms & Conditions and Privacy Policy
- Fixed CV download functionality
- Enhanced ID card generation with tracking
- Added units management for non-teaching staff

### Version 1.0.0 (Initial Release)
- Core profile management system
- User authentication and authorization
- ID card generation
- Staff directory
- Admin dashboard
- Basic reporting

## 🎯 Roadmap

### Planned Features
- [ ] Mobile app (iOS/Android)
- [ ] Advanced analytics dashboard
- [ ] Automated email notifications
- [ ] Document management system
- [ ] Performance review module
- [ ] Leave management integration
- [ ] Payroll integration
- [ ] Multi-language support
- [ ] API for third-party integrations
- [ ] Advanced search with filters
- [ ] Bulk import/export functionality
- [ ] Two-factor authentication

## 📚 Documentation

Additional documentation available:
- [Installation Guide](docs/INSTALLATION.md)
- [User Manual](docs/USER_MANUAL.md)
- [Admin Guide](docs/ADMIN_GUIDE.md)
- [API Documentation](docs/API.md)
- [Database Schema](docs/DATABASE.md)

## 🙏 Acknowledgments

- Taraba State University for project sponsorship
- ICT Department for technical support
- All staff members who provided feedback
- Open source community for tools and libraries

---

**Made with ❤️ for Taraba State University**

*For more information, visit [tsuniversity.edu.ng](https://tsuniversity.edu.ng)*
