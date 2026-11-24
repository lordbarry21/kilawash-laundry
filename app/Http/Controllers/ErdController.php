<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ErdController extends Controller
{
    public function index(): View
    {
        $migrationFiles = glob(database_path('migrations/*.php')) ?: [];
        sort($migrationFiles);

        $migrations = [];
        foreach ($migrationFiles as $path) {
            $name = basename($path);
            if (str_contains($name, 'cache') || str_contains($name, 'jobs')) {
                continue;
            }
            $migrations[] = [
                'name' => $name,
                'code' => trim((string) file_get_contents($path)),
            ];
        }

        return view('erd', compact('migrations'));
    }
}
