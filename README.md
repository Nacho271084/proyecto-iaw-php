# Proyecto IAW - Gestor de Incidencias

Este proyecto consiste en una aplicación web desarrollada en PHP nativo (sin frameworks) para la gestión de tickets de soporte técnico (Helpdesk). Permite a los usuarios autenticados crear, listar, editar y eliminar incidencias de forma segura.

## Requisitos del sistema
Para ejecutar este proyecto es necesario contar con un entorno de servidor web (LAMP o WAMP) con los siguientes componentes:
- Apache (Servidor web)
- PHP 8.0 o superior (con el módulo `pdo_mysql` habilitado)
- MySQL o MariaDB (Base de datos)

## Pasos para la instalación
1. Clona este repositorio o descarga los archivos en tu equipo.
2. Mueve la carpeta `proyectoIAW` a tu directorio raíz de Apache (por ejemplo, `/var/www/html/` en Linux o `C:\wamp64\www\` en Windows).
3. Abre tu gestor de base de datos (phpMyAdmin) y crea una base de datos llamada `gestor_incidencias`.
4. Crea un usuario de MySQL llamado `iaw_user` con la contraseña `iaw_password` y otórgale todos los privilegios sobre la base de datos.
5. Importa los archivos SQL desde la terminal o desde phpMyAdmin en este orden:
   ```bash
   mysql -u iaw_user -p gestor_incidencias < sql/schema.sql
   mysql -u iaw_user -p gestor_incidencias < sql/seed.sql
---
