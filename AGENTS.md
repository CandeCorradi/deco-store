# AGENTS.md - DecoStore PHP Repository

## Project Overview

**Legacy PHP CRUD application** — no modern framework, no build tools, no package manager.
- **Language**: PHP 7.x/8.x with MySQLi extension
- **Database**: MySQL `d26com_db_cande` on `localhost`, user `root`, no password
- **Entry point**: `index.php?page=<name>` — switches on `$_GET['page']` with 70+ cases
- **Frontend**: CDN-only (Bootstrap 5.3, jQuery 3.7, FontAwesome 6, DataTables 1.13, SweetAlert2 11). No local asset pipeline.

## Critical: No Build / Test / Lint Infrastructure

- **No `package.json`**, **no `composer.json`**, **no PHPUnit**, **no linters**
- Do **not** attempt `npm install`, `npm run`, `composer`, or `php vendor/...`
- Verification is manual: load pages in a browser at `http://localhost/deco_store/`
- If you see "Command not found" for npm/composer, this is expected

## Development Workflow

1. **Start the server**: Place files in WAMP/XAMPP `www/` and visit `http://localhost/deco_store/`
2. **Auth required**: `index.php` redirects to `login/index.php` if `{$_SESSION['id_usuarios']}` is not set
3. **Module pattern**: Each module in `modulos/` sets `$titulo_modulo`, `$icono_modulo`, `$campo_id`, `$tabla`, queries DB, then includes `componentes/tabla_generica.php`
4. **Adding a new module**: Follow the pattern in existing `modulos/*_listar.php` files — set the four vars, run a SELECT, include `componentes/tabla_generica.php`

## Key Conventions & Quirks

- **Auth auto-migration**: `login/procesar_login.php` checks `password_verify()` first, then falls back to plain-text password comparison. If plain-text matches, it automatically hashes the password in the DB (`UPDATE usuarios SET contrasenia = password_hash(...)`)
- **Inline double-click edit**: `.editable` cells in any module table support double-click-to-edit. Saves to `php/actualizar_inline_universal.php` via AJAX. Pattern: `id_{tabla}` as primary key. Special rule: for `caja` table `importe` column, sign enforces existing balance (negative = egreso, positive = ingreso)
- **Global search**: `php/buscador_global.php` — AJAX endpoint for the live search on `panel_inicio.php`. Returns up to 5 results each for articulos, clientes, ventas
- **Excel exports**: 
  - `exportar_caja_excel.php` — daily caja ? .xls with BOM, joined with `cuentas` table
  - `exportar_venta_excel.php` — specific sale by `?id=` ? .csv with `;` delimiter, BOM for UTF-8
- **Deleted features**: `metodos_pago/` and standalone `provincias/` CRUD files were removed in a refactor. The generic `modulos/` + `componentes/tabla_generica.php` pattern now handles these features.
- **Menu navigation**: `componentes/menu.php` produces both topbar and offcanvas sidebar. Includes "DECOSTORE" brand + logout.
- **Database charset**: `php/conexion.php` sets `mysqli_set_charset($conexion, "utf8")` — acentos and Ñ depend on this

## Commands Cheat Sheet

| Action | Command |
|--------|---------|
| Browse all modules | `http://localhost/deco_store/index.php?page=articulos` |
| Login page | `http://localhost/deco_store/login/index.php` |
| Add new articulo | From articulos list: "Nuevo Registro" button ? `articulos_agregar.php` |
| Inline edit any cell | Double-click any `.editable` cell |
| Export caja daily | `index.php?page=caja` ? exportar_caja_excel.php link |
| Export specific venta | `index.php?page=ventas_detalle&id=123` ? export link |

## What to Avoid

- Do **not** look for `composer.json`, `package.json`, `phpunit.xml`, `.eslintrc`, or any config files — they don't exist
- Do **not** modify `php/conexion.php` charset unless you understand the local DB collation
- Do **not** assume any testing framework exists — there are no test files or CI pipelines
- Do **not** try to run any npm / yarn / composer commands in this directory
