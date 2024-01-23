# Pasantia1.01
# Guía de Configuración para Proyecto Symfony 

## 1. Requisitos previos:

Asegúrate de tener instalados los siguientes elementos en tu PC:

- PHP
- Composer
- Symfony

Puedes instalar Symfony mediante el instalador oficial o a través de Composer.

## 2. Clonar el Repositorio:

Clona el repositorio del proyecto desde el repositorio remoto utilizando Git.

```bash
git clone <URL_del_repositorio>
```
## 3. Instalar Dependencias:

Navega al directorio del proyecto y ejecuta el siguiente comando para instalar las dependencias del proyecto.

```bash
composer install
```

##  4 Configuración del Entorno:
Copia el archivo .env y configura las variables de entorno necesarias, como la conexión a la base de datos y las credenciales de Google Sign-In.
```bash
cp .env.dist .env
```

## 5 Crear la Base de Datos:
Si tu proyecto utiliza una base de datos, crea la base de datos y ejecuta las migraciones.
```bash
php bin/console doctrine:database:create
```
```bash
php bin/console doctrine:migrations:migrate
```


## 6 Ejecutar el Servidor de Desarrollo:
Inicia el servidor de desarrollo de Symfony.
```bash
symfony server:start
```
ó
```bash
npm run serve
```
