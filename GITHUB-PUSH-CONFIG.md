# Push to GitHub Using a Personal Access Token (PAT)

This guide explains **where and how** to configure Git so you can push to GitHub using a **Personal Access Token** on Windows. Your token is **never** stored in this repo.

---

## 1. Create a GitHub token

1. Open **GitHub** → your profile (top right) → **Settings**.
2. In the left sidebar, go to **Developer settings** → **Personal access tokens** → **Tokens (classic)**.
3. Click **Generate new token (classic)**.
4. Set:
   - **Note:** e.g. `KHB Booth System - push from PC`
   - **Expiration:** e.g. 90 days or No expiration (your choice).
   - **Scopes:** enable **repo** (full control of private repositories).
5. Click **Generate token**, then **copy the token once** (you won’t see it again).

**Important:** Do not paste this token into any file in this project. Do not commit it.

---

## 2. Where to configure Git (on your machine)

Configuration is done in **your user profile**, not in the project folder:

| What                                | Where                                           |
| ----------------------------------- | ----------------------------------------------- |
| Git credential helper (recommended) | **Windows Credential Manager** (via Git config) |
| Optional: Git user name/email       | `git config --global user.name` / `user.email`  |

---

## 3. Recommended: Use Windows to store the token (one-time sign-in)

This way you push with `git push origin main` and Git uses the stored credentials.

### Step 1: Set credential helper (once per machine)

In **PowerShell** or **Command Prompt** (any folder), run:

```powershell
git config --global credential.helper manager
```

On some Windows setups the helper is named `wincred`:

```powershell
git config --global credential.helper wincred
```

### Step 2: Trigger a push so Git asks for credentials

In your project folder:

```powershell
cd c:\xampp\htdocs\KHB\khbevents\boothsystemv1
git push origin main
```

When prompted:

- **Username:** your GitHub username (e.g. `chamnabmeyinfo`).
- **Password:** paste your **Personal Access Token** (not your GitHub account password).

Git will store these in **Windows Credential Manager**. Later pushes will not ask again until the token expires or you remove the stored credential.

### Step 3: Check stored credential (optional)

- Press **Windows key** → type **Credential Manager** → open **Windows Credential Manager**.
- **Windows Credentials** → look for an entry like `git:https://github.com`. That is where the token is stored.

---

## 4. Your current remote

This repo is already set to push to:

- **URL:** `https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git`
- **Branch:** `main`

No need to change the remote URL if you use the credential helper above.

---

## 5. If you prefer not to use the credential helper (one-time push)

You can embed the token in the URL for a single push. **Use only on a private machine and consider revoking the token after.**

```powershell
git push https://YOUR_USERNAME:YOUR_TOKEN@github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git main
```

Replace `YOUR_USERNAME` and `YOUR_TOKEN`. Do **not** save this URL in the repo or in a committed config file.

---

## 6. Troubleshooting

| Issue                                             | What to do                                                                                                 |
| ------------------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| “Authentication failed”                           | Use the **token** as password, not your GitHub account password. Check token has **repo** scope.           |
| “Support for password authentication was removed” | You must use a **Personal Access Token** (or SSH). Passwords no longer work for Git over HTTPS.            |
| Token expired                                     | Create a new token in GitHub, then run `git push origin main` again and enter the new token when prompted. |
| Clear stored credential                           | Windows Credential Manager → Windows Credentials → find `git:https://github.com` → Remove.                 |

---

## Summary

- **Create token:** GitHub → Settings → Developer settings → Personal access tokens.
- **Configure push:** Use `credential.helper` (e.g. `manager` or `wincred`) so the token is stored in **Windows Credential Manager**.
- **Where:** Configuration is in your **user-level Git config** and Windows Credential Manager, **not** in this project folder.
- **Security:** Never put the token in `.env`, config files, or any file committed to the repo.

After following steps 1–3, run `git push origin main` from the project folder to push using your token.
