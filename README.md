🏪 DecoStore - Sistema de Gestión Comercial

Sistema web integral de gestión comercial y punto de venta (POS) diseñado para administrar de manera ágil el inventario, las ventas, la caja diaria y las cuentas corrientes de clientes.

🚀 Características Principales

📦 Gestión de Artículos y Categorías: Alta, baja y modificación (ABM) de productos, edición rápida en línea (inline), control de stock y clasificación por categorías.

🛒 Módulo de Ventas y Facturación: Registro ágil de ventas con emisión de comprobantes listos para imprimir (factura_imprimir.php) y exportación de reportes a Excel.

👥 Clientes y Cuentas Corrientes: Administración de cartera de clientes con seguimiento detallado de saldos, movimientos y estado de cuentas corrientes.

💰 Control de Caja: Apertura, seguimiento de movimientos diarios, cierre de caja y exportación de reportes financieros a Excel.

🔍 Buscador Global Inteligente: Búsqueda rápida en tiempo real desde cualquier pantalla para localizar artículos, clientes o comprobantes de venta al instante.

🛠️ Tecnologías Utilizadas

Backend: PHP (arquitectura modular)

Base de Datos: MySQL / MariaDB

Frontend: HTML5, CSS3, JavaScript

Entorno Local Recomendado: XAMPP / Laragon / WAMP

⚙️ Instalación y Configuración Local

Seguí estos pasos para ejecutar el proyecto en tu entorno local:

Clonar el repositorio:

git clone https://github.com/tu-usuario/deco-store.git


Mover al servidor local:
Colocá la carpeta del proyecto dentro del directorio raíz de tu servidor web (por ejemplo, C:\xampp\htdocs\deco_store si usás XAMPP).

Configurar la Base de Datos:

Abrí phpMyAdmin (o tu gestor de base de datos preferido).

Creá una nueva base de datos llamada sistemagestion (o el nombre configurado en tu archivo de conexión).

Importá el archivo .sql del proyecto para generar las tablas y estructura inicial.

Verificar credenciales de conexión:
Revisá el archivo de conexión a la base de datos en el proyecto y asegurate de que el usuario, contraseña y nombre de la base coincidan con tu entorno local.

Ejecutar el sistema:
Abrí tu navegador e ingresá a:

http://localhost/deco_store


📂 Estructura General del Proyecto

deco_store/
├── articulos/            # ABM y edición rápida de productos
├── caja/                 # Control de movimientos y cierres de caja
├── categorias/           # Gestión de categorías de artículos
├── php/                  # Lógica de servidor y buscador global
├── index.php             # Punto de entrada y enrutamiento principal
├── panel_inicio.php      # Dashboard principal del sistema
└── ...                   # Módulos de ventas, facturación y exportación


✒️ Autor

Cande – Desarrollo y arquitectura del sistema
