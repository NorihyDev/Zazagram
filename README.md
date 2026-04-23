# Zazagram

A full-stack social media web app built with PHP, inspired by Instagram.

Users can post photos, add friends, like and comment, send messages, and more.

Built for fun. Dark UI. No frameworks.

---

## Features

- Register and login with hashed passwords

- Create posts with image upload and filters

- Like and comment on posts

- Friend requests and social feed

- Messaging system

- Notifications

- User profiles with bio and avatar

- Settings (edit profile, change password)

- Admin panel (ban users, manage content)

---

## Project Structure

Zazagram_Website/

├── api/           -> Backend API endpoints

├── assets/

│   ├── css/       -> Stylesheets

│   ├── js/        -> JavaScript

│   └── images/    -> Static images

├── data/          -> JSON database files

├── includes/      -> Shared PHP (auth, db, header, footer)

├── pages/         -> All main pages

├── uploads/       -> User uploaded images

├── config.php     -> App configuration

└── index.php      -> Entry point

---

## Installation (local)

1. Install XAMPP (https://www.apachefriends.org)

2. Clone this repo into C:/xampp/htdocs/

3. Start Apache in XAMPP

4. Open http://localhost/Zazagram_Website

---

## Tech Stack

- Backend  : PHP (no framework)

- Database : JSON files

- Frontend : HTML, CSS, Vanilla JS

- Auth     : PHP sessions + bcrypt

---

## Author

Made by Norihy