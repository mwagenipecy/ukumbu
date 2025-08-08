<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $showModal = false;
    public $editingCategoryId = null;
    public $name = '';
    public $type = 'venue';
    
    protected $listeners = [
        'categoryDeleted' => '$refresh',
        'closeModal' => 'resetModal'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:venue,service',
    ];

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.max' => 'Category name cannot exceed 255 characters.',
        'type.required' => 'Category type is required.',
        'type.in' => 'Category type must be either venue or service.',
    ];

    public function mount()
    {
        $this->filterType = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function openEditModal($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $this->editingCategoryId = $categoryId;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->showModal = true;
    }

    public function resetModal()
    {
        $this->showModal = false;
        $this->editingCategoryId = null;
        $this->name = '';
        $this->type = 'venue';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        // Check for duplicate names within the same type
        $query = Category::where('name', $this->name)->where('type', $this->type);
        if ($this->editingCategoryId) {
            $query->where('id', '!=', $this->editingCategoryId);
        }
        
        if ($query->exists()) {
            $this->addError('name', 'A category with this name already exists for ' . $this->type . 's.');
            return;
        }

        try {
            if ($this->editingCategoryId) {
                // Update existing category
                $category = Category::findOrFail($this->editingCategoryId);
                $category->update([
                    'name' => $this->name,
                    'type' => $this->type,
                ]);
                
                session()->flash('success', 'Category updated successfully!');
            } else {
                // Create new category
                Category::create([
                    'name' => $this->name,
                    'type' => $this->type,
                ]);
                
                session()->flash('success', 'Category created successfully!');
            }

            $this->resetModal();
            $this->dispatch('categoryUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the category.');
        }
    }

    public function delete($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Check if category is being used
            $venueCount = $category->venues()->count();
            $serviceCount = $category->services()->count();
            
            if ($venueCount > 0 || $serviceCount > 0) {
                session()->flash('error', 'Cannot delete category. It is currently being used by ' . ($venueCount + $serviceCount) . ' ' . ($category->type === 'venue' ? 'venues' : 'services') . '.');
                return;
            }

            $category->delete();
            session()->flash('success', 'Category deleted successfully!');
            $this->dispatch('categoryDeleted');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the category.');
        }
    }

    public function getCategoriesProperty()
    {
        $query = Category::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        return $query->withCount(['venues', 'services'])
                    ->orderBy('type')
                    ->orderBy('name')
                    ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'categories' => $this->categories,
        ]);
    }
}