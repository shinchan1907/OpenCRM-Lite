# OpenCRM Lite

**Version:** MVP V1.0

OpenCRM Lite is a fast, modular, and self-hosted CRM designed for freelancers, solopreneurs, and small agencies. Built with PHP (no heavy frameworks), it is easy to deploy on any shared or local PHP server. The system is highly extensible, featuring a WordPress-style plugin and theme system, REST API, and a clean UI powered by Tailwind CSS.

---

## 📁 File Hierarchy

```
opencrm-lite/
│
├── index.php                # Entry point, bootstraps everything
├── .htaccess                # Clean URLs, security rules
├── config.php               # DB, site, API config
├── router.php               # Simple router (maps URLs to controllers)
│
├── core/                    # Core logic (never edit for plugins/themes)
│   ├── init.php             # Loads config, plugins, modules, themes
│   ├── db.php               # DB connection (PDO, supports SQLite/MySQL)
│   ├── auth.php             # Auth/session/token logic
│   ├── api.php              # REST API entrypoint
│   ├── webhook.php          # Webhook registration/dispatch
│   ├── functions.php        # Shared helpers (sanitize, redirect, etc.)
│   └── hooks.php            # Plugin system (add_action, do_action)
│
├── modules/                 # Core modules (clients, tasks, invoices, etc.)
│   ├── clients/
│   ├── tasks/
│   ├── invoices/
│   ├── dashboard/
│   └── users/
│
├── views/                   # Default view templates (can be overridden by themes)
│   ├── layout.php
│   ├── login.php
│   ├── dashboard.php
│   └── ...
│
├── themes/                  # Theme folders (override views, add CSS/JS)
│   └── modern-light/
│       ├── theme.json
│       ├── header.php
│       ├── footer.php
│       ├── style.css
│       └── dashboard.php
│
├── plugins/                 # Drop-in plugins (WordPress style)
│   └── whatsapp-chat/
│       ├── plugin.json
│       ├── init.php
│       └── assets/
│
├── assets/                  # Static files (global CSS, JS, icons)
│   ├── css/
│   ├── js/
│   └── icons/
│
├── storage/                 # Uploads, logs, cache (gitignored)
│
└── README.md                # Docs, contribution guide, etc.
```

---

## 🚀 Features (MVP V1.0)

- **Fast, lightweight, and modular**
- **PHP-based** (no heavy frameworks)
- **SQLite or MySQL** support
- **Clean UI** with Tailwind CSS
- **REST API** with token-based authentication (for n8n, Zapier, Make, etc.)
- **WordPress-style plugin system** (`add_action()`, `do_action()`)
- **Theme system** (override default views, add custom CSS/JS)
- **Core modules:**
  - Client management
  - Task tracking
  - Invoice system (with PDF export)
  - Dashboard with KPIs
- **Authentication system** with Admin/Staff roles
- **Extensible:**
  - Webhooks
  - Email/SMS plugins
  - Lead capture forms

---

## 🛠️ Customization & Extensibility

### Plugins
- Drop plugin folders into `/plugins/`.
- Each plugin has a `plugin.json` (meta) and `init.php` (registers hooks).
- Use hooks (`add_action`, `do_action`) to extend or modify core behavior.
- Never modify core files directly.

### Themes
- Drop theme folders into `/themes/`.
- Each theme has a `theme.json` and can override any view by copying it from `/views/`.
- Include custom CSS/JS in the theme folder.
- Fallback to default views if not overridden.

### Modules
- Core features are organized as modules in `/modules/`.
- Add new modules for additional features.

---

## ⚡ Installation

1. **Requirements:**
   - PHP 7.4+
   - SQLite or MySQL
   - Web server (Apache, Nginx, or PHP built-in)

2. **Download & Extract:**
   - Clone or download the repository:
     ```
     git clone https://github.com/yourusername/opencrm-lite.git
     ```
   - Or download and extract the ZIP.

3. **Configure:**
   - Copy `config.sample.php` to `config.php` and update DB/site settings.

4. **Set Permissions:**
   - Ensure `/storage/` is writable by the web server (for uploads, logs, cache).

5. **Run Installer:**
   - Visit your site in the browser and follow the setup instructions.

---

## 📚 Usage

- **Login:** Access `/login` with your admin credentials.
- **Dashboard:** View KPIs and quick stats.
- **Clients/Tasks/Invoices:** Use the navigation to manage core data.
- **API:** Use `/api/v1/` endpoints with token-based authentication for automation tools.
- **Plugins/Themes:** Add or update via the `/plugins/` and `/themes/` folders.

---

## 🧩 Developer Guide

- **Hooks:** Use `add_action()` and `do_action()` to extend functionality.
- **REST API:** Extend `/core/api.php` for new endpoints.
- **Views:** Override any view in your theme by copying from `/views/`.
- **Modules:** Add new modules in `/modules/` for new features.
- **Contributions:**
  - Fork, branch, and submit pull requests.
  - Follow coding standards and document your code.

---

## 🤝 Community & Contributions

- **Open Source:** MIT License
- **Contribute:** See [CONTRIBUTING.md] (coming soon)
- **Report Issues:** Use GitHub Issues
- **Feature Requests:** Open a discussion or issue

---

## 💡 Monetization (Future)
- GitHub Sponsors, OpenCollective, BuyMeACoffee
- Premium plugins/themes
- Hosted version for non-technical users
- Marketplace for third-party plugins/themes

---

## 📣 Credits
- Inspired by WordPress, InvoiceNinja, and other open-source CRMs
- UI powered by [Tailwind CSS](https://tailwindcss.com/)

---

**OpenCRM Lite** — Simple, fast, and extensible CRM for everyone!  
