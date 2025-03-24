<?php

namespace App\Livewire;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Table extends Component
{
    use WithPagination, WithFileUploads;

    #[Modelable]
    public $tableData = [];

    public $pagination;

    public $model;  // The model class passed to the component

    #[Validate('image|max:2048')] // 2MB Max
    public $image;

    #[Validate(['images.*' => 'image|max:2048'])] // 2MB Max
    public $images = [];

    #[Url(as: 'q')]
    public $search = ''; // Query string parameter


    public $componentClass = [];
    public $formData = [];
    public $form;
    public $storerule = [];
    public $updaterule = [];
    public $title;
    public $add_route;
    public $delete;
    public $edit_route;
    public $table_fields = [];
    public $status_route;
    public $foreignKeys = [];
    public $itemId;
    public $pendingDbTransaction;
    public $detail_page;
    public $column_values = [];
    public $sortable = [];
    public $sortBy = 'id';
    public $sortOrder = 'desc';
    // Lifecycle hook to initialize properties
    public function mount($title, $model, $storerules = null, $updaterules, $componentClass)
    {
        $this->getForeignData();
        $this->form = $this->tableData['form'] ?? null;
        $this->title = $title;
        $this->foreignKeys = $this->tableData['foreignKeys'] ?? [];
        $this->add_route = $this->tableData['add'] ?? false;
        $this->edit_route = $this->tableData['edit'] ?? false;
        $this->delete = $this->tableData['delete'] ?? false;
        $this->table_fields = $this->tableData['table_fields'];
        $this->status_route = $this->tableData['status'] ?? null;
        $this->detail_page = $this->tableData['detail_page'] ?? null;
        $this->sortable = $this->tableData['sortable'] ?? [];
        $this->model = $model;
        $this->storerule = $storerules;
        $this->updaterule = $updaterules;
        $this->pagination = 5;
        $this->componentClass = $componentClass;
    }


    public function messages()
    {
        $messages = [];

        foreach ($this->formData as $index => $data) {
            $itemName = Str::title(str_replace('_', ' ', $index));

            $messages["formData.$index.required"] = "The $itemName field is required  .";
            $messages["formData.$index.string"] = "The $itemName must be a string .";
            $messages["formData.$index.max"] = "The $itemName may not exceed 255 characters  .";
            $messages["formData.$index.required"] = "The $itemName field is required.";
            $messages["formData.$index.integer"] = "The $itemName must be an integer.";
            $messages["formData.$index.min"] = "The $itemName must be at least 1.";
        }
        return $messages;
    }
    // Method to fetch data based on search query and tableData
    public function getList()
    {
        // Ensure that the model and search fields are set
        $query = $this->model::query();

        if (isset($this->tableData['search_fields']) && is_array($this->tableData['search_fields'])) {
            foreach ($this->tableData['search_fields'] as $index => $field) {
                if ($index === 0) {
                    $query->where($field, 'like', '%' . $this->search . '%');
                } else {
                    $query->orWhere($field, 'like', '%' . $this->search . '%');
                }
            }
        }

        $query->orderBy($this->sortBy, $this->sortOrder);

        return $query->paginate($this->pagination);
    }

    // Method to handle sorting of table data
    #[On('sortTable')]
    public function sortTable($key, $order)
    {
        $this->sortBy = $key;
        $this->sortOrder = $order;
        $this->dispatch('getList');
    }

    // Method to get the value of a foreign key
    public function getForeignValue($id, $model, $column)
    {
        if ($id) {
            $data = $model::find($id);
            if (isset($data->$column))
                return $data->$column;
        }
        return null;
    }

    // Method to handle to get form data, to support dynamic form data
    #[On('collectData')]
    public function collectFormData($key, $value, $formId = null)
    {
        $this->formData[$key] =  $value;
        $this->itemId = $formId;
    }

    // Method to handle to store form data
    #[On('storeForm')]
    public function storeForm()
    {
        $this->validate($this->storerule);
        $fillable = array_flip((new $this->model())->getFillable());
        $filteredData = array_intersect_key($this->formData, $fillable);
        $filteredData = $this->alterFormData($filteredData);
        DB::beginTransaction();
        try {
            $this->beforeCreateOperations();
            $model = $this->model::create($filteredData);
            $this->afterCreateOperations($model);

            if (isset($this->image)) {
                $path = $this->image->store(path: $this->title);
                $model->image()->create(['image' => $path]);
            } elseif (!empty($this->images)) {
                foreach ($this->images as $photo) {
                    $path = $photo->store(path: $this->title);
                    $model->image()->create(['image' => $path]);
                }
            }

            $this->dispatch('show-toast', message: 'Sucessfully Created!', type: 'success')->self();
            $this->formData = [];

            DB::commit();
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: $e->getMessage(), type: 'danger')->self();
            DB::rollBack();
        }
        $this->dispatch('refreshComponentState');
    }

    // Method to handle to update form data
    #[On('updateForm')]
    public function updateForm()
    {
        $this->validate($this->updaterule);
        $model = $this->model::find($this->itemId);

        $fillable = array_flip((new $this->model())->getFillable());
        $filteredData = array_intersect_key($this->formData, $fillable);
        $filteredData = $this->alterFormData($filteredData);
        request()->merge($this->formData);
        DB::beginTransaction();
        try {
            $this->beforeUpdateOperations($model);
            $model->update($filteredData);
            $this->afterUpdateOperations($model);
            if (isset($this->image)) {
                $path = $this->image->store(path: $this->title);
                $model->image()->create(['image' => $path]);
            } elseif (!empty($this->images)) {
                foreach ($this->images as $photo) {
                    $path = $photo->store(path: $this->title);
                    $model->image()->create(['image' => $path]);
                }
            }
            $this->dispatch('show-toast', message: 'Sucessfully Created!', type: 'success')->self();
            DB::commit();
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: $e->getMessage(), type: 'danger')->self();
            DB::rollBack();
        }
        $this->formData = [];
        $this->dispatch('refreshComponentState');
    }

    // Method to handle to delete image
    #[On('detachImage')]
    public function detachImage($modelType, $modelId, $image)
    {
        DB::beginTransaction();
        try {
            $modelType = $modelType;
            $modelId = $modelId;
            $modelInstance = App::make($modelType)->find($modelId);
            $img = Image::find($image);
            $filePath = $img->image;
            $flag = $modelInstance->image()->where('id', $img->id)->delete();
            Storage::delete($filePath);
            DB::commit();
            $this->dispatch('show-toast', message: 'Sucessfully Removed!', type: 'success')->self();
        } catch (Exception $e) {
            $this->dispatch('show-toast', message: $e->getMessage(), type: 'danger')->self();
            DB::rollBack();
        }
    }

    // Method to handle to delete image
    #[On('switchVisibility')]
    public function switchVisibility($id)
    {
        $item = $this->model::find($id);
        if ($item) {
            $item->status = !$item->status;
            $item->save();
        } else {
            $this->dispatch('show-toast', message: 'Item Not Found!', type: 'danger')->self();
        }
    }

    // Method to handle to delete item
    public function deleteItem($id)
    {
        $item = $this->model::find($id);
        if ($item) {
            $this->beforeDeleteOperations($item);
            $item->delete();
            $this->afterDeleteOperations($item);
            $this->dispatch('show-toast', message: 'Sucessfully Deleted Item!', type: 'success')->self();
        } else {
            $this->dispatch('show-toast', message: 'Item Not Found!', type: 'danger')->self();
        }
        $this->dispatch('close-modal');
        $this->dispatch('refreshComponentState');
    }


    // Method to handle to get foreign data
    public function getForeignData()
    {
        $data = [];
        foreach ($this->foreignKeys as $index => $item) {
            $values = $item['model']::all()->toArray();
            $data[$index] = $values;
        }
        return $data;
    }

    // Method to handle to alter form data before saving or updating
    public function alterFormData($formData)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'alterFormData')) {
            return $componentObj->alterFormData($formData);
        }
        return [];
    }

    // Method to handle to perform operations after creating a model
    public function afterCreateOperations($model)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'afterCreateOperations')) {
            $componentObj->afterCreateOperations($model);
        }
    }

    // Method to handle to perform operations after updating a model
    public function afterUpdateOperations($model)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'afterUpdateOperations')) {
            $componentObj->afterUpdateOperations($model);
        }
    }

    // Method to handle to perform operations before creating a model
    public function beforeCreateOperations()
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'beforeCreateOperations')) {
            $componentObj->beforeCreateOperations();
        }
    }

    // Method to handle to perform operations before updating a model
    public function beforeUpdateOperations($model)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'beforeUpdateOperations')) {
            $componentObj->beforeUpdateOperations($model);
        }
    }

    // Method to handle to perform operations before deleting a model
    public function beforeDeleteOperations($model)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'beforeDeleteOperations')) {
            $componentObj->beforeDeleteOperations($model);
        }
    }

    // Method to handle to perform operations after deleting a model
    public function afterDeleteOperations($model)
    {
        $componentObj = new $this->componentClass();
        if (method_exists($componentObj, 'afterDeleteOperations')) {
            $componentObj->afterDeleteOperations($model);
        }
    }

    // Method to handle to render the component
    public function render()
    {
        return view('livewire.table', [
            'values' => $this->getList(),
            'foreignData' => $this->getForeignData(),
            'foreignKeys' => $this->foreignKeys,
        ]);
    }
}
