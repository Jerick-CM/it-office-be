<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class UsersExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{

    use Exportable;
    public $count;

    public function styles(Worksheet $sheet)
    {
        return [

            // Style the first row as bold text.
            // 1    => ['font' => ['bold' => true]],
            // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
            // Styling a specific cell by coordinate.
            'A1' => ['font' => ['bold' => true]],
            'B1' => ['font' => ['bold' => true]],
            'C1' => ['font' => ['bold' => true]],
            'D1' => ['font' => ['bold' => true]],
            'E1' => ['font' => ['bold' => true]],
            'F1' => ['font' => ['bold' => true]],
            'G1' => ['font' => ['bold' => true]],
            'H1' => ['font' => ['bold' => true]],

        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 45,
            'C' => 45,
            'D' => 45,
            'E' => 45,
            'F' => 45,

        ];
    }


    public function collection()
    {
        return User::select('id', 'name', 'username', 'email', 'is_admin', 'created_at')->get();
    }

    public function headings(): array
    {
        return ["No", "Name", "Username", "E-mail", "Admin Account", "Data / Time"];
    }

    public function map($user): array
    {
        $this->count++;
        return [
            $this->count,
            // $user->id,
            $user->name,
            $user->username,
            $user->email,
            $user->is_admin ? 'Yes' : 'No',
            Carbon::parse($user->created_at)->isoFormat('HH:mm - MMM Do YYYY '),
        ];
    }
}
