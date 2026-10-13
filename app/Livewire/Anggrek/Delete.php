<?php

namespace App\Livewire\Anggrek;

use App\Models\Anggrek;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Delete extends Component
{
    public $anggrekId;
    public $showModal = false;

    protected $listeners = ['deleteAnggrek' => 'delete'];

    public function delete($id)
    {
        $this->anggrekId = $id;
        $this->showModal = true;
    }

    public function destroy()
    {
        $anggrek = Anggrek::findOrFail($this->anggrekId);

        // Ensure user can only delete their own orchids
        if ($anggrek->user_id !== Auth::id()) {
            abort(403);
        }

        $anggrek->delete();

        session()->flash('message', 'Data anggrek berhasil dihapus.');
        
        $this->showModal = false;
        $this->dispatch('anggrekDeleted');
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->anggrekId = null;
    }

    public function render()
    {
        return view('livewire.anggrek.delete');
    }
}
