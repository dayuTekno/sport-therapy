<?php

namespace App\Imports;

use App\Models\MasterICD;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ICDImport implements ToModel, WithHeadingRow
{
    protected $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public function model(array $row)
    {
        $code = isset($row['code']) ? trim((string)$row['code']) : null;

        if (!$code) {
            return null;
        }

        return new MasterICD([
            'icd_code' => $code,
            'category' => $this->category,
            'name' => $row['display'] ?? null,
            'version' => $row['version'] ?? null,
        ]);
    }
}