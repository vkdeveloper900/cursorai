
# 🚀 Laravel Backend API

<p align="center">
  <img src="https://laravel.com/img/logomark.min.svg" width="120" alt="Laravel Logo">
</p>

<p align="center">
  <strong>A modern RESTful backend API built with Laravel for frontend integration and testing.</strong>
</p>

<p align="center">
  <a href="https://laravelbackendapi.alwaysdata.net/api"><strong>Live API Base URL</strong></a>
</p>

---

## 📌 Project Overview

This project is a **Laravel-based backend API** developed to support frontend applications such as **React, Vue, mobile apps, or external clients**.

The APIs are publicly deployed to allow **easy testing, integration, and collaboration** without requiring a local backend setup.

---

## 🌐 Live Deployment

* **Hosting Provider:** alwaysdata (Free plan)
* **Purpose:** Testing / Staging
* **Public API Base URL:**

```text
https://laravelbackendapi.alwaysdata.net/api
```

---

## 🛠 Tech Stack & Versions

| Technology                        | Version            |
| --------------------------------- | ------------------ |
| PHP                               | **8.2**            |
| Laravel Framework                 | **12.x**           |
| Laravel Sanctum                   | **4.x**            |
| Laravel Tinker                    | **2.10.1**         |
| Laravel Excel (maatwebsite/excel) | **3.1**            |
| Database                          | MySQL              |
| Server OS                         | Linux (alwaysdata) |

---

## 📂 Project Type

* Backend-only API project
* RESTful architecture
* JSON-based API responses
* Designed for frontend and third-party consumption

---

## 🔑 Key Features

* Laravel 12 modern architecture
* Clean API routing structure
* Controller & service-based logic
* Database migrations
* API authentication ready (Sanctum)
* Auto-deployment via GitHub Webhooks
* Publicly accessible APIs for testing

---

## 📁 API Structure (Example)

```text
/api
 ├── /auth
 ├── /users
 ├── /products
 └── /health
```

Example endpoint:

```http
GET /api/hello
```

Response:

```json
{
  "status": true,
  "message": "API working fine"
}
```

---

## 🚀 Deployment Workflow

This project uses **automatic deployment** with GitHub Webhooks:

```text
Local development
→ Git commit
→ Git push (main branch)
→ GitHub Webhook
→ Server auto-deploy
```

No manual server interaction is required after setup.

---

## 🧪 Usage for Frontend Developers

1. Use the provided **base API URL**
2. Call APIs using Axios / Fetch / Postman
3. Consume JSON responses
4. Authentication can be added later using Laravel Sanctum

---

## ⚠️ Free Hosting Notes (alwaysdata)

* Limited disk space
* Single main website per account
* Not intended for high-traffic production use

✅ Best suited for:

* API testing
* Staging
* Demos
* Frontend integration

---

## 🔐 Environment & Security

* `.env` file is not committed
* Secrets are managed on the server
* Database seeders are **not auto-executed** on deploy
* Webhook-based deployment is secured via secret key

---

## 📈 Planned Enhancements

* Authentication & authorization (Sanctum)
* Role & permission management
* Background jobs & queues
* API rate limiting
* Migration to VPS / production server if required

---

## 📄 License

This project is open-sourced under the **MIT License**.

---

### ✅ Current Status

**LIVE • STABLE • READY FOR API TESTING**

