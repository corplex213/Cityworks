<?php
// filepath: app/Http/Livewire/TotalProjectsCard.php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Project;

class TotalProjectsCard extends Component
{
    public function render()
    {
        return view('livewire.total-projects-card', [
            'totalProjects' => Project::count(),
        ]);
    }
}