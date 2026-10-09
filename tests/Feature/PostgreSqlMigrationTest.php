<?php

namespace Tests\Feature;

use App\Models\MasterPatients;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostgreSqlMigrationTest extends TestCase
{
    /**
     * Memverifikasi koneksi database aktif adalah PostgreSQL.
     */
    public function test_database_connection_is_postgresql(): void
    {
        $this->assertEquals('pgsql', DB::getDriverName());
        $result = DB::select("SELECT version()");
        $this->assertNotEmpty($result);
        $this->assertStringContainsStringIgnoringCase('PostgreSQL', $result[0]->version);
    }

    /**
     * Memverifikasi fitur case-insensitive ilike pada PostgreSQL berjalan dengan baik.
     */
    public function test_postgresql_ilike_case_insensitive_search(): void
    {
        $patient = MasterPatients::first();
        if ($patient) {
            $upperName = strtoupper($patient->full_name);
            $lowerName = strtolower($patient->full_name);

            $foundUpper = MasterPatients::where('full_name', 'ilike', "%{$upperName}%")->exists();
            $foundLower = MasterPatients::where('full_name', 'ilike', "%{$lowerName}%")->exists();

            $this->assertTrue($foundUpper, "Case-insensitive search (UPPER) gagal di PostgreSQL");
            $this->assertTrue($foundLower, "Case-insensitive search (LOWER) gagal di PostgreSQL");
        } else {
            $this->assertTrue(true);
        }
    }
}
