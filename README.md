# Vision Point Educational Service

Official website and web portal for **Vision Point Educational Service**, a premier educational consultancy in Nepal providing student counseling, admissions, scholarships, and visa guidance for India and worldwide destinations.

🌐 **Website:** [https://visionpoint.edu.np](https://visionpoint.edu.np)  
📍 **Office:** Vidhyapati Chowk, Janakpur / Biratnagar, Nepal  
📞 **Contact:** +977 9807765812  

---

## 🛠️ Technology Stack
* **Backend:** PHP 8.3 (Object-Oriented & Procedural helpers, PDO MySQL)
* **Frontend:** HTML5, CSS3, JavaScript (ES6+), jQuery, Bootstrap 5.3, Font Awesome 6
* **Web Server:** LiteSpeed Web Server / Apache (cPanel, CageFS)
* **Database:** MySQL 8.0+ / MariaDB 10.4+ (InnoDB, UTF8mb4)
* **Security:** Timing-safe CSRF defense, W3C `Sec-Fetch-Site` verification, brute-force rate-limiting, hardened session cookies, MIME-validated file uploads

---

## ✨ Key Features
* **Academic Counseling & Inquiries:** Dynamic contact & study abroad counseling forms with instant processing.
* **Online Student Enrollment:** Multi-step digital enrollment form supporting document and photo uploads.
* **Online Payment Receipt Submission:** Students can upload bank/Fonepay payment receipts (`.jpg`, `.jpeg`, `.png`, `.pdf`) and select their regional branch for accounting verification.
* **Comprehensive Admin CMS (`/vision`):**
  * Inquiries, Enrollments & Payment Voucher management with PDF/CSV export.
  * Office Location & Interactive Google Maps management.
  * Campus Showcase Video management (MP4/WebM uploads or YouTube embeds).
  * Bank Accounts & QR Code upload manager.
  * Destinations, Course Streams, Institutions, and Educational Guides CRUD.
  * Centralized SEO metadata management & dynamic XML sitemap generation.
* **Production Hardened:** Script-execution blocked in all upload directories, hardened session manager, and zero sensitive data leakage in error logs.

---

## 🚀 Deployment Instructions (cPanel)

1. **Git Deployment:**
   * Go to **cPanel** $\rightarrow$ **Git™ Version Control**.
   * Add or manage repository using repository URL: `https://github.com/Rohito1x/Sunilkumarsah.git`.
   * Click **Update from Remote** (Pull).

2. **File Permissions:**
   Ensure the following directories are writable (permission `755`):
   * `uploads/`
   * `uploads/receipts/`
   * `uploads/videos/`
   * `sessions/`

3. **Database Setup:**
   * Import `database.sql` into your cPanel MySQL database.
   * Verify database credentials in `config.php`.

4. **Admin Portal:**
   * Access URL: `https://your-domain.com/login.php`
   * Default Administrator: `admin@visionpoint.edu.np`
