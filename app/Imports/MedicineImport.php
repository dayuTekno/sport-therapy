<?php

namespace App\Imports;

use App\Models\MasterMedicine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicineImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = strtolower(trim($row['nama_obat'])); // case-insensitive + trim

        $exists = \App\Models\MasterMedicine::whereRaw('LOWER(medicine_name) = ?', [$name])->exists();

        if ($exists) {
            return null;
        }

        return new \App\Models\MasterMedicine([
            'medicine_name' => $row['nama_obat'],
            'medicine_international_name' => $row['nama_obat_internasional'] ?? null,
        ]);
    }
}