# Push Notifications – Configuration Guide

This guide explains how to configure the system so **browser push notifications** work (Web Push with VAPID).

---

## 1. Generate VAPID keys

You need a **public** and **private** key pair. The app can generate them for you.

### Option A: Using Artisan (recommended)

From the project root:

```bash
php artisan webpush:vapid
```

Copy the **Public key** and **Private key** from the output.

To print keys as `.env` lines:

```bash
php artisan webpush:vapid --dotenv
```

### Option B: Using Node.js

If you have Node.js installed:

```bash
npx web-push generate-vapid-keys
```

Use the printed **Public Key** and **Private Key**.

### Option C: OpenSSL manually

```bash
openssl ecparam -genkey -name prime256v1 -out vapid_key.pem
openssl ec -in vapid_key.pem -pubout -outform DER | tail -c 65 | base64 | tr -d '=' | tr '/+' '_-'   # public
openssl ec -in vapid_key.pem -outform DER | tail -c +8 | head -c 32 | base64 | tr -d '=' | tr '/+' '_-'   # private
```

Use the first line output as public key, second as private key. Then remove `vapid_key.pem`.

---

## 2. Configure the keys

You can set keys in **.env** and/or in **Settings** in the UI. The private key must **only** be in `.env` (never in the database).

### 2a. Using .env

Edit `.env` and add (use your generated values):

```env
# Push Notifications (Web Push)
PUSH_NOTIFICATIONS_ENABLED=true
PUSH_VAPID_PUBLIC_KEY=your_public_key_here
PUSH_VAPID_PRIVATE_KEY=your_private_key_here
```

- Replace `your_public_key_here` with the **public** VAPID key.
- Replace `your_private_key_here` with the **private** VAPID key.
- Do **not** commit `.env` or share the private key.

Then clear config cache:

```bash
php artisan config:clear
```

### 2b. Using Settings in the UI

1. Log in as a user with **Manage Settings** permission.
2. Go to **Settings**.
3. Find the **Push Notifications** section.
4. Check **Enable push notifications**.
5. (Optional) Paste the **VAPID public key** into **VAPID public key** and click **Save Push Notification Settings**.

If you set the public key here, it is stored in the database and used even when `PUSH_VAPID_PUBLIC_KEY` is not in `.env`. The **private key** must still be set only in `.env` as `PUSH_VAPID_PRIVATE_KEY`.

---

## 3. Verify configuration

- **Enable/disable:** Controlled by Settings (DB) or `PUSH_NOTIFICATIONS_ENABLED` in `.env` (default `true`).
- **Public key:** From Settings (DB) or `PUSH_VAPID_PUBLIC_KEY` in `.env`. Required for the browser to subscribe.
- **Private key:** Only `PUSH_VAPID_PRIVATE_KEY` in `.env`. Required on the server to send push messages.

After changing `.env`, run:

```bash
php artisan config:clear
```

---

## 4. What is already in place

- **Settings:** Enable/disable and VAPID public key (Settings > Push Notifications).
- **Config:** `config/notifications.php` reads `PUSH_*` from `.env`.
- **Routes:** `GET/POST /settings/push-notifications` for loading/saving push settings.

---

## 5. What is needed for push to actually be delivered

For users to **receive** push notifications when something happens (e.g. new booking, booth update), the app still needs:

1. **Push subscription storage** – A table (e.g. `push_subscriptions`) to store each user’s browser subscription (endpoint + keys) when they click “Allow” in the browser.
2. **Subscribe endpoint** – An API that the frontend calls to save the subscription (e.g. `POST /notifications/push-subscribe`) after `PushManager.subscribe()`.
3. **Sending push** – When creating an in-app notification (e.g. in `NotificationService`), call a Web Push library (e.g. `minishlink/web-push` or `laravel-notification-channels/webpush`) to send to all stored subscriptions for that user, using the VAPID private key from config.
4. **Frontend** – Request notification permission, subscribe with the VAPID public key, and send the subscription to your subscribe endpoint.
5. **Service worker** – A service worker (e.g. `public/sw.js`) that listens for `push` events and shows the notification with `registration.showNotification()`.

Once the above is implemented, the configuration in this guide (VAPID keys and “Enable push notifications”) is what makes push notification **work** end-to-end.

---

## 6. How to test and see it work

1. **Configure VAPID keys** (see sections 1–2). Ensure `.env` has `PUSH_VAPID_PUBLIC_KEY` and `PUSH_VAPID_PRIVATE_KEY`; run `php artisan config:clear`.
2. **Run migrations**: `php artisan migrate`. If `push_subscriptions` already exists (e.g. from a failed run), drop it in tinker then migrate again.
3. **Open the app** in Chrome/Firefox/Edge, log in, go to **Notifications**.
4. Click **Enable push notifications** and choose **Allow** when the browser asks.
5. Click **Send test push** — you should get a system notification.
6. Trigger a real event (e.g. create a booking) to see push for in-app notifications.

Push requires **HTTPS** (or localhost). If "Send test push" says "No push subscription", enable push first (step 4).

---

## 7. Troubleshooting

| Issue | What to check |
|-------|----------------|
| “Enable push notifications” has no effect | Ensure `push_notifications_enabled` is saved in Settings and, if you use `.env`, that `PUSH_NOTIFICATIONS_ENABLED` is set. Run `php artisan config:clear`. |
| Public key not found | Set **VAPID public key** in Settings (Push Notifications) or `PUSH_VAPID_PUBLIC_KEY` in `.env`. |
| Push not sent from server | Private key must be in `.env` as `PUSH_VAPID_PRIVATE_KEY`. No code should read the private key from the database. |
| Keys invalid / push fails | Regenerate with `php artisan webpush:vapid` and update both public and private key in `.env` (and public in Settings if you use it). |
