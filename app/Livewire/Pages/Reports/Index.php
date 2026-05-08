<?php

namespace App\Livewire\Pages\Reports;

use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Models\VehicleService;
use App\Models\VehicleUsage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    public string $reportType  = 'booking';
    public string $filterYear  = '';
    public string $filterMonth = '';
    public string $filterVehicleId = '';

    public function mount(): void
    {
        $this->filterYear  = (string) now()->year;
        $this->filterMonth = '';
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()->orderBy('code')->get(['id', 'code', 'plate_number']);
    }

    #[Computed]
    public function reportData()
    {
        return match ($this->reportType) {
            'booking'     => $this->bookingReport(),
            'fuel'        => $this->fuelReport(),
            'service'     => $this->serviceReport(),
            'usage'       => $this->usageReport(),
            default       => collect(),
        };
    }

    private function bookingReport()
    {
        return VehicleBooking::query()
            ->with(['requester:id,name', 'vehicle:id,code,plate_number', 'driver:id,name'])
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('created_at', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('created_at', $this->filterMonth))
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->orderByDesc('created_at')
            ->get();
    }

    private function fuelReport()
    {
        return FuelLog::query()
            ->with(['vehicle:id,code,plate_number', 'filledBy:id,name'])
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('fuel_date', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('fuel_date', $this->filterMonth))
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->orderByDesc('fuel_date')
            ->get();
    }

    private function serviceReport()
    {
        return VehicleService::query()
            ->with('vehicle:id,code,plate_number')
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('service_date', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('service_date', $this->filterMonth))
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->orderByDesc('service_date')
            ->get();
    }

    private function usageReport()
    {
        return VehicleUsage::query()
            ->with(['vehicle:id,code,plate_number', 'driver:id,name', 'booking:id,booking_code,purpose'])
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('actual_departure', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('actual_departure', $this->filterMonth))
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->orderByDesc('actual_departure')
            ->get();
    }

    public function exportCsv(): StreamedResponse
    {
        $data    = $this->reportData;
        $type    = $this->reportType;
        $headers = $this->csvHeaders($type);
        $rows    = $this->csvRows($type, $data);
        $filename = $type . '_report_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function csvHeaders(string $type): array
    {
        return match ($type) {
            'booking' => ['Booking Code', 'Pemohon', 'Kendaraan', 'Plat', 'Driver', 'Keperluan', 'Tujuan', 'Tgl Berangkat', 'Tgl Kembali', 'Status'],
            'fuel'    => ['Tanggal', 'Kendaraan', 'Plat', 'Liter', 'Harga/Liter', 'Total Biaya', 'Odometer', 'Diisi Oleh', 'Catatan'],
            'service' => ['Tanggal', 'Kendaraan', 'Plat', 'Jenis Service', 'Bengkel', 'Biaya', 'Odometer', 'Next Service', 'Catatan'],
            'usage'   => ['Booking Code', 'Kendaraan', 'Plat', 'Driver', 'Km Awal', 'Km Akhir', 'Total Jarak', 'Berangkat', 'Kembali', 'Catatan'],
            default   => [],
        };
    }

    private function csvRows(string $type, $data): array
    {
        return match ($type) {
            'booking' => $data->map(fn($b) => [
                $b->booking_code,
                $b->requester?->name ?? '-',
                $b->vehicle?->code ?? '-',
                $b->vehicle?->plate_number ?? '-',
                $b->driver?->name ?? '-',
                $b->purpose,
                $b->destination,
                $b->departure_date?->format('d/m/Y H:i'),
                $b->return_date?->format('d/m/Y H:i'),
                ucfirst($b->status),
            ])->toArray(),
            'fuel' => $data->map(fn($f) => [
                $f->fuel_date?->format('d/m/Y'),
                $f->vehicle?->code ?? '-',
                $f->vehicle?->plate_number ?? '-',
                $f->liter,
                $f->price_per_liter,
                $f->total_cost,
                $f->odometer ?? '-',
                $f->filledBy?->name ?? '-',
                $f->notes ?? '',
            ])->toArray(),
            'service' => $data->map(fn($s) => [
                $s->service_date?->format('d/m/Y'),
                $s->vehicle?->code ?? '-',
                $s->vehicle?->plate_number ?? '-',
                $s->service_type,
                $s->workshop_name ?? '-',
                $s->cost,
                $s->odometer ?? '-',
                $s->next_service_date?->format('d/m/Y') ?? '-',
                $s->notes ?? '',
            ])->toArray(),
            'usage' => $data->map(fn($u) => [
                $u->booking?->booking_code ?? '-',
                $u->vehicle?->code ?? '-',
                $u->vehicle?->plate_number ?? '-',
                $u->driver?->name ?? '-',
                $u->start_odometer ?? '-',
                $u->end_odometer ?? '-',
                $u->total_distance ?? '-',
                $u->actual_departure?->format('d/m/Y H:i') ?? '-',
                $u->actual_return?->format('d/m/Y H:i') ?? '-',
                $u->notes ?? '',
            ])->toArray(),
            default => [],
        };
    }

    public function render()
    {
        return view('pages.reports.index');
    }
}
