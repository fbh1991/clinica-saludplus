# Clínica SaludPlus

Aplicación web formativa para la gestión de una clínica médica, desarrollada con PHP, MySQL, HTML5, CSS3 y JavaScript.

## Funcionalidades

- Registro e inicio de sesión de usuarios.
- Gestión de perfiles.
- Solicitud y administración de citas.
- Publicación y consulta de noticias.
- Panel de administración.
- Gestión de usuarios y roles.

## Tecnologías

- PHP 8
- MySQL / MariaDB
- HTML5, CSS3 y JavaScript
- PDO para acceso a datos

## Instalación local con XAMPP

1. Instala XAMPP y copia el proyecto dentro de `htdocs`.
2. Crea la base de datos `clinical_fbh`.
3. Importa el archivo SQL incluido en el repositorio.
4. Copia `includes/.env.example.php` como `includes/.env.php`.
5. Edita `includes/.env.php` con los datos de conexión de tu entorno local.
6. Inicia Apache y MySQL desde el panel de XAMPP.
7. Abre el proyecto desde `http://localhost/clinica_saludplus_fbh/` (ajusta la ruta al nombre de tu carpeta).

## Configuración de base de datos

El archivo privado `includes/.env.php` contiene únicamente la configuración local y está excluido de Git. También se pueden configurar las variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`.

Nunca publiques contraseñas, tokens ni credenciales reales.

## Seguridad y alcance

- Las contraseñas se almacenan mediante hash con `password_hash()` y se verifican con `password_verify()`.
- El acceso a datos utiliza PDO y consultas preparadas en las operaciones implementadas.
- El identificador de sesión se regenera al iniciar sesión.
- La aplicación es un proyecto formativo, no un producto clínico listo para tratar datos médicos reales.
- Antes de cualquier despliegue real, deben completarse pruebas y revisiones adicionales de autorización, protección CSRF, validación de archivos subidos, tratamiento de errores y protección de datos.

## Autor

Fernando Barajas
