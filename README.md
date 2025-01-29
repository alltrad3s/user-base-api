Sistema de Gestión y Análisis de Usuarios
=========================================

Este proyecto es parte de una aplicación full-stack que proporciona capacidades de gestión de usuarios y análisis a través de una API backend en Laravel con un frontend en React.

Descripción General
-------------------

El sistema cuenta con dos niveles de acceso principales:

### Usuarios Regulares

-   Registro de cuenta
-   Inicio de sesión seguro mediante tokens (caducidad: 10 minutos)
-   Creación y gestión de posts y comentarios
-   Visualización de contenido público

### Administradores

-   Visualización de estadísticas completas de registro de usuarios
-   Seguimiento del crecimiento de usuarios en diferentes períodos
-   Gestión completa de cuentas de usuario
-   Acceso a análisis detallados de patrones de registro

Guía de Uso de la API
---------------------

### Autenticación

`POST /api/register`
`Body:
{
    "name": "string",
    "email": "string",
    "password": "string"
}`

`POST /api/login`
`Body:
{
    "email": "string",
    "password": "string"
}`

`POST /api/logout (requiere autenticación)
Header: Authorization: Bearer {token}`

### Endpoints Exclusivos para Administradores

Todos estos endpoints requieren autenticación y rol de administrador.

#### Gestión de Usuarios

`GET    /api/admin/users         - Listar usuarios (paginado)`
`POST   /api/admin/users         - Crear usuario`
`PUT    /api/admin/users/{id}    - Actualizar usuario`
`DELETE /api/admin/users/{id}    - Eliminar usuario`

#### Estadísticas

`GET /api/admin/statistics`

### Guía de Uso de Estadísticas

El endpoint de estadísticas acepta los siguientes parámetros:

#### 1\. Estadísticas Diarias

`GET /api/admin/statistics?period=daily&date=2024-01-28`

`Respuesta:`
`- Total de usuarios registrados ese día`
`- Desglose por hora (0-23 horas)`

#### 2\. Estadísticas Semanales (Últimos 7 días)

`GET /api/admin/statistics?period=weekly`

`Respuesta:`
`- Total de usuarios de la semana`
`- Desglose diario de los últimos 7 días`

#### 3\. Estadísticas Mensuales (Últimos 30 días)

`GET /api/admin/statistics?period=monthly`

`Respuesta:`
`- Total de usuarios del mes`
`- Desglose diario de los últimos 30 días`

#### 4\. Período Personalizado

`GET /api/admin/statistics?period=custom&start_date=2024-01-01&end_date=2024-01-31`

`Respuesta:`
`- Total de usuarios del período`
`- Desglose diario entre las fechas seleccionadas`

Seguridad
---------

El sistema implementa varias medidas de seguridad:

-   Autenticación basada en tokens con caducidad de 10 minutos
-   Control de acceso basado en roles
-   Encriptación de contraseñas
-   Endpoints API protegidos
-   Validación de todas las entradas de usuario

Características Técnicas
------------------------

### Backend

-   Desarrollado con Laravel 11
-   Autenticación mediante Laravel Sanctum
-   Control de acceso basado en roles
-   API RESTful con respuestas JSON

### Frontend

-   Desarrollado en React
-   Panel de control intuitivo
-   Selectores de fecha interactivos
-   Gráficos visuales de tendencias
-   Interfaz de gestión de usuarios

Posibles Extensiones Futuras
----------------------------

El sistema está diseñado para ser expandible. Algunas posibles adiciones incluyen:

-   Análisis de usuarios más detallados
-   Funcionalidad de exportación de informes
-   Roles y permisos adicionales
-   Búsqueda y filtrado mejorados
-   Notificaciones por correo electrónico

Notas de Implementación
-----------------------

Para mantener la seguridad del sistema:

-   Todos los tokens caducan después de 10 minutos de inactividad
-   Los usuarios deben volver a iniciar sesión después de la caducidad del token
-   Las solicitudes a endpoints protegidos requieren un token válido
-   Solo los administradores pueden acceder a las funciones de gestión y estadísticas
