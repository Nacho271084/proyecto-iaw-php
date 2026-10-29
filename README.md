# Proyecto IAW - Gestor de Incidencias

Aplicación web desarrollada en PHP (sin frameworks) para la gestión de incidencias (Helpdesk). 

## 🚀 Requisitos
- Apache, MySQL y PHP (LAMP/WAMP).
- Habilitar el módulo `pdo_mysql` en PHP.

## 🛠️ Instalación
1. Clona este repositorio o descarga los archivos.
2. Mueve la carpeta a tu directorio raíz de Apache (`/var/www/html/` o `C:\wamp64\www\`).
3. Crea una base de datos en MySQL llamada `gestor_incidencias`.
4. Crea un usuario `iaw_user` con contraseña `iaw_password` y dale permisos sobre la base de datos.
5. Importa los archivos SQL en este orden:
   ```bash
   mysql -u iaw_user -p gestor_incidencias < sql/schema.sql
   mysql -u iaw_user -p gestor_incidencias < sql/seed.sql