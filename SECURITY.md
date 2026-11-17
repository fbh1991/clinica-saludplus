# Política de seguridad y alcance

Clínica SaludPlus es un proyecto formativo y no debe utilizarse para almacenar información sanitaria, datos personales reales de pacientes ni credenciales de producción.

## Configuración segura para desarrollo

- Mantén `includes/.env.php` fuera del control de versiones.
- Utiliza credenciales locales y un usuario de base de datos con permisos limitados.
- No publiques volcados SQL con información personal o contraseñas reales.
- Usa HTTPS en cualquier entorno accesible desde Internet.
- Mantén PHP, MySQL/MariaDB y XAMPP actualizados.

## Reportar un problema

Para informar de un problema en este proyecto académico, abre una incidencia en el repositorio de GitHub sin incluir contraseñas, tokens, datos personales ni información sensible.

## Limitaciones conocidas

La aplicación no ha sido sometida a una auditoría profesional, pruebas de penetración ni validación de cumplimiento normativo. Antes de cualquier despliegue real serían necesarios, entre otros trabajos, una revisión de seguridad independiente, controles operativos, gestión de copias de seguridad, monitorización, protección de datos y pruebas funcionales completas.
