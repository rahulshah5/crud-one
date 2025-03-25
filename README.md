# CRUDOne Documentation

CRUDOne is a powerful Laravel Livewire package that simplifies CRUD operations with dynamic table and form handling. It provides a flexible, reusable Table component that can be easily integrated into your Laravel applications.

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Livewire 3.0+
- Bootstrap 5.0+ (CSS and JS)

## Features

- Dynamic table with pagination
- Sorting and searching
- Add, edit, and delete operations
- Form validation
- File uploads (single and multiple)
- Foreign key relationship handling
- Status toggling
- Custom operations hooks
- Responsive design

## Installation

Install the package via Composer:

```bash
composer require rahulshah/crudone
```

Publish the assets:

```bash
php artisan vendor:publish --provider="RahulShah\CRUDOne\CRUDOneServiceProvider" --tag="crudone"
```

### Livewire Configuration

Make sure Livewire is properly configured in your application. Add the Livewire scripts and styles to your layout:

```html
<!-- In your blade layout file -->
<html>
  <head>
    <!-- ... -->
    @livewireStyles
  </head>
  <body>
    <!-- ... -->
    @livewireScripts
  </body>
</html>
```

### Bootstrap Requirement

This package uses Bootstrap 5 for styling. Make sure to include Bootstrap CSS and JS in your application:

```html
<!-- In your blade layout file -->
<head>
  <!-- ... -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
</head>
<body>
  <!-- ... -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
```

## Basic Usage

### Create a Livewire Component

```php
<?php

namespace App\Livewire;

use App\Models\YourModel;
use Livewire\Component;
use Livewire\WithPagination;

class YourModelList extends Component
{
    use WithPagination;

    public $tableData = [
        'add' => true,
        'edit' => true,
        'delete' => true,
        'form' => 'admin.your-model.form',
        'table_fields' => [
            'name' => 'Name',
            'description' => 'Description',
            'created_at' => 'Created At'
        ],
        'search_fields' => ['name'],
        'sortable' => ['name', 'created_at']
    ];

    public $title = 'Your Model';
    public $model = YourModel::class;

    public $storerules = [
        'formData.name' => 'required|string|max:255',
        'formData.description' => 'required|string'
    ];

    public $updaterules = [
        'formData.name' => 'required|string|max:255',
        'formData.description' => 'required|string'
    ];

    public function render()
    {
        return view('livewire.your-model-list');
    }
}
```

### Blade View

```html
<div>
  <livewire:table
    wire:model="tableData"
    :storerules="$storerules"
    :updaterules="$updaterules"
    :model="$model"
    :title="$title"
    :componentClass="static::class"
  />
</div>
```

## Advanced Configuration

### Table Options

| Option          | Type   | Description                 |
| --------------- | ------ | --------------------------- |
| `add`           | bool   | Enable add functionality    |
| `edit`          | bool   | Enable edit functionality   |
| `delete`        | bool   | Enable delete functionality |
| `form`          | string | Path to form component      |
| `table_fields`  | array  | Fields to display           |
| `search_fields` | array  | Searchable fields           |
| `sortable`      | array  | Sortable fields             |

### Foreign Key Handling

```php
'tableData' => [
    'foreignKeys' => [
        'category_id' => [
            'model' => Category::class,
            'column' => 'name'
        ]
    ]
]
```

### File Uploads

```html
<input
  type="file"
  class="file:bg-primary file:text-white"
  name="image"
  id="image"
/>
```

## Custom Operations

Implement these methods in your component:

- `alterFormData($formData)`
- `beforeCreateOperations()`
- `afterCreateOperations($model)`
- `beforeUpdateOperations($model)`
- `afterUpdateOperations($model)`

Example:

```php
public function alterFormData($formData)
{
    $formData['slug'] = Str::slug($formData['title']);
    return $formData;
}
```

I'll update the README.md file to include the proper instructions for registering the Table component in the AppServiceProvider.php.

````markdown:e:\Coding Workspace\Alpha\Package Test\package-test\AlphaTechnologies\CRUDZen\README.md
// ... existing code ...

## Custom Operations

Implement these methods in your component:

- `alterFormData($formData)`
- `beforeCreateOperations()`
- `afterCreateOperations($model)`
- `beforeUpdateOperations($model)`
- `afterUpdateOperations($model)`

Example:

```php
public function alterFormData($formData)
{
    $formData['slug'] = Str::slug($formData['title']);
    return $formData;
}
````

## Register Table Component

Add the following code to your `AppServiceProvider.php` to manually register the Table component:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Table;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the Table component with the alias 'table'
        Livewire::component('table', Table::class);
    }
}
```

This step is necessary if you've published the Table component to your application's Livewire directory and want to use it with a custom namespace.

## License

[MIT License](https://opensource.org/licenses/MIT)

```

```
