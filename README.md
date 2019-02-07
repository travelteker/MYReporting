# MYReporting
## Proyecto para centralizar la generación de reports

### 1. Objetivo:
Centralizar mediante una única aplicación la gestión de los informes de usuarios (sistema reporting) generando ficheros de tipo Excel.

### 2. Aplicación dual:
- Via web, desde portal, incorporando:
    *  Control de sesiones
    *  Implementación JSON Web Token (JWT) 
    *  Control CSRF 
    *  Gestión de usuarios mediante roles y permisos
    *  Generación y descarga de informes en formato Excel

- Api RESTful para implementar servicio de descarga de ficheros Excel
    *  Implementación JSON Web Token (JWT) 

### 3. Sistema:
- Slim3 micro framework PHP
- Composer - Gestor de dependencias para PHP 
- PHINX - sistema de migraciones de Base de datos para PHP
- MySQL - Base de datos relacional para la persistencia de los datos
- Bootstrap 3 - Framework para desarrollo responsivo

### 4. Instalación y Ejecución
- Descargar todas las dependencias del proyecto
>  $ composer install
- Disponer de conexión a Base de datos MySQL
> Ver directorio **docs** para utilizar las migraciones generando las tablas necesarias y utilizando los seeders para poblar con datos las mismas.

- Uso via web (entorno desarrollo); configurar servidor web para que soporte URL amigables.
> http://localhost/myreporting

- Consumir api:

a) Obtener token para acceso al uso de la API, para ello utilizar como cabera la clave **'Myrtkn'** es el identificador para el uso de la API
```PHP
 CURLOPT_URL => "http://127.0.0.1/myreporting/public/api/token/codigo"
 CURLOPT_HTTPHEADER => array(
    "Myrtkn: --valor random--"
  )
```
b) Solicitar recurso para descargar fichero:
```PHP
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://127.0.0.1/myreporting/public/api/v1/inventario_productos/excel",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Myrtkn: --valor random--",
    "X-Token: Bearer --valor jwt obtenido en el paso anterior--",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
```
