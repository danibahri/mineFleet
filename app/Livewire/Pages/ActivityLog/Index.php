<?php

namespace App\Livewire\Pages\ActivityLog;

use App\Models\ActivityLog as ActivityLogModel;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $module    = '';
    public string $action    = '';
    public string $userId    = '';
    public string $dateFrom  = '';
    public string $dateTo    = '';
    public int    $perPage   = 20;

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingModule(): void   { $this->resetPage(); }
    public function updatingAction(): void   { $this->resetPage(); }
    public function updatingUserId(): void   { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void   { $this->resetPage(); }

    #[Computed]
    public function logs()
    {
        return ActivityLogModel::query()
            ->with('user:id,name,email')
            ->when($this->userId !== '', fn($q) => $q->where('user_id', $this->userId))
            ->when($this->module !== '', fn($q) => $q->where('module', $this->module))
            ->when($this->action !== '', fn($q) => $q->where('action', $this->action))
            ->when($this->dateFrom !== '', fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($q): void {
                    $q->where('description', 'like', "%{$this->search}%")
                      ->orWhere('module', 'like', "%{$this->search}%")
                      ->orWhere('action', 'like', "%{$this->search}%")
                      ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function users()
    {
        return User::query()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function modules()
    {
        return ActivityLogModel::query()
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
    }

    #[Computed]
    public function actions()
    {
        return ActivityLogModel::query()
            ->distinct()
            ->when($this->module !== '', fn($q) => $q->where('module', $this->module))
            ->orderBy('action')
            ->pluck('action');
    }

    #[Computed]
    public function stats()
    {
        return [
            'today'  => ActivityLogModel::whereDate('created_at', today())->count(),
            'week'   => ActivityLogModel::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total'  => ActivityLogModel::count(),
            'users'  => ActivityLogModel::distinct('user_id')->count('user_id'),
        ];
    }

    public function moduleClass(string $module): string
    {
        return match (strtolower($module)) {
            'booking'  => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
            'approval' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300',
            'vehicle'  => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
            'driver'   => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
            'auth'     => 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-300',
            'fuel'     => 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300',
            'service'  => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
            default    => 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400',
        };
    }

    public function actionClass(string $action): string
    {
        return match (strtolower($action)) {
            'login', 'create', 'approve' => 'text-emerald-600 dark:text-emerald-400',
            'logout', 'delete', 'reject', 'cancel' => 'text-rose-600 dark:text-rose-400',
            'update', 'edit' => 'text-amber-600 dark:text-amber-400',
            default => 'text-slate-600 dark:text-slate-300',
        };
    }

    public function render()
    {
        return view('pages.activity-log.index');
    }
}
