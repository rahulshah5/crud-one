# CRUDOne Documentation

CRUDOne is a powerful Laravel Livewire package that simplifies CRUD operations with dynamic table and form handling. It provides a flexible, reusable Table component that can be easily integrated into your Laravel applications.

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Livewire 3.0+

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
        :componentClass="static::class" />
</div>
```

## Advanced Configuration

### Table Options

| Option | Type | Description |
|--------|------|-------------|
| `add` | bool | Enable add functionality |
| `edit` | bool | Enable edit functionality |
| `delete` | bool | Enable delete functionality |
| `form` | string | Path to form component |
| `table_fields` | array | Fields to display |
| `search_fields` | array | Searchable fields |
| `sortable` | array | Sortable fields |

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
<input type="file" 
       class="file:bg-primary file:text-white"
       name="image" 
       id="image">
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

## Complete Example

### Blog Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['title', 'description'];
    
    public function image()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
```

## License

[MIT License](https://opensource.org/licenses/MIT)

```