<?php

namespace App\Livewire\Pages\Setting;

use App\Models\Region;
use App\Models\Role;
use App\Models\VehicleType;
use App\Services\ActivityLogger;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $tab = 'company'; // company | approval | regions | vehicle_types

    // ── Company profile ───────────────────────────────────────────────────────
    public string $companyName    = '';
    public string $companyAddress = '';
    public string $companyPhone   = '';
    public string $companyEmail   = '';
    public string $companyWebsite = '';

    // ── Approval config ───────────────────────────────────────────────────────
    public string $approvalLevel1RoleId = '';
    public string $approvalLevel2RoleId = '';
    public bool   $requireLevel2        = true;

    // ── Region form ───────────────────────────────────────────────────────────
    public ?int   $regionId   = null;
    public string $regionName = '';
    public string $regionCode = '';
    public string $regionDesc = '';
    public string $activeRegionModal = '';

    // ── VehicleType form ──────────────────────────────────────────────────────
    public ?int   $vehicleTypeId   = null;
    public string $vehicleTypeName = '';
    public string $vehicleTypeDesc = '';
    public string $activeVehicleTypeModal = '';

    public function mount(): void
    {
        // Load company settings from config or database (simple file-based approach)
        $settings = $this->loadSettings();
        $this->companyName        = $settings['company_name'] ?? 'MineFleet';
        $this->companyAddress     = $settings['company_address'] ?? '';
        $this->companyPhone       = $settings['company_phone'] ?? '';
        $this->companyEmail       = $settings['company_email'] ?? '';
        $this->companyWebsite     = $settings['company_website'] ?? '';
        $this->requireLevel2      = (bool) ($settings['require_level_2'] ?? true);
        $this->approvalLevel1RoleId = (string) ($settings['approval_level1_role_id'] ?? '');
        $this->approvalLevel2RoleId = (string) ($settings['approval_level2_role_id'] ?? '');
    }

    private function loadSettings(): array
    {
        $path = storage_path('app/settings.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?? [];
        }
        return [];
    }

    private function saveSettings(array $data): void
    {
        $path     = storage_path('app/settings.json');
        $existing = $this->loadSettings();
        file_put_contents($path, json_encode(array_merge($existing, $data), JSON_PRETTY_PRINT));
    }

    #[Computed]
    public function roles()
    {
        return Role::query()->orderBy('name')->get(['id', 'name', 'description']);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->withCount('vehicles', 'users')->orderBy('name')->get();
    }

    #[Computed]
    public function vehicleTypes()
    {
        return VehicleType::query()->withCount('vehicles')->orderBy('name')->get();
    }

    // ── Company ───────────────────────────────────────────────────────────────
    public function saveCompany(): void
    {
        $this->validate([
            'companyName'    => ['required', 'string', 'max:100'],
            'companyAddress' => ['nullable', 'string', 'max:500'],
            'companyPhone'   => ['nullable', 'string', 'max:30'],
            'companyEmail'   => ['nullable', 'email', 'max:100'],
            'companyWebsite' => ['nullable', 'url', 'max:100'],
        ]);

        $this->saveSettings([
            'company_name'    => $this->companyName,
            'company_address' => $this->companyAddress,
            'company_phone'   => $this->companyPhone,
            'company_email'   => $this->companyEmail,
            'company_website' => $this->companyWebsite,
        ]);

        ActivityLogger::log('settings', 'update', 'Update company profile');
        Flux::toast('Company profile disimpan.');
    }

    // ── Approval config ───────────────────────────────────────────────────────
    public function saveApprovalConfig(): void
    {
        $this->validate([
            'approvalLevel1RoleId' => ['nullable', 'exists:roles,id'],
            'approvalLevel2RoleId' => ['nullable', 'exists:roles,id'],
        ]);

        $this->saveSettings([
            'approval_level1_role_id' => $this->approvalLevel1RoleId,
            'approval_level2_role_id' => $this->approvalLevel2RoleId,
            'require_level_2'         => $this->requireLevel2,
        ]);

        ActivityLogger::log('settings', 'update', 'Update approval configuration');
        Flux::toast('Konfigurasi approval disimpan.');
    }

    // ── Region CRUD ───────────────────────────────────────────────────────────
    public function openRegionForm(?int $id = null): void
    {
        if ($id) {
            $r = Region::query()->findOrFail($id);
            $this->regionId   = $r->id;
            $this->regionName = $r->name;
            $this->regionCode = $r->code;
            $this->regionDesc = (string) ($r->description ?? '');
        } else {
            $this->regionId   = null;
            $this->regionName = '';
            $this->regionCode = '';
            $this->regionDesc = '';
        }
        $this->activeRegionModal = 'form';
    }

    public function saveRegion(): void
    {
        $isUpdate = $this->regionId !== null;
        $validated = $this->validate([
            'regionName' => ['required', 'string', 'max:100'],
            'regionCode' => ['required', 'string', 'max:20', 'unique:regions,code,' . ($this->regionId ?? 'NULL') . ',id'],
            'regionDesc' => ['nullable', 'string', 'max:255'],
        ]);

        Region::query()->updateOrCreate(
            ['id' => $this->regionId],
            ['name' => $validated['regionName'], 'code' => $validated['regionCode'], 'description' => $validated['regionDesc'] ?: null]
        );

        ActivityLogger::log('settings', $isUpdate ? 'update' : 'create', ($isUpdate ? 'Update' : 'Tambah') . ' region: ' . $validated['regionName']);
        $this->activeRegionModal = '';
        unset($this->regions);
        Flux::toast($isUpdate ? 'Region diperbarui.' : 'Region ditambahkan.');
    }

    public function deleteRegion(int $id): void
    {
        $region = Region::query()->findOrFail($id);
        ActivityLogger::log('settings', 'delete', 'Hapus region: ' . $region->name);
        $region->delete();
        unset($this->regions);
        Flux::toast('Region dihapus.');
    }

    // ── VehicleType CRUD ──────────────────────────────────────────────────────
    public function openVehicleTypeForm(?int $id = null): void
    {
        if ($id) {
            $vt = VehicleType::query()->findOrFail($id);
            $this->vehicleTypeId   = $vt->id;
            $this->vehicleTypeName = $vt->name;
            $this->vehicleTypeDesc = (string) ($vt->description ?? '');
        } else {
            $this->vehicleTypeId   = null;
            $this->vehicleTypeName = '';
            $this->vehicleTypeDesc = '';
        }
        $this->activeVehicleTypeModal = 'form';
    }

    public function saveVehicleType(): void
    {
        $isUpdate  = $this->vehicleTypeId !== null;
        $validated = $this->validate([
            'vehicleTypeName' => ['required', 'string', 'max:100', 'unique:vehicle_types,name,' . ($this->vehicleTypeId ?? 'NULL') . ',id'],
            'vehicleTypeDesc' => ['nullable', 'string', 'max:255'],
        ]);

        VehicleType::query()->updateOrCreate(
            ['id' => $this->vehicleTypeId],
            ['name' => $validated['vehicleTypeName'], 'description' => $validated['vehicleTypeDesc'] ?: null]
        );

        ActivityLogger::log('settings', $isUpdate ? 'update' : 'create', ($isUpdate ? 'Update' : 'Tambah') . ' jenis kendaraan: ' . $validated['vehicleTypeName']);
        $this->activeVehicleTypeModal = '';
        unset($this->vehicleTypes);
        Flux::toast($isUpdate ? 'Jenis kendaraan diperbarui.' : 'Jenis kendaraan ditambahkan.');
    }

    public function deleteVehicleType(int $id): void
    {
        $vt = VehicleType::query()->findOrFail($id);
        ActivityLogger::log('settings', 'delete', 'Hapus jenis kendaraan: ' . $vt->name);
        $vt->delete();
        unset($this->vehicleTypes);
        Flux::toast('Jenis kendaraan dihapus.');
    }

    public function render()
    {
        return view('pages.setting.index');
    }
}
