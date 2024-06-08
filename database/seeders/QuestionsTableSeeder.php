<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionsTableSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            [
                'question' => 'Який колір вам подобається?',
                'answers' => [
                    ['answer' => 'Помаранчевий', 'team_id' => 1],
                    ['answer' => 'Білий', 'team_id' => 2],
                    ['answer' => 'Синій', 'team_id' => 3],
                    ['answer' => 'Зелений', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Яка ваша улюблена пора року?',
                'answers' => [
                    ['answer' => 'Літо', 'team_id' => 1],
                    ['answer' => 'Зима', 'team_id' => 2],
                    ['answer' => 'Осінь', 'team_id' => 3],
                    ['answer' => 'Весна', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Яка ваша улюблена стихія?',
                'answers' => [
                    ['answer' => 'Вогонь', 'team_id' => 1],
                    ['answer' => 'Повітря', 'team_id' => 2],
                    ['answer' => 'Вода', 'team_id' => 3],
                    ['answer' => 'Земля', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Який ваш улюблений вид відпочинку?',
                'answers' => [
                    ['answer' => 'Барбекю на природі', 'team_id' => 1],
                    ['answer' => 'Політ на параплані', 'team_id' => 2],
                    ['answer' => 'Плавання', 'team_id' => 3],
                    ['answer' => 'Походи в гори', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Який ваш улюблений вид спорту?',
                'answers' => [
                    ['answer' => 'Футбол', 'team_id' => 1],
                    ['answer' => 'Йога', 'team_id' => 2],
                    ['answer' => 'Плавання', 'team_id' => 3],
                    ['answer' => 'Скелелазіння', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Який ваш улюблений час дня?',
                'answers' => [
                    ['answer' => 'Полудень', 'team_id' => 1],
                    ['answer' => 'Ранок', 'team_id' => 2],
                    ['answer' => 'Вечір', 'team_id' => 3],
                    ['answer' => 'Світанок', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Який ваш улюблений вид транспорту?',
                'answers' => [
                    ['answer' => 'Автомобіль', 'team_id' => 1],
                    ['answer' => 'Літак', 'team_id' => 2],
                    ['answer' => 'Корабель', 'team_id' => 3],
                    ['answer' => 'Поїзд', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Яка ваша улюблена їжа?',
                'answers' => [
                    ['answer' => 'Гостра їжа', 'team_id' => 1],
                    ['answer' => 'Салати', 'team_id' => 2],
                    ['answer' => 'Супи', 'team_id' => 3],
                    ['answer' => 'М\'ясо на грилі', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Яка ваша улюблена тварина?',
                'answers' => [
                    ['answer' => 'Лев', 'team_id' => 1],
                    ['answer' => 'Орел', 'team_id' => 2],
                    ['answer' => 'Дельфін', 'team_id' => 3],
                    ['answer' => 'Ведмідь', 'team_id' => 4],
                ],
            ],
            [
                'question' => 'Який ваш улюблений напій?',
                'answers' => [
                    ['answer' => 'Кава', 'team_id' => 1],
                    ['answer' => 'Чай', 'team_id' => 2],
                    ['answer' => 'Вода', 'team_id' => 3],
                    ['answer' => 'Сік', 'team_id' => 4],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::create(['question' => $questionData['question']]);
            foreach ($questionData['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'team_id' => $answerData['team_id'],
                ]);
            }
        }
    }
}
