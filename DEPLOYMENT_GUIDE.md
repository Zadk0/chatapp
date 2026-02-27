# 🚀 Guía de Despliegue - ChatApp en Render.com

## 📋 Requisitos Previos
- Cuenta en [Render.com](https://render.com) (gratis)
- Cuenta en GitHub/GitLab
- Cuenta de Gmail para envío de emails (verificación)

---

## 🔒 Seguridad Implementada

| Característica | Implementación |
|---|---|
| **Contraseñas** | `bcrypt` via Laravel Hash (costo 12) |
| **Mensajes** | `AES-256-CBC` via `Illuminate\Support\Facades\Crypt` |
| **Sesiones** | Almacenadas en base de datos, cifradas |
| **Verificación email** | Laravel MustVerifyEmail + link firmado |
| **CSRF** | Token en todos los formularios |
| **Tokens** | `APP_KEY` de 256 bits para cifrado |

---

## 📁 Estructura del Proyecto

```
chatapp/
├── app/
│   ├── Events/MessageSent.php          # Evento WebSocket
│   ├── Http/Controllers/
│   │   ├── Auth/AuthController.php     # Login, Register, Logout
│   │   └── ChatController.php          # Chat, mensajes
│   └── Models/
│       ├── User.php                    # Con MustVerifyEmail
│       ├── Message.php                 # Cifrado automático
│       └── Conversation.php
├── database/migrations/                # Tablas PostgreSQL
├── resources/views/
│   ├── auth/login.blade.php
│   ├── auth/register.blade.php
│   ├── auth/verify-email.blade.php
│   └── chat/index.blade.php           # Interfaz estilo WhatsApp
├── routes/web.php
├── Dockerfile
├── render.yaml
└── docker/start.sh
```

---

## 🚀 Paso a Paso: Despliegue en Render

### 1. Preparar el repositorio

```bash
# En tu computadora local:
git init
git add .
git commit -m "Initial commit - ChatApp"

# Crear repositorio en GitHub y subir
git remote add origin https://github.com/TU_USUARIO/chatapp.git
git push -u origin main
```

### 2. Crear la Base de Datos en Render

1. Ve a [render.com/dashboard](https://dashboard.render.com)
2. Clic en **"New +"** → **"PostgreSQL"**
3. Configura:
   - **Name:** `chatapp-db`
   - **Plan:** Free
4. Clic **"Create Database"**
5. **Guarda** los datos de conexión (los necesitarás)

### 3. Crear el Web Service

1. Clic en **"New +"** → **"Web Service"**
2. Conecta tu repositorio de GitHub
3. Configura:
   - **Name:** `chatapp`
   - **Runtime:** `Docker`
   - **Plan:** Free (o Starter para producción)

### 4. Variables de Entorno

En el panel de Render, ve a **"Environment"** y agrega:

```
APP_NAME=ChatApp
APP_ENV=production
APP_DEBUG=false
APP_KEY=  (deja vacío, el script lo genera)

# Base de datos (copia de tu PostgreSQL de Render)
DB_CONNECTION=pgsql
DB_HOST=<tu-host-postgresql-render>
DB_PORT=5432
DB_DATABASE=chatapp
DB_USERNAME=<tu-usuario>
DB_PASSWORD=<tu-password>

# Email (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu.email@gmail.com
MAIL_PASSWORD=<app-password-gmail>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@chatapp.com
MAIL_FROM_NAME=ChatApp

# WebSocket Reverb
REVERB_APP_ID=chatapp
REVERB_APP_KEY=<generar-clave-aleatoria>
REVERB_APP_SECRET=<generar-secreto-aleatorio>
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https

BROADCAST_DRIVER=reverb
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### 5. Generar App Password de Gmail

1. Ve a tu cuenta de Google
2. **Seguridad** → **Verificación en 2 pasos** (activar si no está)
3. **Contraseñas de aplicaciones** → Generar para "Correo"
4. Usa esa contraseña en `MAIL_PASSWORD`

### 6. Desplegar

1. Clic en **"Create Web Service"**
2. Render descargará el código y construirá el Docker
3. El script `start.sh` ejecutará automáticamente:
   - `php artisan key:generate`
   - `php artisan migrate`
   - Inicia Nginx + PHP-FPM + Queue Worker + Reverb

---

## 🔧 Comandos Útiles (Render Shell)

```bash
# Ver logs de Laravel
php artisan log:view

# Ejecutar migraciones manualmente
php artisan migrate

# Regenerar clave de app
php artisan key:generate

# Limpiar caché
php artisan optimize:clear

# Ver jobs en cola
php artisan queue:monitor
```

---

## 🐛 Solución de Problemas

### "SQLSTATE: Connection refused"
→ Verifica que `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sean correctos.

### "Mail: Connection could not be established"
→ Verifica el App Password de Gmail y que `MAIL_ENCRYPTION=tls`.

### Los mensajes no llegan en tiempo real
→ El polling automático (cada 3 segundos) funciona como fallback. Para WebSockets reales, configura Reverb en un servicio separado.

### "No application encryption key has been specified"
→ El script start.sh debería generarla. Si falla, ejecuta en Render Shell:
```bash
php artisan key:generate --force
```

---

## 📱 Funcionalidades

- ✅ **Login** con email + contraseña
- ✅ **Registro** de nuevos usuarios
- ✅ **Verificación de email** (link firmado)
- ✅ **Interfaz estilo WhatsApp** (oscura, responsiva)
- ✅ **Mensajes cifrados** con AES-256-CBC
- ✅ **Contraseñas hasheadas** con bcrypt
- ✅ **Conversaciones en tiempo real** (polling + WebSocket)
- ✅ **Estado "en línea"** de usuarios
- ✅ **Contador de mensajes no leídos**
- ✅ **Búsqueda** de conversaciones y usuarios
- ✅ **Base de datos PostgreSQL**
- ✅ **Listo para Render.com**

---

## 💰 Costos en Render

| Plan | Costo | Ideal para |
|---|---|---|
| Free | $0/mes | Pruebas (se apaga si no hay tráfico) |
| Starter | $7/mes | Producción básica |
| Standard | $25/mes | Producción con buena performance |

> **Nota:** El plan Free de PostgreSQL expira después de 90 días.
