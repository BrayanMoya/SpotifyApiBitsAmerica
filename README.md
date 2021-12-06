# Spotify Api Bits Americas

Catálogo de ultimos lanzamientos de albumes de spotify, con información a cada artista relacionado. 

##Clonar
```
1. Tener git instalado
2. Ubicarse en un directorio en el cual pondremos el contenido de este repositorio.
3. Abrir en este directorio, la consola de git o la preferente por usted y agregar el siguiente comando:

* Por SSH
git clone git@github.com:BrayanMoya/SpotifyApiBitsAmerica.git

* Con HTTPS
git clone https://github.com/BrayanMoya/SpotifyApiBitsAmerica.git

4. Luego de clonado el repositorio, ubicarse desde la consola dentro de la carpeta que se creo automaticamente como 'SpotifyApiBitsAmerica'
```

## Instalacion 
- Ubicado siempre desde consola dentro de la carpeta del repositorio clonado
### Instalación de dependencias de composer
```sh
composer install
```

### Configuración para servidor
Ya sea si se inicia el servidor local que provee php (php -S localhost:8000), o un servidor aparte como IIS o nginx, la ruta para el controlador frontal esta dentro de la carpeta web/ y el archivo es app.php.

ruta final para visualizar en navegador:

localhost/app.php/es, el parametro 'es' es para internacionalización


## Vista previa
![alt text](https://github.com/BrayanMoya/SpotifyApiBitsAmerica/tree/master/InicioSpotifyApi.png)

![alt text](https://github.com/BrayanMoya/SpotifyApiBitsAmerica/tree/master/Lanzamientos.png)

## Configuracion general
- Iniciar sesión o crear cuenta en https://developer.spotify.com/dashboard/login
- Luego de estar dentro, crear una aplicación dandole en el botón 'Create an app'
- Luego de creada la apliación, ingresar a esta y copiar el client id y el client secret
- Configurar en el app/config/parameters.yml:
    - clientId suministrado por la aplicación creada en spotify
    - clientSecret suministrado por la aplicación creada en spotify

En caso de no tener apliación en spotify y no querer realizar la creación de esta, ya viene por defecto con unos valores de una aplicación ya creada.

## Referencias
- https://developer.spotify.com/documentation/web-api
