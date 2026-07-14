# Gastronomía Caribeña 🍽️🌴

**Plataforma web desarrollada como proyecto universitario** para la **Salvaguarda Cultural de la Gastronomía del Caribe Limón**, en la Universidad de Costa Rica.

Este sistema permite registrar restaurantes, comidas y plantas tradicionales, así como gestionar eventos, usuarios y roles. Es un esfuerzo por preservar el patrimonio cultural de la región Caribe de Costa Rica.

## 🧩 Tecnologías utilizadas

- **Frontend:** Angular, HTML, CSS
- **Backend:** PHP (Slim Framework), MySQL
- **Infraestructura:** Docker, Docker Compose
- **Otros:** SweetAlert2, Bootstrap

## ✨ Características técnicas

- Arquitectura cliente-servidor con separación entre frontend y backend.
- API REST desarrollada con PHP Slim Framework.
- Frontend desarrollado con Angular y TypeScript.
- Base de datos relacional en MySQL.
- Contenerización mediante Docker y Docker Compose.
- Sistema de autenticación con control de roles.
- Gestión de restaurantes, comidas, plantas autóctonas y eventos.
- Base de datos con procedimientos almacenados.
- Interfaz bilingüe (español/inglés).
- Diseño modular para facilitar el mantenimiento y la escalabilidad.

## 🔧 Funcionalidades principales

- Registro e inicio de sesión con control de roles (usuario/admin)
- Gestión de:
  - Restaurantes
  - Comidas tradicionales
  - Plantas autóctonas
  - Eventos con cupos y restricciones de edad
- Interfaz bilingüe (español/inglés)
- Visualización por filtros
- Base de datos estructurada y procedimientos almacenados
- Sistema modular organizado por capas (frontend, backend, base de datos)

## 🚀 Cómo ejecutar el proyecto

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/gaby0398/gastronomia-caribena.git
   cd gastronomia-caribena
   Levantar el entorno con Docker:

bash
docker-compose up --build
Acceder a la aplicación:

Frontend: http://localhost:4200

Backend API: http://localhost:9000/api
