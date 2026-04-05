# 🍗 Kitchen 86 — Restaurant Management MVP Specification

## 1. Project Overview
- **Name:** Kitchen 86 MVP
- **Type:** Full-stack single-page web app (SPA)
- **Primary Domain:** Restaurant Management & Point-of-Sale (Ordering) Features
- **Location Context:** Larkana, Pakistan (specializing in Cheezious Matka Fries & Fried Chicken)
- **Target Audience:** Local customers for online or pickup orders.

## 2. Tech Stack & Architecture
- **Frontend Stack:** HTML5, Vanilla JavaScript (SPA hash routing), Tailwind CSS (via CDN)
- **Backend Stack:** PHP (RESTful JSON APIs, PDO, session management)
- **Database:** MySQL
- **Architecture Pattern:** Client-Side Rendered SPA interacting with stateful PHP APIs.

## 3. Brand Identity & Design System
- **Tone:** Bold, Hot, Fast, High-contrast dark theme.
- **Palette:** 
  - Brand Red: `#FF2020` (CTAs, accents)
  - Amber: `#FFB800` (Prices, badges)
  - Backgrounds: `#0A0A0A` (Base), `#161616` (Surface), `#222222` (Elevated)
  - Text: `#FFFFFF` (Primary), `#AAAAAA` (Muted)
- **Typography:**
  - Display: **Bebas Neue** (Logos/Numbers)
  - Headings: **Poppins** (Titles)
  - Body: **Barlow** (UI labels)
  - Numbers: **Barlow Condensed** (Prices/Qty)

## 4. Key Entities & Flows
- **Users:** Customers (Guests/Registered), Admin. 
- **Core Entities:** Menus, Categories, Deals, Orders, Order Items.
- **Main Flows:**
  - **Customer Flow:** Browse Menu -> Add to Cart (Local Storage) -> Checkout (Guest/User) -> Track Order.
  - **Admin Flow:** Manage Menu/Deals -> Process Orders -> View Stats (Dashboard).

## 5. Security Enforcements
- Password Hashing: bcrypt
- Database Access: PDO prepared statements strictly
- Authorization: Server-side session validation for all admin routes and API actions.
- Input: Strict validation and sanitization on all endpoints.
