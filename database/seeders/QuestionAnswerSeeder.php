<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionAnswerSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            'Який колір вам подобається?' => [
                'Помаранчевий' => 1, // Вогонь
                'Білий' => 2, // Повітря
                'Синій' => 3, // Вода
                'Зелений' => 4, // Земля
            ],
            'Який вид спорту вам найбільше подобається?' => [
                'Легка атлетика' => 1, // Вогонь
                'Плавання' => 3, // Вода
                'Альпінізм' => 4, // Земля
                'Парапланеризм' => 2, // Повітря
            ],
            'Яка ваша улюблена пора року?' => [
                'Літо' => 1, // Вогонь
                'Осінь' => 4, // Земля
                'Весна' => 2, // Повітря
                'Зима' => 3, // Вода
            ],
            'Який ваш улюблений тип погоди?' => [
                'Сонячна' => 1, // Вогонь
                'Дощова' => 3, // Вода
                'Вітряна' => 2, // Повітря
                'Хмарна' => 4, // Земля
            ],
            'Який ваш улюблений елемент природи?' => [
                'Вогонь' => 1, // Вогонь
                'Земля' => 4, // Земля
                'Повітря' => 2, // Повітря
                'Вода' => 3, // Вода
            ],
            'Яке місце для відпочинку вам більше до вподоби?' => [
                'Пляж' => 3, // Вода
                'Гори' => 4, // Земля
                'Ліс' => 1, // Вогонь
                'Озеро' => 2, // Повітря
            ],
            'Яка ваша улюблена тварина?' => [
                'Лев' => 1, // Вогонь
                'Слон' => 4, // Земля
                'Орел' => 2, // Повітря
                'Риба' => 3, // Вода
            ],
            'Який ваш улюблений вид відпочинку?' => [
                'Активний відпочинок' => 1, // Вогонь
                'Медитація' => 2, // Повітря
                'Йога' => 4, // Земля
                'Риболовля' => 3, // Вода
            ],
            'Який ваш улюблений жанр музики?' => [
                'Рок' => 1, // Вогонь
                'Класика' => 4, // Земля
                'Джаз' => 2, // Повітря
                'Поп' => 3, // Вода
            ],
            'Яке ваше улюблене хобі?' => [
                'Малювання' => 4, // Земля
                'Садівництво' => 1, // Вогонь
                'Політ на дельтаплані' => 2, // Повітря
                'Плавання' => 3, // Вода
            ],
            'Який ваш улюблений фрукт?' => [
                'Апельсин' => 1, // Вогонь
                'Яблуко' => 4, // Земля
                'Виноград' => 2, // Повітря
                'Банан' => 3, // Вода
            ],
            'Який ваш улюблений вид мистецтва?' => [
                'Малювання' => 4, // Земля
                'Скульптура' => 1, // Вогонь
                'Фотографія' => 2, // Повітря
                'Музика' => 3, // Вода
            ],
            'Яка ваша улюблена книга або жанр літератури?' => [
                'Фентезі' => 1, // Вогонь
                'Наукова фантастика' => 4, // Земля
                'Біографії' => 2, // Повітря
                'Детективи' => 3, // Вода
            ],
            'Який ваш улюблений час доби?' => [
                'Ранок' => 1, // Вогонь
                'День' => 4, // Земля
                'Вечір' => 2, // Повітря
                'Ніч' => 3, // Вода
            ],
            'Який ваш улюблений вид подорожі?' => [
                'Літаком' => 2, // Повітря
                'Потягом' => 4, // Земля
                'Автомобілем' => 1, // Вогонь
                'Кораблем' => 3, // Вода
            ],
            'Який ваш улюблений напій?' => [
                'Кава' => 1, // Вогонь
                'Чай' => 4, // Земля
                'Сік' => 2, // Повітря
                'Вода' => 3, // Вода
            ],
            'Який ваш улюблений вид кухні?' => [
                'Італійська' => 1, // Вогонь
                'Японська' => 4, // Земля
                'Мексиканська' => 2, // Повітря
                'Індійська' => 3, // Вода
            ],
            'Який ваш улюблений вид діяльності на природі?' => [
                'Пікнік' => 4, // Земля
                'Похід' => 1, // Вогонь
                'Катання на велосипеді' => 2, // Повітря
                'Спостереження за птахами' => 3, // Вода
            ],
            'Який ваш улюблений святковий сезон?' => [
                'Новий рік' => 1, // Вогонь
                'Великдень' => 4, // Земля
                'Літні канікули' => 2, // Повітря
                'Осінній фестиваль' => 3, // Вода
            ],
            'Який ваш улюблений вид фільму?' => [
                'Комедія' => 1, // Вогонь
                'Драма' => 4, // Земля
                'Трилер' => 2, // Повітря
                'Анімація' => 3, // Вода
            ],
        ];

        foreach ($questions as $question => $answers) {
            $questionModel = Question::create(['question' => $question]);

            foreach ($answers as $answer => $teamId) {
                Answer::create([
                    'question_id' => $questionModel->id,
                    'answer' => $answer,
                    'team_id' => $teamId,
                ]);
            }
        }
    }
}