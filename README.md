# Bistro FDI 🍴

[](https://www.fdi.ucm.es/)
[](https://github.com/Raanmdornes1920/AW)

[cite_start]Proyecto desarrollado para la asignatura **Aplicaciones Web** en la Universidad Complutense de Madrid[cite: 325, 332]. [cite_start]Este sistema integral permite la gestión de usuarios, categorías, productos y el flujo completo de pedidos en un entorno de restauración[cite: 552].

## 🚀 Datos de Despliegue y Acceso

  * [cite_start]**URL de la aplicación:** [vm016.containers.fdi.ucm.es](https://vm016.containers.fdi.ucm.es/) [cite: 420]
  * [cite_start]**Repositorio:** [GitHub - Raanmdornes1920/AW](https://github.com/Raanmdornes1920/AW) [cite: 421]

### [cite_start]Cuentas de Prueba[cite: 422]:

| Rol | Usuario | Contraseña |
| :--- | :--- | :--- |
| **Gerente** | `admin` | `admin` |
| **Cliente** | `cliente` | `cliente` |
| **Camarero** | `camarero` | `camarero` |
| **Cocinero** | `cocinero` | `cocinero` |

-----

## 🛠️ Arquitectura del Sistema

La aplicación sigue un modelo de arquitectura por capas para garantizar la escalabilidad y el mantenimiento:

### 1\. Capa de Vistas y Formularios

Utiliza una estructura de scripts PHP para generar la interfaz dinámica. [cite_start]Se apoya en una clase base `formularioBase.php` para la gestión estandarizada de formularios[cite: 335, 416].

### 2\. Capa de Servicio (SA)

[cite_start]Actúa como intermediaria entre las vistas y el acceso a datos, aplicando las reglas de negocio[cite: 585, 588].

  * [cite_start]`UsuarioSA`, `CategoriaSA`, `ProductoSA`, `PedidoSA`[cite: 416].

### 3\. Capa de Acceso a Datos (DAO)

[cite_start]Encargada de las consultas SQL y la persistencia en la base de datos[cite: 563].

  * [cite_start]**Transacciones:** El `PedidoDAO` utiliza transacciones de MySQL para asegurar que el pedido y sus líneas se guarden correctamente o no se guarde nada (atomicidad)[cite: 573].

### 4\. Objetos de Transferencia (DTO)

[cite_start]Clases que representan las entidades del sistema (`Usuario`, `Producto`, `Categoria`, `Pedido`) facilitando el transporte de información entre capas[cite: 575, 416].

-----

## 📋 Funcionalidades Principales

### 👤 Gestión de Usuarios (Funcionalidad 0)

  * [cite_start]**Autenticación:** Sistema de login y registro con validación de campos[cite: 450, 454].
  * [cite_start]**Perfil:** Los usuarios pueden editar su avatar, email y contraseña mediante pop-ups gestionados con JavaScript[cite: 458, 462].
  * [cite_start]**Panel de Administración:** El gerente puede listar, crear, editar y eliminar cualquier usuario[cite: 440, 467].

### 📦 Gestión de Catálogo (Funcionalidad 1)

  * **Categorías:** Organización de productos. [cite_start]El gerente gestiona las categorías (CRUD) y los clientes pueden navegar por ellas[cite: 485, 487].
  * [cite_start]**Productos:** Fichas detalladas con carrusel de imágenes, gestión de stock, cálculo automático de IVA mediante `producto.js` y borrado lógico[cite: 527, 538, 544].

### 🛒 Ciclo de Pedidos (Funcionalidad 2)

1.  [cite_start]**Cliente:** Añade productos al carrito, elige modalidad (Local/Llevar) y simula el pago[cite: 553].
2.  [cite_start]**Seguridad:** El sistema recalcula el total en el backend antes de guardar para evitar manipulaciones de precio en el frontend[cite: 590].
3.  [cite_start]**Cocina/Sala:** Los cocineros marcan productos listos individualmente y los camareros gestionan el cobro y entrega final[cite: 557, 558].

-----

## 💾 Estructura de la Base de Datos

[cite_start]El sistema se apoya en una base de datos relacional con las siguientes tablas principales[cite: 602]:

  * [cite_start]**Usuarios:** Almacena credenciales, roles y rutas de avatares[cite: 602].
  * [cite_start]**Categorías y Productos:** Relación 1:N que define la carta del restaurante[cite: 607, 611].
  * [cite_start]**Imágenes de Producto:** Permite múltiples fotos por producto para visualización en carrusel[cite: 621].
  * [cite_start]**Pedidos y Líneas de Pedido:** Estructura N:M para registrar las compras, estados del pedido y precios históricos de los productos[cite: 626, 634].

-----

## 👥 Equipo (Grupo C - Equipo Nº 2)

  * [cite_start]Héctor Manuel Díaz Bernal [cite: 329]
  * [cite_start]Ángela Meirás Seisdedos [cite: 329]
  * [cite_start]Ramón Andrés Salazar Gutiérrez [cite: 330]
  * [cite_start]Miguel Sevilla Benito [cite: 331]

-----

[cite_start]*Facultad de Informática - Universidad Complutense de Madrid* [cite: 332]
