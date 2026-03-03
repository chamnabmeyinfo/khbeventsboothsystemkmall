# How to configure the SFTP extension in Cursor / VS Code

Use this to sync your Laravel project with the server (e.g. system.khbevents.com) from inside the IDE.

---

## 1. Install the extension

1. Open **Extensions** (Ctrl+Shift+X).
2. Search for **SFTP**.
3. Install **"SFTP"** by Natizyskunk (extension ID: `liximomo.sftp`).

---

## 2. Create the config file

From your **project root** (e.g. `khbeventsboothsystemkmall`):

```bash
mkdir -p .vscode
cp sftp.json.example .vscode/sftp.json
```

Then open **`.vscode/sftp.json`** in Cursor.

---

## 3. Edit `.vscode/sftp.json`

Replace the placeholders with your real values:

| Placeholder | Replace with |
|-------------|--------------|
| `FTP_OR_SFTP_HOST` | Your server hostname (e.g. `system.khbevents.com` or the host’s SFTP host) |
| `YOUR_CPANEL_OR_FTP_USERNAME` | cPanel username or FTP account username |
| `YOUR_FTP_OR_CPANEL_PASSWORD` | cPanel or FTP account password |

**Example** (with fake values):

```json
{
  "name": "system.khbevents.com",
  "host": "system.khbevents.com",
  "protocol": "sftp",
  "port": 22,
  "username": "khbevents",
  "password": "yourActualPassword",
  "remotePath": "/home/khbevents/system.khbevents.com",
  "uploadOnSave": false,
  "downloadOnOpen": false,
  "ignore": [".vscode", ".git", "node_modules", "vendor", ".env", "chamnabmey_documents", "*.log", ".cursor"],
  "watcher": { "files": "**/*", "autoUpload": false, "autoDelete": false }
}
```

- **remotePath**: Must match where the project lives on the server (e.g. `/home/khbevents/system.khbevents.com`). Get this from cPanel or your host.
- **uploadOnSave**: `false` = upload only when you run a command. Set to `true` only if you want every save to upload (use with care).

### Using an SSH key instead of password

1. Remove the `"password"` line from `sftp.json`.
2. Add your private key path, for example:
   - Linux/Mac: `"privateKeyPath": "/home/YOUR_USERNAME/.ssh/id_rsa"`
   - Windows: `"privateKeyPath": "C:\\Users\\YOUR_USERNAME\\.ssh\\id_rsa"`

---

## 4. Use the extension

1. **Command Palette**: Ctrl+Shift+P (Cmd+Shift+P on Mac).
2. Run one of:
   - **SFTP: Upload Project** – upload whole project (respects `ignore` list).
   - **SFTP: Download Project** – download from server to local.
   - **SFTP: Sync Local -> Remote** – upload only changed files.
   - **SFTP: Sync Remote -> Local** – download only changed files.
3. To upload a **single file or folder**: right‑click it in the Explorer → **SFTP: Upload** (or **Upload Folder**).

---

## 5. Where to get host / username / password

- **cPanel** → **FTP Accounts** (or “FTP”): host, username; create an FTP account if you want a separate user.
- **cPanel** → **SSH Access**: check if SFTP/SSH is enabled; host and port are often the same as for SFTP.
- Use **SFTP** (port 22) rather than plain FTP when the host supports it.

---

## 6. Safety (don’t overwrite server by mistake)

- **Do not** upload your local `.env` over the server’s `.env` (the config’s `ignore` list already excludes `.env`).
- **Do not** upload `vendor/` or `node_modules/`; run `composer install` and `npm install` on the server instead.
- Keep **`.vscode/sftp.json`** out of Git (it’s in `.gitignore`). Never commit real passwords or key paths.

---

**See also:** `CURSOR-REMOTE-ACCESS.txt` (SSH option), `docs/04-deployment/CPANEL-REMOTE-FILE-MANAGER.md` (cPanel and paths).
