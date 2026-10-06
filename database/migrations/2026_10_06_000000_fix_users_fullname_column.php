<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * concat() returns NULL as soon as any part is NULL, so every user without
 * a middle initial ended up with an empty fullname. concat_ws() skips NULLs.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY fullname VARCHAR(255) AS (concat_ws(' ', fname, NULLIF(minitial, ''), lname)) VIRTUAL");
    }

    public function down(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY fullname VARCHAR(255) AS (concat(fname, " ", minitial, " ", lname)) VIRTUAL');
    }
};
