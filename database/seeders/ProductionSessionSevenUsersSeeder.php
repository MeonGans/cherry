<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductionSessionSevenUsersSeeder extends Seeder
{
    private const SESSION_ID = 7;
    private const LICEUM_ID = 1;
    private const DEFAULT_PHONE_NUMBER = '';
    private const DEFAULT_DATE_OF_BIRTH = '2010-01-01';

    public function run(): void
    {
        $this->ensureSessionExists();
        $this->ensureLiceumExists();

        $students = $this->students();
        $teamIds = $this->teamIds(array_unique(array_column($students, 'desired_team')));
        $now = now();
        $createdCount = 0;
        $updatedCount = 0;

        DB::transaction(function () use ($students, $teamIds, $now, &$createdCount, &$updatedCount): void {
            foreach ($students as $student) {
                $desiredTeamId = $teamIds[$student['desired_team']];

                $existingUser = DB::table('users')
                    ->where('session_id', self::SESSION_ID)
                    ->where('name', $student['name'])
                    ->first();

                if ($existingUser) {
                    DB::table('users')
                        ->where('id', $existingUser->id)
                        ->update([
                            'desired_team_id' => $desiredTeamId,
                            'team_id' => null,
                            'gender' => $student['gender'],
                            'updated_at' => $now,
                        ]);

                    $updatedCount++;
                    continue;
                }

                DB::table('users')->insert([
                    'name' => $student['name'],
                    'session_id' => self::SESSION_ID,
                    'phone_number' => self::DEFAULT_PHONE_NUMBER,
                    'date_of_birth' => self::DEFAULT_DATE_OF_BIRTH,
                    'liceum_id' => self::LICEUM_ID,
                    'team_id' => null,
                    'desired_team_id' => $desiredTeamId,
                    'gender' => $student['gender'],
                    'pin_code' => $this->generateUniquePinCode(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $createdCount++;
            }
        });

        $this->command?->info("Created users: {$createdCount}");
        $this->command?->info("Updated users: {$updatedCount}");
    }

    private function ensureSessionExists(): void
    {
        $session = DB::table('sessions')->where('id', self::SESSION_ID)->first();

        if (!$session) {
            throw new RuntimeException('Session with id ' . self::SESSION_ID . ' was not found.');
        }

        if (!(bool) $session->active) {
            throw new RuntimeException('Session with id ' . self::SESSION_ID . ' exists but is not active.');
        }
    }

    private function ensureLiceumExists(): void
    {
        $liceumExists = DB::table('liceums')->where('id', self::LICEUM_ID)->exists();

        if (!$liceumExists) {
            throw new RuntimeException('Liceum with id ' . self::LICEUM_ID . ' was not found.');
        }
    }

    /**
     * @param array<int, string> $teamNames
     * @return array<string, int>
     */
    private function teamIds(array $teamNames): array
    {
        $teamIds = [];
        $missingTeams = [];
        $teamsHaveSessionId = DB::getSchemaBuilder()->hasColumn('teams', 'session_id');

        foreach ($teamNames as $teamName) {
            $query = DB::table('teams')->where('name', $teamName);

            if ($teamsHaveSessionId) {
                $team = (clone $query)->where('session_id', self::SESSION_ID)->first();

                if (!$team) {
                    $team = $query->orderBy('id')->first();
                }
            } else {
                $team = $query->orderBy('id')->first();
            }

            if (!$team) {
                $missingTeams[] = $teamName;
                continue;
            }

            $teamIds[$teamName] = (int) $team->id;
        }

        if ($missingTeams !== []) {
            throw new RuntimeException('Missing teams: ' . implode(', ', $missingTeams));
        }

        return $teamIds;
    }

    private function generateUniquePinCode(): string
    {
        do {
            $pinCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('users')->where('pin_code', $pinCode)->exists());

        return $pinCode;
    }

    /**
     * @return array<int, array{name: string, desired_team: string, gender: string}>
     */
    private function students(): array
    {
        return [
            ['name' => 'Мельник Оксана', 'desired_team' => 'Земля', 'gender' => 'female'],
            ['name' => 'Уляна Риженко', 'desired_team' => 'Земля', 'gender' => 'female'],
            ['name' => 'Цвіткова Вероніка', 'desired_team' => 'Земля', 'gender' => 'female'],
            ['name' => 'Демедюк Анастасія', 'desired_team' => 'Земля', 'gender' => 'female'],
            ['name' => 'Ярмоленко Марк', 'desired_team' => 'Земля', 'gender' => 'male'],
            ['name' => 'Плевако Дмитро', 'desired_team' => 'Земля', 'gender' => 'male'],

            ['name' => 'Петренко Тімур', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Касянчук Станіслав', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Донець Ангеліна', 'desired_team' => 'Вода', 'gender' => 'female'],
            ['name' => 'Ревізюк Анна', 'desired_team' => 'Вода', 'gender' => 'female'],
            ['name' => 'Гринишина Аліна', 'desired_team' => 'Вода', 'gender' => 'female'],
            ['name' => 'Михайленко Вероніка', 'desired_team' => 'Вода', 'gender' => 'female'],

            ['name' => 'Бердніков Тимур', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Єрмакова Анна', 'desired_team' => 'Вогонь', 'gender' => 'female'],
            ['name' => 'Акбаш Євгеній', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Набожна Кіра', 'desired_team' => 'Вогонь', 'gender' => 'female'],
            ['name' => 'Іванюк Міла', 'desired_team' => 'Вогонь', 'gender' => 'female'],
            ['name' => 'Соколюк Даша', 'desired_team' => 'Вогонь', 'gender' => 'female'],

            ['name' => 'Морозова Єва', 'desired_team' => 'Повітря', 'gender' => 'female'],
            ['name' => 'Орел Софія', 'desired_team' => 'Повітря', 'gender' => 'female'],
            ['name' => 'Діхтяр Владислав', 'desired_team' => 'Повітря', 'gender' => 'male'],
            ['name' => 'Блищик Захар', 'desired_team' => 'Повітря', 'gender' => 'male'],
            ['name' => 'Заливанська Анастасія', 'desired_team' => 'Повітря', 'gender' => 'female'],
            ['name' => 'Клепус Поліна', 'desired_team' => 'Повітря', 'gender' => 'female'],

            ['name' => 'Петренко Валерія', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Чубук Єлизавета', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Мацола Мілана', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Винокур Олександра', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Карпиш Іван', 'desired_team' => 'Метал', 'gender' => 'male'],
            ['name' => 'Степура Ілля', 'desired_team' => 'Метал', 'gender' => 'male'],
        ];
    }
}
