# API Select Fieldtype addon for Statamic

Utilise API endpoints for your Select Fieldtype options.

## License

API Select requires a license.  

You can purchase one at https://statamic.com/marketplace/addons/api-select

You may use API Select without a license while Statamic is in [Trial mode](https://docs.statamic.com/knowledge-base/trial-mode).

## Installation

Require it using Composer.

```
composer require laraluke/statamic-api-select
```

Publish the assets:

```
php artisan vendor:publish --provider="Laraluke\ApiSelectFieldtype\ServiceProvider"
```

## Using the API Select Fieldtype

Add the fieldtype to your fieldset/blueprint. You will then need to configure the following settings:

#### Endpoint Type

This is to define whether or not your endpoint value is a URL or a config variable.

#### Endpoint

If your endpoint type is a URL your endpoint value will look like this:

```
https://jsonplaceholder.typicode.com/users
```

If your endpoint type is a config variable your endpoint value will look like this:

```
endpoints.users
```

and your `endpoints.php` file would look something like this:

```php
return [
    'users' => 'https://jsonplaceholder.typicode.com/users',
];
```

#### Cache Duration

This is how long API requests will be cached for in minutes.

Set this value to 0 if you don't want to cache results.

#### Data Set Key

If your data set isn't in the top-level of your API response you can define it's location using dot syntax.

So if your API result looks like:

```json
{
    "data": {
        "users": []
    }
}
```

You would set the data set key value to:

```
data.users
```

#### Item Key

Define the unique identifier to be used as the option value.

So if the iteration in your API result looks like this:

```json
{
    "id": 1,
    "name": "Leanne Graham",
    "username": "Bret",
    "email": "Sincere@april.biz",
}
```

You might set your item key to:

```
id
```

#### Item Label

Define the value to be used as the option label.

So if the iteration in your API result looks like this:

```json
{
    "id": 1,
    "name": "Leanne Graham",
    "username": "Bret",
    "email": "Sincere@april.biz",
}
```

You might set your item label to:

```
name
```

## Using the API Select value in your templates

Let's assume your API select field handle is `api_select_users` API response is like so:

```json
[
    {
        "id": 1,
        "name": "Leanne Graham",
        "username": "Bret",
        "email": "Sincere@april.biz",
    },
    {
        "id": 2,
        "name": "Ervin Howell",
        "username": "Antonette",
        "email": "Shanna@melissa.tv",
    }
]
```

#### Single-choice API Select

```
{{ api_select_users.name }}

// Returns
Leanne Graham
```

#### Multi-choice API Select

```
{{ api_select_multi }}
    {{ name }}
{{ /api_select_multi }}

// Returns
Leanne Graham
Ervin Howell
```
