<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            ['title' => 'Preparar propuesta del proyecto', 'description' => 'Definir alcance y entregables.', 'status' => 'in_progress', 'priority' => 'high', 'due_date' => now()->addDays(2)->toDateString()],
            ['title' => 'Revisar documentación', 'description' => 'Validar requisitos y pendientes.', 'status' => 'pending', 'priority' => 'medium', 'due_date' => now()->addDays(5)->toDateString()],
            ['title' => 'Enviar resumen semanal', 'description' => 'Compartir avances con el equipo.', 'status' => 'completed', 'priority' => 'low', 'due_date' => null],
        ];

        foreach ($examples as $example) {
            Activity::create($example);
        }
    }
}
