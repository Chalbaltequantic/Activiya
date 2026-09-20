<?php

namespace Database\Seeders;

use App\Models\PoTemplate;
use Illuminate\Database\Seeder;

class PoTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'metro',
                'name' => 'Metro Cash & Carry',
                'parser_code' => 'metro',
                'status' => true,
            ],
            [
                'code' => 'myntra',
                'name' => 'Myntra',
                'parser_code' => 'myntra',
                'status' => true,
            ],
            [
                'code' => 'citymall',
                'name' => 'CityMall',
                'parser_code' => 'citymall',
                'status' => true,
            ],
            [
                'code' => 'walmart',
                'name' => 'Walmart',
                'parser_code' => 'walmart',
                'status' => true,
            ],
        ];

        foreach ($templates as $template) {
            PoTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}