# cPanel: Remote access to File Manager

This guide covers configuring cPanel so you can access and manage files remotely (from Cursor or any FTP/SFTP client), and where the File Manager lives on the server.

**Project path on server:** `/home/khbevents/system.khbevents.com`  
**Document root (web):** `/home/khbevents/system.khbevents.com/public`

---

## 1. In cPanel: enable remote file access

### 1.1 FTP / SFTP (for Cursor SFTP extension or FileZilla)

1. Log in to **cPanel** (e.g. `https://system.khbevents.com:2083` or your host’s cPanel URL).
2. Open **FTP Accounts** (under “Files”).
3. **Option A – Use main cPanel login**
   - Your FTP/SFTP host is usually your domain (e.g. `system.khbevents.com`) or the host’s FTP hostname.
   - Username: your **cPanel username**.
   - Password: your **cPanel password**.
4. **Option B – Create a dedicated FTP account**
   - Click **Add FTP Account**.
   - Set username, password, and directory (e.g. `system.khbevents.com` so path is `/home/khbevents/system.khbevents.com`).
   - Note the “FTP account username” (often `user@system.khbevents.com` or similar).
5. **SFTP:** Most cPanel hosts support SFTP on port **22** with the same username/password. Use protocol **SFTP** in your client.

### 1.2 SSH (for Cursor “Remote – SSH” and terminal on server)

1. In cPanel go to **SSH Access** (under “Security”).
2. If SSH is **disabled**, click **Manage SSH Keys** and/or **Enable SSH** (steps vary by host).
3. Note the **SSH host** (often your domain or something like `server123.hosting.com`) and **port** (usually **22**).
4. User is your **cPanel username**; password is your cPanel password (or use SSH key if you set one up).

---

## 2. Where is the “File Manager” in cPanel?

- In the cPanel UI: **Files → File Manager**.
- On disk, your account’s files are under `/home/khbevents/`. This project is in:
  - **Full path:** `/home/khbevents/system.khbevents.com`
  - **Web root:** `/home/khbevents/system.khbevents.com/public`

When you connect via SFTP or SSH, you are effectively using the same files as the cPanel File Manager; you just access them from Cursor or another client instead of the browser.

---

## 3. Connect Cursor to cPanel (remote file manager)

Use one of these; both give you “remote file manager” style access from Cursor.

### Option A – SSH (recommended if available)

- Follow **CURSOR-REMOTE-ACCESS.txt** in the project root (Remote – SSH extension, SSH config, connect to host, open folder `/home/khbevents/system.khbevents.com`).
- You edit files on the server directly and can run `php artisan`, `composer`, etc. in the terminal.

### Option B – SFTP (browser-style file manager in Cursor)

1. Copy the example config:
   ```bash
   mkdir -p .vscode
   cp sftp.json.example .vscode/sftp.json
   ```
2. Edit **`.vscode/sftp.json`** and set:
   - **host:** Your FTP/SFTP host (e.g. `system.khbevents.com`).
   - **username:** cPanel username or FTP account username.
   - **password:** cPanel or FTP account password.
   - **remotePath:** `/home/khbevents/system.khbevents.com`
3. Install an SFTP extension (e.g. “SFTP” by Natizyskunk).
4. Use **SFTP: Upload / Download / Sync** from the command palette to mirror local ↔ remote (same as managing files via cPanel File Manager, but from Cursor).

Details and safety notes (e.g. not overwriting `.env`, ignoring `vendor/`, `node_modules/`) are in **CURSOR-REMOTE-ACCESS.txt**.

---

## 4. Summary

| Goal                         | Where to configure        | What to use in Cursor / client   |
|-----------------------------|---------------------------|-----------------------------------|
| Use cPanel File Manager     | Browser → cPanel → Files  | N/A (use cPanel in browser)       |
| Edit server files in Cursor | cPanel: SSH or FTP/SFTP   | Remote – SSH or SFTP extension    |
| Same files as File Manager  | Path above                | `/home/khbevents/system.khbevents.com` |

Once FTP/SFTP or SSH is enabled in cPanel and Cursor is configured as above, you have “cPanel remote to file manager” set up: remote access to the same files you see in cPanel File Manager.
