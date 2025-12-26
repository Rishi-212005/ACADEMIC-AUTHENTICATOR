# Academia Authenticator

A comprehensive OCR-based academic certificate verification system designed to combat fake degrees and credentials. Built for the Jharkhand Department of Higher & Technical Education, this platform enables fast and secure verification of academic certificates using advanced OCR technology.

## 🎯 Overview

Academia Authenticator is a web-based platform that allows institutions and organizations to upload, verify, and manage academic certificates. The system uses Optical Character Recognition (OCR) to extract key information from certificate images, validates them against trusted records, and provides clear verification results (Valid/Suspect/Invalid).

## ✨ Features

### Core Functionality
- **OCR-Based Extraction**: Automatic extraction of key fields from certificate images using advanced OCR engines (PaddleOCR, Tesseract)
- **Multi-User System**: Separate dashboards for Institutions, Organizations, and Administrators
- **Certificate Validation**: Automated validation against trusted registry with confidence scoring
- **Secure Authentication**: OTP-based login system with email verification via PHPMailer
- **Admin Dashboard**: Comprehensive admin panel for managing registrations, viewing statistics, and monitoring suspicious cases
- **File Management**: Secure file upload, storage, and hash-based verification (SHA256)
- **Bulk Processing**: Support for bulk certificate uploads via CSV

### Key Capabilities
- Fast verification process - results in seconds
- Role-based access control
- Audit logs and tracking
- Responsive web interface

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+** - Server-side logic and API endpoints
- **MySQL/MariaDB** - Database management with UTF-8 support
- **PHPMailer 6.10.0** - Email functionality for OTP delivery

### Frontend
- **HTML5** - Structure
- **Tailwind CSS** - Modern, responsive styling
- **Vanilla JavaScript** - Client-side interactivity
- **Font Awesome** - Icons and UI elements

### OCR & Processing
- **Python 3.x** - OCR processing scripts
- **PaddleOCR** - High-accuracy OCR engine (optional)
- **Tesseract OCR** - Primary OCR engine
- **Pillow (PIL)** - Image processing
- **OpenCV** - Image preprocessing and enhancement

### Infrastructure
- **Apache/Nginx** - Web server
- **Composer** - PHP dependency management

## 📁 Project Structure

```
.
├── assets/
│   └── js/                    # JavaScript files for frontend functionality
│       ├── admin_dashboard.js
│       ├── admin_detail.js
│       ├── admin_lists.js
│       ├── admin_login.js
│       ├── admin_pending_detail.js
│       ├── login.js
│       └── register.js
├── workers/                    # Python OCR processing scripts
│   ├── final_ocr_extractor.py
│   ├── certificate_ocr_extractor.py
│   ├── paddle_ocr_extractor.py
│   ├── standalone_ocr_extractor.py
│   └── ... (various OCR implementations)
├── uploads/                    # Uploaded files storage
│   ├── certificates/
│   └── ocr_images/
├── vendor/                     # PHP dependencies (Composer)
│   └── phpmailer/
├── scripts/                    # Database migration scripts
│   └── 2025-09-23-ocr-duplicate-to-new-table.sql
├── config.php                  # Database and app configuration
├── api_updated_final.php       # Main API endpoint handler
├── index.html                  # Landing page
├── login.php                   # Login page handler
├── register.php                # Registration handler
├── admin_dashboard.php         # Admin dashboard
├── institute_dashboard.php     # Institution dashboard
├── organisation_dashboard.php  # Organization dashboard
├── document_upload.php         # Certificate upload interface
├── document_ocr.php            # OCR processing handler
├── sih2026.sql                 # Database schema
├── composer.json               # PHP dependencies
└── package.json                # Frontend dependencies (optional)
```

## 🗄️ Database Schema

### Main Tables
- **users** - Approved user accounts (institutions/organizations)
- **registrations** - Pending registration requests
- **admins** - Administrator accounts
- **otps** - One-time passwords for user authentication
- **admin_otps** - OTPs for admin authentication
- **ocr_extracted_data** - Stored OCR extraction results
- **ocr_saved_details** - Detailed certificate information
- **organization_validations** - Validation results for organizations

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Python 3.7+ (for OCR processing)
- Composer (PHP dependency manager)
- Tesseract OCR installed on system
- (Optional) PaddleOCR for enhanced accuracy

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd CODE
```

### Step 2: Database Configuration
1. Create a MySQL database:
```sql
CREATE DATABASE sih2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u your_username -p sih2026 < sih2026.sql
```

3. Update database credentials in `config.php`:
```php
$DB_HOST = 'localhost';
$DB_USER = 'your_username';
$DB_PASS = 'your_password';
$DB_NAME = 'sih2026';
```

### Step 3: Install PHP Dependencies
```bash
composer install
```

### Step 4: Install Python Dependencies
```bash
pip install pytesseract pillow opencv-python
# Optional: For enhanced OCR accuracy
pip install paddlepaddle paddleocr
```

### Step 5: Configure Tesseract OCR
- **Windows**: Install Tesseract from [GitHub releases](https://github.com/UB-Mannheim/tesseract/wiki) and update path in Python scripts
- **Linux**: `sudo apt-get install tesseract-ocr`
- **macOS**: `brew install tesseract`

Update the Tesseract path in `workers/standalone_ocr_extractor.py`:
```python
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"  # Windows
# or
# pytesseract.pytesseract.tesseract_cmd = '/usr/bin/tesseract'  # Linux/Mac
```

### Step 6: Configure Email Settings (PHPMailer)
Update email configuration in the code where `send_otp_email()` is called to use your SMTP settings.

### Step 7: Set File Permissions
```bash
chmod -R 755 uploads/
chmod -R 755 workers/
```

### Step 8: Configure Web Server

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api_updated_final.php?action=$1 [QSA,L]
```

#### Nginx
```nginx
location /api/ {
    rewrite ^/api/(.*)$ /api_updated_final.php?action=$1 last;
}
```

## 👥 User Roles

### 1. Institutions
- Upload student certificates
- Manage certificate records
- View upload statistics
- Extract and store certificate data via OCR

### 2. Organizations
- Verify certificates uploaded by students
- View validation results
- Manage validation records
- Check certificate authenticity

### 3. Administrators
- Approve/reject registration requests
- View system statistics and analytics
- Monitor suspicious activities
- Manage user accounts
- Access comprehensive dashboards

## 🔐 Default Admin Credentials

**⚠️ Important**: Change these credentials after first login!

- **Email**: `admin@SIH`
- **Password**: `PASSWORD@SIH`

## 📡 API Endpoints

### Authentication
- `POST /api/register` - Register new institution/organization
- `POST /api/login_start` - Initiate login (sends OTP)
- `POST /api/verify_otp` - Verify OTP and complete login
- `POST /api/admin_login` - Admin login
- `POST /api/admin_verify_otp` - Admin OTP verification

### Registration Management (Admin)
- `GET /api/pending` - List pending registrations
- `POST /api/approve` - Approve registration
- `POST /api/reject` - Reject registration
- `GET /api/user_detail` - Get user details
- `GET /api/registration_detail` - Get registration details

### OCR & Certificates
- `POST /api/ocr_text` - Process OCR extraction
- `POST /api/save_ocr_data` - Save extracted OCR data
- `POST /api/validate_certificate` - Validate certificate
- `POST /api/bulk_upload_csv` - Bulk upload via CSV
- `GET /api/get_records` - Retrieve certificate records
- `POST /api/delete_record` - Delete a record

### Dashboard & Statistics
- `GET /api/admin_summary` - Admin dashboard statistics
- `GET /api/institute_dashboard_stats` - Institution statistics
- `GET /api/get_organization_validations` - Organization validation results
- `POST /api/save_organization_validation` - Save validation result

## 🔧 Configuration

### Environment Variables
Update `config.php` with your configuration:

```php
// Database
$DB_HOST = 'localhost';
$DB_USER = 'your_username';
$DB_PASS = 'your_password';
$DB_NAME = 'sih2026';

// Session configuration (if needed)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
```

### OCR Configuration
The system supports multiple OCR engines. Configure in Python scripts:
- **Primary**: Tesseract OCR (configurable path)
- **Enhanced**: PaddleOCR (optional, higher accuracy)

## 🧪 Testing

1. **Test Registration**:
   - Visit `/registration.html`
   - Register as Institution or Organization
   - Check email for confirmation

2. **Test Admin Approval**:
   - Login to admin panel
   - Approve pending registrations

3. **Test OCR**:
   - Login as Institution
   - Upload a certificate image
   - Verify OCR extraction accuracy

4. **Test Validation**:
   - Login as Organization
   - Upload certificate for verification
   - Check validation results

## 🔒 Security Features

- Password hashing using bcrypt
- OTP-based authentication
- Session management
- SQL injection prevention (prepared statements)
- File upload validation
- SHA256 file fingerprinting
- Role-based access control
- Secure file storage



## 📞 Support

For support, contact:
- Email: sairishikumar.2005@gmail.com
- Project Repository: [https://github.com/Rishi-212005/ACADEMIC-AUTHENTICATOR.git]

## 🗺️ Roadmap

- [ ] Blockchain integration for certificate verification
- [ ] Mobile app development
- [ ] Advanced AI-based fraud detection
- [ ] Multi-language support
- [ ] API documentation with Swagger/OpenAPI
- [ ] Enhanced OCR accuracy improvements
- [ ] Real-time notifications
- [ ] Advanced analytics and reporting

## 🙏 Acknowledgments

- Smart India Hackathon 2025
- Open source OCR communities (Tesseract, PaddleOCR)
- PHPMailer contributors

## 📄 Additional Notes

- The system is currently in prototype/development phase
- Regular database backups are recommended
- Monitor server logs for errors and security issues
- Update dependencies regularly for security patches

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Status**: Active Development

