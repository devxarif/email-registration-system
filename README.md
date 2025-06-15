# 📬 Registration API

This application based REST API allows users to register using their email address. Upon successful registration, a **welcome email is sent using the Gmail API** — without blocking the registration process (email is sent asynchronously using Laravel jobs/queues).

---

## ✅ Features

- ✅ RESTful `POST /register` endpoint
- ✅ Email saved in PostgreSQL
- ✅ Welcome email sent using Gmail API
- ✅ Asynchronous (non-blocking) email delivery
- ✅ Gmail OAuth2 flow with token persistence
- ✅ Dockerized for simple local development

---

## 🚀 Setup Instructions

```bash
git clone git@github.com:devxarif/email-registration-system.git registration-api
cd registration-api

cp .env.example .env
composer install
php artisan key:generate

docker-compose up --build
```

## 📧 Usage
After setting up the API, you can register a user by sending a POST request to the `/register` endpoint with the following JSON body:

```json
{
  "email": "arif.fullstackdev@gmail.com"
}
```

## 🛠️ Requirements
- PHP 8.3+
- Composer
- PostgreSQL (Optional: Docker for local development)
- Docker (for containerization)
