<?php

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = Label::getColors();

        $labels = [
            [
                'name'  => 'Duplicate',
                'color' => $colors['default'],
            ],
            [
                'name'  => 'In Progress',
                'color' => $colors['info'],
            ],
            [
                'name'  => 'Question',
                'color' => $colors['info'],
            ],
            [
                'name'  => 'Working on it',
                'color' => $colors['warning'],
            ],
            [
                'name'  => 'Bug',
                'color' => $colors['danger'],
            ],
            [
                'name'  => 'Critical',
                'color' => $colors['danger'],
            ],
        ];

        foreach ($labels as $label) {
            Label::firstOrCreate($label);
        }
    }
}
