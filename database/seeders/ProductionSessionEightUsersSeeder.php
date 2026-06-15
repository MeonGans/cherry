<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductionSessionEightUsersSeeder extends Seeder
{
    private const SESSION_ID = 8;
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
            ['name' => 'Дмитренко Ілля', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Дробінська Марта', 'desired_team' => 'Вода', 'gender' => 'female'],
            ['name' => 'Качалов Марк', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Денисенко Артем', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Діденко Давид', 'desired_team' => 'Вода', 'gender' => 'male'],
            ['name' => 'Тихоненко Роман', 'desired_team' => 'Вода', 'gender' => 'male'],

            ['name' => 'Павловський Роман', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Любивий Назар', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Щербак Арсеній', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Коваленко Ярослав', 'desired_team' => 'Вогонь', 'gender' => 'male'],
            ['name' => 'Бойко Марія', 'desired_team' => 'Вогонь', 'gender' => 'female'],

            ['name' => 'Санжаровський Віталій', 'desired_team' => 'Повітря', 'gender' => 'male'],
            ['name' => 'Горяєв Іван', 'desired_team' => 'Повітря', 'gender' => 'male'],
            ['name' => 'Сушко Катя', 'desired_team' => 'Повітря', 'gender' => 'female'],
            ['name' => 'Гулько Віктор', 'desired_team' => 'Повітря', 'gender' => 'male'],
            ['name' => 'Новохацький Станіслав', 'desired_team' => 'Повітря', 'gender' => 'male'],

            ['name' => 'Маковей Леонід', 'desired_team' => 'Земля', 'gender' => 'male'],
            ['name' => 'Омельченко Даніїл', 'desired_team' => 'Земля', 'gender' => 'male'],
            ['name' => 'Полупан Діана', 'desired_team' => 'Земля', 'gender' => 'female'],
            ['name' => 'Руденко Євгеній', 'desired_team' => 'Земля', 'gender' => 'male'],
            ['name' => 'Тепленко Ігор', 'desired_team' => 'Земля', 'gender' => 'male'],
            ['name' => 'Богатиренко Іван', 'desired_team' => 'Земля', 'gender' => 'male'],

            ['name' => 'Іванова Софія', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Марчук Софія', 'desired_team' => 'Метал', 'gender' => 'female'],
            ['name' => 'Ярцев Тимур', 'desired_team' => 'Метал', 'gender' => 'male'],
            ['name' => 'Балдін Олександр', 'desired_team' => 'Метал', 'gender' => 'male'],
            ['name' => 'Бойко Саша', 'desired_team' => 'Метал', 'gender' => 'male'],
            ['name' => 'Остапенко Денис', 'desired_team' => 'Метал', 'gender' => 'male'],
        ];
    }
}
