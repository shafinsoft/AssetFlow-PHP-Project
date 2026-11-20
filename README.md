# AssetFlow (PHP + MySQL)
Asset management system with Admin and Customer roles.

## Run (XAMPP)
1. Start Apache and MySQL in XAMPP.
2. Copy the AssetFlow folder to C:\xampp\htdocs\  (so index.php is at C:\xampp\htdocs\AssetFlow\index.php)
3. Open http://localhost/phpmyadmin -> Import -> database/assetflow.sql -> Go
4. Open http://localhost/AssetFlow

## Default Admin (auto-created on first run)
admin@assetflow.com / Admin@123  (change it from the Profile page)
Signup always creates a Customer.

## Features
- Signup, Login, Logout, Profile (change name and password)
- Admin and Customer roles; customers can never touch each other's assets
- Asset CRUD with photo, category, status, warranty date; Search, Filter, Pagination
- Table and Cards views, product detail page, print
- Category CRUD (Admin), Customer list (Admin)
- Dashboard: statistics, status chart, category chart, recent assets, warranty alerts
- CSV export, activity log (Admin), login attempt limit
- Dark mode, responsive layout

## New in v3
- App shell with sidebar, top bar, notification bell, dark mode
- Favorites, recently viewed, bulk actions (status change or delete), advanced filters (category, date range)
- Add/Edit asset in a dialog, product page with health score, estimated value and timeline
- Maintenance scheduler and notification center (warranty and maintenance alerts)
- Printable report (Save as PDF from the browser), CSV export
- Tables are created automatically on first run; re-importing database/assetflow.sql is optional

## Not included yet
- QR codes and QR scanner, Three.js scenes (the 3D look uses CSS 3D transforms so it works offline)

## Optional
Put a short muted video at public/videos/hero.mp4 to play it behind the home page cover.
Database settings: config/database.php (default root / empty password).
