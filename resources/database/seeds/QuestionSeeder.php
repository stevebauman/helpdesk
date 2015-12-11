<?php

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = Question::getDefault();

        foreach ($questions as $question) {
            Question::firstOrCreate([
                'content' => $question,
            ]);
        }
    }
}
