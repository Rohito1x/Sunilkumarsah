# Vision Point Educational Service - Implementation & Optimization Checklist

## Refactoring Completed
- [x] Fix critical syntax error in `setup.php` (lack of space in DROP TABLE Cascade statement).
- [x] Replace old Sajilo Sewa schema in `database.sql` with correct Krishna Education Foundation schema.
- [x] Refactor duplicate modals across 5 pages (`index.php`, `cities.php`, `courses.php`, `guides.php`, `study-in-india.php`) into a single centralized `includes/modals.php` file included globally via `includes/footer.php`.
- [x] Add missing course streams in the `faculty` dropdown select (e.g., Dental, Behavioral Science, Allied Health, Agriculture).
- [x] Add dynamic modal pre-filling for course inquiries on `courses.php` and institutions on `cities.php` using a centralized delegated click event listener in `includes/footer.php`.
- [x] Add comprehensive Review & Feedback feature: created `reviews.php` frontend page with rating breakdown stats, filter buttons, interactive 5-star rating form & AJAX submission, added `reviews` table schema & seeds to `database.sql`, `setup.php`, and `jaiswal/update_schema.php`, updated `submit_form.php`, added footer & navigation menu links, and integrated a complete Reviews & Feedback management section in `jaiswal/dashboard.php`.
- [x] Add Accreditation section to `index.php` matching design with interactive International / National pill tab switcher displaying `int1.jpeg`, `int2.jpeg`, `int3.jpeg` and `nat1.jpeg`, `nat2.jpeg`, `nat3.jpeg`.
- [x] Resolve HTTP 500 error for live production server deployment on `krishnaeducationfoundation.edu.np`:
  - Created centralized [`config.php`](file:///c:/xampp/htdocs/keb1/config.php) to manage MySQL host, user, password, port, and environment settings.
  - Hardened [`includes/db.php`](file:///c:/xampp/htdocs/keb1/includes/db.php) to prevent fatal script termination (`die()`) on connection issues and allow public pages to render seamlessly in fallback mode.
  - Created production-grade [`.htaccess`](file:///c:/xampp/htdocs/keb1/..htaccess) with DirectoryIndex, MIME types, security protection, and custom error handling.
  - Added fallback datasets to [`includes/header.php`](file:///c:/xampp/htdocs/keb1/includes/header.php), [`includes/institutions.php`](file:///c:/xampp/htdocs/keb1/includes/institutions.php), [`courses.php`](file:///c:/xampp/htdocs/keb1/courses.php), and [`guides.php`](file:///c:/xampp/htdocs/keb1/guides.php).
  - Built diagnostic health tool [`check_status.php`](file:///c:/xampp/htdocs/keb1/check_status.php) to verify PHP extensions, database connection status, and directory write permissions.
- [x] Fix mobile view layout bug and missing icon glyphs on "A Reflection of Our Commitment to Excellence" Fun Facts section:
  - Replaced hardcoded `col-4` with responsive grid classes (`col-12 col-md-4`) and `row g-3 g-md-0`.
  - Upgraded mobile statistics into modern frosted glass cards with proper padding, clean spacing, and fluid font sizing (`clamp()`) to prevent text overlap.
  - Implemented centralized `formatFaIcon()` helper in [`includes/db.php`](file:///c:/xampp/htdocs/keb1/includes/db.php) and forced `Font Awesome 6 Free` font-family fallback in CSS to fix broken missing icon glyphs (`⮽`).
  - Updated admin dashboard fun facts preview and handlers in [`jaiswal/dashboard.php`](file:///c:/xampp/htdocs/keb1/jaiswal/dashboard.php).


