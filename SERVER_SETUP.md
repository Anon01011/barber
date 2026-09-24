# Server & CI/CD Setup Guide

This guide explains how to configure your server and GitHub repository to enable the automated CI/CD pipeline.

## 1. Server Prerequisites

Ensure your server (e.g., DigitalOcean Droplet, AWS EC2, or VPS) has the following installed:

- **PHP 8.2** (with extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`)
- **Composer**
- **Node.js & NPM** (Version 18+)
- **Git**
- **Nginx/Apache** (Configured to serve the site)
- **MySQL/MariaDB**

## 2. Initial Project Setup on Server

### Standard Setup
1.  **SSH into your server**: `ssh username@your-server-ip`
2.  **Navigate to web root**: `cd /var/www`
3.  **Clone repo**: `git clone https://github.com/your-username/salon-cms.git`

### aaPanel Specific Setup
1.  **Create Website**: Go to "Website" > "Add Site".
    -   Domain: `salon.fsterp.com`
    -   Database: Create a MySQL database.
    -   PHP Version: **PHP-82**
2.  **SSH into server**: Use the terminal in aaPanel or your local terminal.
3.  **Navigate to site directory**:
    ```bash
    cd /www/wwwroot/salon.fsterp.com
    ```
4.  **Clone/Upload Code**:
    -   If the directory is empty, clone: `git clone https://github.com/your-username/salon-cms.git .`
    -   **Important**: Ensure the contents are in the root of the site folder, not a subfolder.
5.  **Set Website Directory**:
    -   In aaPanel Website settings, go to "Site Directory".
    -   Set "Running directory" to `/public`.
    -   Save.
6.  **Configure URL Rewrite**:
    -   In Website settings, go to "URL rewrite" and select `laravel5`.
    -   Save.

### Common Steps (All Setups)
1.  **Set Permissions**:
    ```bash
    chown -R www:www .  # For aaPanel (user is usually 'www')
    # OR
    chown -R www-data:www-data . # For standard Ubuntu/Nginx

    chmod -R 775 storage bootstrap/cache
    chmod +x deploy.sh
    ```

2.  **Environment Configuration**:
    -   `cp .env.example .env`
    -   Edit `.env` with your database details.
    -   `php artisan key:generate`

## 3. GitHub Secrets Configuration

To allow GitHub Actions to access your server, you need to add secrets to your repository.

1.  Go to your GitHub Repository.
2.  Click on **Settings** > **Secrets and variables** > **Actions**.
3.  Click **New repository secret** and add the following:

| Name | Value |
|------|-------|
| `HOST` | Your server's IP address |
| `USERNAME` | SSH Username (e.g., `root`) |
| `PORT` | SSH Port (default `22`) |
| `SSH_KEY` | Your **Private SSH Key**. Ensure the public key is in `~/.ssh/authorized_keys` on the server. |

## 4. Deployment Configuration

### Updating `deploy.sh` for aaPanel
aaPanel often uses specific paths for PHP. You might need to edit `deploy.sh` on the server to point to the correct PHP binary.

Open `deploy.sh` and find:
```bash
PHP_CMD=${PHP_BINARY:-php}
```

Change it to:
```bash
PHP_CMD=/www/server/php/82/bin/php
```
(Adjust `82` if you are using a different PHP version).

### Updating GitHub Workflow
If your site path is different (e.g., `/www/wwwroot/your-domain.com`), you must update `.github/workflows/deploy.yml`.

Find:
```yaml
script: |
  cd /var/www/salon-cms
  bash deploy.sh
```

Change to:
```yaml
script: |
  cd /www/wwwroot/your-domain.com
  bash deploy.sh
```

## 5. Deployment

1.  Push changes to `main`.
2.  Check the "Actions" tab in GitHub.

## 6. Transitioning from Manual Deployment

If you have already deployed the site manually (e.g., via FTP or a manual upload), you need to prepare the server folder so the automation can take over.

### Option A: You used `git clone` originally
If you originally set up the site using `git clone`:

1.  **SSH into your server**.
2.  **Navigate to the folder**: `cd /www/wwwroot/salon.fsterp.com`
3.  **Discard local changes** (Important: this makes your server match the repository exactly):
    ```bash
    git fetch --all
    git reset --hard origin/main
    ```
4.  **Ensure Permissions**:
    ```bash
    chown -R www:www .
    chmod +x deploy.sh
    ```

### Option B: You uploaded files manually (FTP/Zip)
If you just uploaded files and there is no `.git` folder on your server:

1.  **SSH into your server**.
2.  **Backup your configuration and uploads**:
    ```bash
    cp /www/wwwroot/salon.fsterp.com/.env /www/wwwroot/env_backup
    # If you have uploads in storage/app/public, back them up too
    ```
3.  **Re-setup as a Git Repo** (Safest method):
    -   Delete the contents (except `.env` if you didn't back it up, but backing up is safer).
    -   Ideally, rename the old folder to keep it safe:
        ```bash
        mv /www/wwwroot/salon.fsterp.com /www/wwwroot/salon.fsterp.com_old
        ```
    -   Create a new folder:
        ```bash
        mkdir /www/wwwroot/salon.fsterp.com
        ```
    -   Clone the repo:
        ```bash
        git clone https://github.com/your-username/salon-cms.git /www/wwwroot/salon.fsterp.com
        ```
4.  **Restore Configuration**:
    -   Copy your `.env` file back:
        ```bash
        cp /www/wwwroot/env_backup /www/wwwroot/salon.fsterp.com/.env
        ```
    -   Copy your `storage` folder content back if needed.
5.  **Set Permissions**:
    ```bash
    cd /www/wwwroot/salon.fsterp.com
    chown -R www:www .
    chmod -R 775 storage bootstrap/cache
    chmod +x deploy.sh
    ```

Once you have done this **one time**, the GitHub Actions pipeline will handle all future updates automatically.
