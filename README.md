# Clínica SaludPlus

Aplicación web de gestión de una clínica ficticia, desarrollada como proyecto formativo para demostrar conocimientos de desarrollo web con PHP y MySQL.

> **Aviso:** SaludPlus es una demostración académica. No está diseñada ni certificada para almacenar datos médicos reales ni para utilizarse como sistema clínico en producción.

## Vista general

La aplicación permite crear cuentas, iniciar sesión, gestionar perfiles y citas, consultar noticias y administrar usuarios, citas y publicaciones desde un área privada.

### Funcionalidades

- Registro e inicio de sesión.
- Gestión de datos personales y cambio de contraseña.
- Solicitud, edición y eliminación de citas.
- Consulta pública de noticias.
- Panel de administración protegido por rol.
- Gestión administrativa de usuarios, citas y noticias.
- Publicación de noticias con imágenes.

## Tecnologías

| Área | Tecnología |
|---|---|
| Interfaz | HTML5, CSS3 y JavaScript |
| Lógica de servidor | PHP 8 |
| Base de datos | MySQL / MariaDB |
| Acceso a datos | PDO y consultas preparadas |
| Entorno local | XAMPP |

## Arquitectura

El navegador presenta las páginas HTML y los estilos CSS, y utiliza JavaScript para interacciones de interfaz. Las páginas PHP procesan formularios, comprueban la sesión y los permisos, y llaman a las clases de acceso a datos. `includes/DB.php` centraliza la conexión PDO y `includes/SQL.php` agrupa las operaciones SQL. La información persistente se almacena en MySQL.

```text
Navegador
  ├── HTML / CSS / JavaScript
  └── Solicitudes HTTP
        ↓
Páginas PHP (views/ y admin/)
  ├── Sesión y protección CSRF (includes/security.php)
  ├── Reglas de la aplicación
  └── Acceso a datos (includes/SQL.php)
        ↓
Conexión PDO (includes/DB.php)
        ↓
MySQL / MariaDB
```

## Instalación local con XAMPP

1. Instala XAMPP con Apache, PHP y MySQL/MariaDB.
2. Copia la carpeta del proyecto dentro de `htdocs`.
3. Inicia Apache y MySQL desde el panel de XAMPP.
4. Abre phpMyAdmin y crea la base de datos `clinical_fbh`.
5. Importa `clinical_fbh.sql` en esa base de datos.
6. Copia `includes/.env.example.php` y crea `includes/.env.php` en la misma carpeta.
7. Edita `includes/.env.php` con el host, nombre de base de datos, usuario y contraseña de tu instalación local.
8. Abre el proyecto desde `http://localhost/NOMBRE-DE-LA-CARPETA/`.

El archivo `.env.php` es local y contiene la configuración de conexión. **No debe subirse al repositorio.** También se admiten las variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`.

### Crear el primer administrador

Registra una cuenta desde la aplicación. Después, en phpMyAdmin, ejecuta la siguiente consulta sustituyendo `tu_usuario` por el nombre de usuario que acabas de registrar:

```sql
UPDATE users_login
SET rol = 'admin'
WHERE usuario = 'tu_usuario';
```

Cierra la sesión y vuelve a entrar para que la aplicación cargue el rol actualizado.

## Estructura del proyecto

```text
admin/       Páginas de administración
css/         Hojas de estilo
images/      Recursos gráficos e imágenes de noticias
includes/    Configuración, conexión, seguridad y navegación
scripts/     JavaScript de interfaz
views/       Registro, acceso, perfil, citas y noticias
index.php    Página principal
logout.php   Cierre de sesión
clinical_fbh.sql  Estructura y datos iniciales de la base de datos
```

## Medidas de seguridad implementadas

- Contraseñas almacenadas con `password_hash()` y verificadas con `password_verify()`.
- Consultas preparadas mediante PDO en las operaciones de acceso a datos revisadas.
- Regeneración del identificador de sesión tras el inicio de sesión.
- Cookies de sesión `HttpOnly` y `SameSite=Lax`; `Secure` cuando la conexión utiliza HTTPS.
- Tokens CSRF en formularios que modifican información.
- Comprobación del rol de administrador en las páginas administrativas.
- Validación de imágenes subidas, límite de tamaño y nombres de archivo aleatorios.
- Escape de contenido mostrado en HTML en las vistas revisadas.
- Configuración local excluida mediante `.gitignore`.

Estas medidas reducen riesgos habituales, pero no constituyen una auditoría de seguridad ni garantizan que la aplicación sea adecuada para producción.

## Pruebas recomendadas antes de publicar

- Registrar una cuenta, iniciar sesión y cerrar sesión.
- Actualizar el perfil y cambiar la contraseña.
- Crear, editar y cancelar citas como usuario.
- Acceder al panel con una cuenta administradora y probar la gestión de usuarios, citas y noticias.
- Probar la subida de imágenes JPG, PNG y WEBP, y comprobar que se rechazan archivos no válidos y archivos superiores al límite.
- Comprobar las páginas en escritorio y móvil.
- Verificar que `includes/.env.php` no aparece entre los archivos versionados.

## Autor

**Fernando Barajas**

- GitHub: [@fbh1991](https://github.com/fbh1991)
- Repositorio: [clinica-saludplus](https://github.com/fbh1991/clinica-saludplus)

---

Proyecto personal de aprendizaje y portfolio. Los textos, perfiles y datos de demostración no representan pacientes ni una clínica real.
