<?php

namespace App\Livewire;

use App\Models\Todo as myTable;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Todo extends Component
{
    use WithPagination;

    #[Rule('required|min:2|max:30')]
    public $name ='';
    public $search='';
    public $editId='';
    #[Rule('required|min:2|max:30')]
    public $newName='';

    public function createTodo(){
        $this->validateOnly('name');
        myTable::create(['name' => $this->name]);
        $this->resetPage();
        session()->flash('success','Task added successfully');
    }
    public function deleteTask($id){
        // $todo->delete();
        try{
            myTable::findOrFail($id)->delete();
        }catch(Exception $e){
            session()->flash('error','failed to delete');
            return;
        }
    }
    public function toggle($id){
        $task = myTable::find($id);
        $task->completed = !$task->completed;
        $task->save();
        // dd($task->completed);
    }
    public function edit($id){
        $this->editId = $id;
        $this->newName = myTable::find($id)->name;
    }
    public function update(){
        // $data = myTable::find($this->editId);
        $this->validateOnly('newName');
        myTable::find($this->editId)->update([
            'name' => $this->newName,
        ]);
        session()->flash('update','Task updated');
        $this->cancelEdit();
    }
    public function cancelEdit(){
        $this->reset('editId','newName');
    }
    public function render(){
        return view('livewire.todo',[
            'tasks' => myTable::latest()->where('name','like',"%{$this->search}%")->paginate(5),
        ]);
    }
}
