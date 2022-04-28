<?php

namespace App\Exports;

use App\Models\User;
use App\Models\AdminUsersLogs;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Carbon\Carbon;

class UsersLogsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{

    use Exportable;
    public $count;

    // protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }

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

        if ($this->id != "-1") {
            return AdminUsersLogs::join('users', 'users.id', '=', 'admin_users_logs.user_id')
                ->select('admin_users_logs.*', 'users.name', 'users.email', 'users.username')
                ->where('admin_users_logs.user_id', $this->id)
                ->get();
        } else {
            return AdminUsersLogs::join('users', 'users.id', '=', 'admin_users_logs.user_id')
                ->select('admin_users_logs.*', 'users.name', 'users.email', 'users.username')
                ->get();
        }
    }

    public function headings(): array
    {
        return ["No", "Name", "E-mail", "Description", "Data / Time";
    }


    public function map($user): array
    {
        $this->count++;
        return [
            $this->count,
            $user->name,
            $user->email,
            $user->description,
            Carbon::parse($user->created_at)->isoFormat('HH:mm - MMM Do YYYY '),

        ];
    }
}
