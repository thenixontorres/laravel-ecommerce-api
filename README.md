# Curso de Laravel Avanzado en Platzi

Creación de un sistema que permitirá a tus usuarios puntuar compras y a otros usuarios desde 1 a 5 estrellas, implementando: Model Factory y seeders para generar datos; relaciones polimórficas entre tus clases; eventos que se dispararán ante las acciones de tus usuarios, service providers y service containers para aspectos como autenticación; y todo esto podrás publicarlo dentro de Packagist para ser reutilizado en múltiples proyectos.

## Clase 8

1. ``php artisan make:command SendNewsletterCommand``
2. Colocar una firma y descripción en el comando creado.
3. Crear notificación ``php artisan make:notification NewsletterNotification``
4. Habilitar verificación de correo implementando en el modelo ``User`` la interfaz ``MustVerifyEmail`` y modificando las rutas de Auth ``Auth::routes(['verify' => true]);``
5. Configurar Servidor SMTP, tenemos el de Homestead o podemos usar Mailtrap.
6. Crear la consulta dentro del Comando.
7. Probar que lleguen los correos.

## Clase 7 Reto

1. Utilizar el trait *CanBeRate* en Usuario.
2. Modificar los Test para probar.

## Clase 7

1. ``php artisan make:migration UpdateRatingTable``
2. Composicion de la tabla con rateable y qualifier
3. ``php artisan make:model Rating``
4. Definir mis relaciones *morphTo*
5. Definir las relaciones en *User*.
6. Hacer lo mismo en el modelo *Product*.
7. Como queremos evitar duplicar código, y nuestras relaciones son abstractas, podemos llevarlas a un trait.
8. Creo una carpeta llamada Traits y muevo el código a CanBeRated y CanRate respectivamente,
9. Correr los Test

La idea es que en nuestros trait no haya referencia alguna a ningún modelo, de tal manera que lo podamos reutilizar, así que pasemos por parámetro.

## Clase 6 Reto

1. Crear migración ``php artisan make:model AddCreatedByToProductsTable``
2. Agregar relación en Product.
```php
use App\User;

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}
```
3. Editar el Factory
```php
$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->numberBetween(10000, 60000),
        'category_id' => function () {
            return Category::inRandomOrder()->first()->id;
        },
        'created_by' => function () {
            return User::inRandomOrder()->first()->id;
        },
    ];
});
```
Nota: Para que funcione correctamente hay que asegurarnos que ejecutamos los Seeders en el Orden correcto. (Ver DatabaseSeeder.php)

## Clase 6

1. Crear migración ``php artisan make:migration add_category_id_to_products_table``
2. Editar migración para agregar la columna a la tabla Productos y ademas colocarle una categoría por defecto llamada "Otras" que creamos en la migración.
3. Ejecutar migración ``php artisan migrate``
4. Agregar relación en el Modelo Product:
```php
use App\Category;

public function category()
{
    return $this->belongsTo(Category::class);
}
```
5. Agregar relación inversa en Category.
```php
use App\Product;

public function products()
{
    return $this->hasMany(Product::class);
}
```
6. Modificar el Factory de Productos, para cuando lo usemos se cree con una categoria al azar.
```php
'category_id' => function (array $post) {
    return Category::inRandomOrder()->first()->id;
},
```
7. Testear y verificar que este andando todo.

###Extra: Crear Relación con un Modelo llamado Rating.

1. Crear Modelo (Rating.php), migracion (2020_05_23_235914_create_ratings_table.php) y Test (RatingTest.php)
2. Modificar la migración.
3. Relacionar con Productos y Usuarios (ver los modelos).


## Clase 5 Reto

1. Crear un nuevo recurso con ``php artisan make:resource CategoryResource`` y el método toArray definir lo que queremos devolver al usuario.
2. Modificar controlador de Categorías, y donde se devuelva el modelo sustituir por ``return new CategoryResource($category);``
3. Donde tengamos colecciones usar el método collection para no crear un nuevo archivo ``CategoryResource::collection(Category::paginate(5))``
4. Crear FormRequest ``php artisan make:request StoreCategoryRequest`` y  ``php artisan make:request UpdateCategoryRequest`` y agregar la regla de validación para que el nombre sea único ``Rule::unique('categories')``, como en la edición tenemos que validar todos los registros menos el que se esta actualizando colocamos ``Rule::unique('categories')->ignore($this->category)``
5. Ejecutar los Test.

## Clase 5

1. Crear un nuevo recurso con ``php artisan make:resource ProductResource`` y el método toArray definir lo que queremos devolver al usuario.
2. Modificar controlador de Productos, y donde se devuelva el modelo sustituir por ``return new ProductResource($product);``

Si necesitamos trabajar con más de un Modelo tenemos las Colecciones. No es necesario crear una colección por cada Recursos que declaremos, puesto que los mismos, contienen un método llamado collection. Pero como estamos practicando, si necesitas personalizar la meta data dentro de una coleccion puedes crear una con el comando:

1. ``php artisan make:resource ProductCollection``
2. Si queremos indicar que use un recurso colocamos ``public $collects = ProductResource::class;``
3. Completar método *toArray*
4. Si necesitas paginación las collections ya vienen con todo preparado, en la consulta del modelo y cambiamos el método all por *paginate* ``new ProductCollection(Product::paginate(5));``

Separando responsabilidades:

1. Crear FormRequest ``php artisan make:request StoreProductRequest`` y  ``php artisan make:request UpdateProductRequest``
2. En cada uno de los request podemos incorporar las reglas de validación en el método rules, y el acceso al mismo en el método authorize.
3. Ejecutar los Test. Acá agregamos un nuevo test llamado *test_validation_new_product* para comprobar las reglas de validación, se puede jugar con esto y hacer distintas pruebas para verificación.

*Reto de la Clase*: Hacer lo mismo con Categorías, y agregar una regla de validación para que el nombre sea único.

## Clase 4 - Reto

1. Modificar los Test y usar el Facade de Sanctum para autenticar un usuario 
```php
use App\User;
use Laravel\Sanctum\Sanctum;

Sanctum::actingAs(
    factory(User::class)->create()
);
``` 
2. Usar Trait en el Modelo Usuario para generar tokens ``use HasApiTokens``
3. Crear Endpoint para devolver Token ``php artisan make:controller UserTokenController``
4. Crear Test ``php artisan make:test UserTokenControllerTest``
5. Crear la ruta ``Route::post('/sanctum/token', 'UserTokenController');``
6. Correr los Test y verificar que todo siga funcionando.

*Extra*:
El middleware ``auth:sanctum`` se colocaron en cada Controller para que los métodos index y show no lo tengan. Ver *ProductController* y *CategoryController*.


## Clase 4

1. Instalar paquete Laravel UI ``composer require laravel/ui``
2. ``php artisan ui bootstrap --auth``
3. Ejecutar migraciones ``php artisan migrate``

Aca puedes probar la creacion de Usuarios si quieres, por otro lado si quieres ver los estilos tienes que compilar con Laravel Mix, ejecutando ``npm run dev
`` si no tienes Node, recuerda que puedes ejecutarlo dentro de la maquina virtual de Homestead. 

Ahora vamos con la autenticación API:

1. Instalar Sanctum ``composer require laravel/sanctum``
2. ``php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"``
3. Ejecutar migraciones ``php artisan migrate``
4. Editar Kernel.php para agregar el Middleware de Sanctum a la api.
```
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

'api' => [
    EnsureFrontendRequestsAreStateful::class,
    'throttle:60,1',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

5. Proteger las rutas.
6. Testear y ver que fallen los Test por que devuelven que el Usuario no esta autenticado.

*Reto de la Clase*: Modificar los Test para que pasen.

## Clase 3

¿Que es y como instalar Homestead?

https://laravel.com/docs/7.x/homestead#introduction

Cambiar el motor de Base de Datos.

## Clase 2 - Reto

1. Crear CategoryControllerTest y crear cada caso de uso para la API de Categorías. ```php migración make:test CategoryControllerTest```
2. Crea mi modelo Categoría con artisan e indicar las flags necesarios para que además cree la migracion, factory, seeder y controllador de API con ```php artisan make:model Category --api --all```
3. Editar migración para crear la tabla.
4. Crear las rutas y apuntarlas a cada método de mi API. Se puede usar  ```Route::apiResource('categories', 'CategoryController');```
5. Programar la lógica de negocio dentro de CategoryController
6. Ir Testeando cada método con CategoryControllerTest ``vendor/bin/phpunit --filter=CategoryControllerTest``

## Clase 2 

1. Crear ProductControllerTest y crear cada caso de uso para nuestra API de Productos. ```php artisan make:test ProductControllerTest```
2. Crea mi modelo Producto con artisan e indicar las flags necesarios para que además cree la migración, factory, seeder y controllador de API con ```php artisan make:model Produdct --api --all```
3. Editar migración de productos para crear la tabla.
4. Crear las rutas y apuntarlas a cada método de mi API. Se puede usar  ```Route::apiResource('products', 'ProductController');```
5. Programar la lógica de negocio dentro de ProductController
6. Ir Testeando cada método con ProductControllerTest ``vendor/bin/phpunit --filter=ProductControllerTest``

*Reto de la Clase*: Crear Endpoint para Categorías. 

## Clase 1

Repaso general de Laravel, y creación de proyecto Base.
