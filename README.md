# SkyBook — International Flight Booking System

> **Second Refined Version** &bull; Production-grade Airline Reservation &amp; Ticketing Web Platform  
> **Technologies:** PHP 8+ &bull; MySQL / MariaDB &bull; HTML5 &bull; Vanilla CSS (Dark-First Design) &bull; Vanilla JavaScript &bull; XAMPP

---

## 🛫 Overview

**SkyBook** is a full-fledged international flight reservation web application engineered cleanly from scratch. While built for academic and college presentation standards, its architecture, design language, and workflows emulate top-tier commercial airlines and booking platforms.

### Key Highlights
- **Zero External APIs**: 100% self-hosted flight inventory running on a native relational database. No third-party rate limits, API keys, or latency.
- **Dark-First Modern UI**: Designed with international flight booking aesthetics—deep slate backgrounds, neon cyan accents, custom flight cards, and intuitive steppers.
- **Unified Draft Architecture**: **No separate `booking_drafts` table!** Unfinished reservations are stored in the core `bookings` table using `status = 'pending'` and transition to `status = 'booked'` upon confirmed payment.
- **Resilient Draft Validation**: Draft bookings survive browser restarts, tab closures, and page navigation. When resuming, seat counts and fare updates are automatically verified.
- **Virtual Boarding Pass Engine**: Automatically produces high-fidelity, print-ready digital boarding passes (`window.print()`) with simulated barcodes, QR codes, allocated gates, and seat assignments.
- **Administrative Control Center**: Complete role-based CRUD management for flight schedules, registered user directories, passenger manifests, booking ledgers, and view-only immutable transaction audit logs.

---

## 🗄️ Database Architecture

The system uses **5 relational tables** inside MariaDB/MySQL (`skybook_db`):

```
users (1) ──────────< bookings (N) >────────── (1) flights
                           │
             ┌─────────────┴─────────────┐
             ▼                           ▼
      passengers (N)                payments (N)
```

1. **`users`**: Traveler credentials and administrative accounts. Password hashes stored using standard Bcrypt (`password_hash`). Roles: `user` and `admin`.
2. **`flights`**: Active flight schedules with origin/destination codes, flight numbers, airline names, cabin classes, dynamic prices, and real-time available seats.
3. **`bookings`**: The central reservation table. Uses `status ENUM('pending', 'booked')`.
   - `pending`: Unfinished booking / saved draft.
   - `booked`: Verified, paid, and confirmed reservation.
4. **`passengers`**: Detailed traveler roster (title, legal name, gender, DOB, nationality, passport number, passport expiry) linked via `booking_id`.
5. **`payments`**: Transaction records with unique transaction IDs, demo payment methods, timestamps, and settlement amounts.

---

## 🚀 Setup & Installation (XAMPP)

Follow these simple steps to run SkyBook locally:

### 1. Start XAMPP Services
- Open the **XAMPP Control Panel**.
- Start **Apache** (Status: Running).
- Start **MySQL** (Status: Running).

### 2. Place Project in `htdocs`
- Ensure the project folder is placed inside your XAMPP `htdocs` folder:
  ```
  D:\xampp\htdocs\skyBook_2.0\
  ```

### 3. Database Initialization (Two Options)

#### Option A: 1-Click Browser Installer (Recommended)
Simply open your web browser and navigate to:
```
http://localhost/skyBook_2.0/database/install.php
```
Click **"Initialize & Seed Database Now"**. This creates `skybook_db`, builds all 5 tables, and seeds 30+ international flights and test accounts automatically.

#### Option B: Manual phpMyAdmin Import
1. Open [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
2. Click the **Import** tab.
3. Choose the file located at `D:\xampp\htdocs\skyBook_2.0\database\skybook_db.sql`.
4. Click **Go** / **Import**.

### 4. Access the Application
Open your browser and visit:
```
http://localhost/skyBook_2.0/
```

---

## URL
Live Website: https://flyskybook.site.je

📱Responsive Design: Accessible across desktop, laptop, tablet, and mobile devices.

## 🔄 End-to-End Workflows

### 1. Normal User Flow
```
Visitor (Public)
  ├── 1. Browse Home / Explore / About / Contact
  ├── 2. Search Flights (Origin, Destination, Date, Travelers, Class)
  ├── 3. View Results (Sorted by Cheapest / Earliest / Shortest)
  ├── 4. Click "Select Flight"
  │       └── If guest: Prompted to Login/Register (booking preserved)
  ├── 5. Fill Passenger Details (Lead traveler + companion forms)
  │       └── Draft automatically saved as 'pending' in bookings table
  ├── 6. Review Booking (Itinerary summary, passenger roster, fare + taxes)
  │       └── Can click "Edit Passenger Details" to update info
  ├── 7. Demo Payment (Card, UPI, or Net Banking options)
  │       └── Real-time seat decrement & status changes: 'pending' -> 'booked'
  ├── 8. Confirmation Page (PNR reference generated, transaction logged)
  ├── 9. Virtual Boarding Pass / E-Ticket (QR matrix, barcodes, print to PDF)
  └── 10. My Bookings (View past and upcoming confirmed trips)
```

### 2. Draft Booking Flow
```
Traveler begins booking
  ├── Fills passenger details
  ├── Booking record saved in database with status = 'pending'
  ├── Traveler exits the browser or navigates away
  ├── Traveler returns later & logs in
  ├── Clicks "Drafts" in top navigation
  ├── System checks flight availability and seats
  ├── Traveler clicks "Continue Booking"
  ├── Reviews fare & completes demo payment
  └── Status transitions from 'pending' to 'booked'
```

### 3. Administrator Flow
```
Admin Login (admin/login.php)
  ├── Dashboard KPI Metrics (Revenue, Confirmed, Pending Drafts, Flights, Users)
  ├── Flight Schedule Management (Full CRUD: View, Add, Edit, Cancel/Delete)
  │     └── Newly added flights appear instantly in search results
  ├── Registered Users Directory (View-only user details, passwords never exposed)
  ├── Passenger Manifests (View all traveler records and passport information)
  ├── All Bookings Ledger (Filter by Confirmed or Pending Drafts)
  └── Payment Ledger (Immutable view-only financial records)
```

---

## 📁 Clean Folder Structure

```
skyBook_2.0/
│
├── index.php                 # Application root entry point
│
├── pages/                    # Frontend page controllers
│   ├── home.php              # Dark-first landing page with search hero
│   ├── explore.php           # Destination travel guides & quick search
│   ├── about.php             # Platform architecture & engineering standards
│   ├── contact.php           # 24/7 global support & inquiry form
│   ├── search.php            # Dedicated flight search interface
│   ├── results.php           # Search results with live sorting
│   ├── login.php             # Secure traveler login with redirect memory
│   ├── register.php          # Account registration with duplicate prevention
│   ├── passenger.php         # Dynamic passenger roster & draft initialization
│   ├── review.php            # Itinerary review, passenger summary & edit links
│   ├── payment.php           # Demo payment gateway (Card/UPI/NetBanking)
│   ├── confirmation.php      # Verified confirmation screen
│   ├── ticket.php            # Virtual boarding pass & printable e-ticket
│   ├── drafts.php            # Saved pending reservations management
│   ├── my-bookings.php       # Confirmed reservations history
│   ├── profile.php           # Traveler profile & password change
│   └── logout.php            # Session termination handler
│
├── admin/                    # Protected administrative module
│   ├── index.php             # Control center dashboard with KPI metrics
│   ├── login.php             # Role-based admin authentication
│   ├── flights.php           # Flight CRUD management & scheduling
│   ├── users.php             # Registered user directory (view-only)
│   ├── passengers.php        # Global passenger manifest register
│   ├── bookings.php          # Complete bookings ledger (pending & booked)
│   ├── payments.php          # Immutable financial audit transactions
│   └── logout.php            # Admin session termination
│
├── backend/                  # Modular backend API endpoints
│   ├── auth/                 # login.php, register.php
│   ├── flights/              # search.php, manage.php
│   ├── bookings/             # create.php, draft.php, delete.php
│   ├── passengers/           # save.php
│   └── payments/             # process.php
│
├── config/
│   └── database.php          # PDO database wrapper & auto-connection recovery
│
├── includes/
│   ├── functions.php         # Authentication, URL generators, CSRF, formatters
│   ├── header.php            # Public responsive navigation & flash alerts
│   ├── footer.php            # Global footer with brand & project notes
│   ├── admin_header.php      # Admin sidebar, top bar & strict route guard
│   └── admin_footer.php      # Admin layout closure & scripts
│
├── css/
│   ├── main.css              # Dark design system tokens, responsive grid/flex
│   ├── ticket.css            # Boarding pass styling, perforated lines, print styles
│   └── admin.css             # Admin dashboard cards, sidebar & tables
│
├── js/
│   ├── main.js               # Mobile navigation, dropdowns, auto alert dismissal
│   ├── flight-search.js      # Airport quick swap, date validation, live sorting
│   ├── booking.js            # Payment tab switcher, simulated loading state
│   └── admin.js              # Modals for flight CRUD, live client-side table filter
│
├── database/
│   ├── skybook_db.sql        # Complete schema with 5 tables & realistic sample data
│   └── install.php           # 1-click web installer & seeder utility
│
└── README.md                 # Documentation & college viva guide
```

---

## 🎓 College Viva & Presentation Guide

When demonstrating SkyBook to your professor or examiner, emphasize these key design decisions:

1. **Why not a separate `booking_drafts` table?**
   - *Answer*: Creating a duplicate drafts table causes data redundancy and schema drift. By maintaining a single `bookings` table with a `status` field (`pending` vs `booked`), the schema remains normalized, foreign keys link cleanly to `passengers`, and transitioning from a draft to a confirmed booking requires an atomic `UPDATE` without copying records between tables.
2. **How does the system ensure security without an external API?**
   - *Answer*: All database interactions use PDO prepared statements with bound parameters to eliminate SQL injection. Passwords are encrypted with standard Bcrypt algorithms (`password_hash`), sessions enforce `HttpOnly` cookie constraints, and admin routes strictly check `role === 'admin'`.
3. **How does draft validation work?**
   - *Answer*: When a traveler resumes a pending booking from `pages/drafts.php`, the system queries the current flight record in `flights`. It checks that the flight is still `active`, that `available_seats >= passenger_count`, and recalibrates the base fare if the administrator updated airline pricing in the interim.
4. **How are seat numbers and boarding passes generated?**
   - *Answer*: Upon successful demo checkout, available flight seats are atomically decremented in MariaDB, a payment record is generated with a unique transaction reference, and the e-ticket engine renders an SVG/CSS boarding pass with realistic seat assignments (e.g., 14A, 14B) and print-optimized CSS.

---

&copy; SkyBook Flight Systems. Second Refined Version. Built cleanly for academic and commercial-grade demonstration.
