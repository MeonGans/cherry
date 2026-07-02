<?php

namespace App\Http\Controllers;

use App\Models\MusicClipCard;
use App\Models\Product;
use App\Models\Session as CampSession;
use App\Models\Vote;
use App\Models\WednesdayQuestRoute;
use App\Models\ZootopiaQuestRoute;
use Illuminate\Database\QueryException;
use Throwable;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        try {
            $activeSession = CampSession::withCount('users')
                ->where('active', true)
                ->orderByDesc('start_date')
                ->first();

            return view('admin.dashboard', [
                'databaseUnavailable' => false,
                'activeSession' => $activeSession,
                'recentSessions' => CampSession::withCount('users')
                    ->orderByDesc('active')
                    ->orderByDesc('start_date')
                    ->limit(4)
                    ->get(),
                'latestVotes' => Vote::orderByDesc('created_at')->limit(4)->get(),
                'stats' => [
                    'activeStudents' => $activeSession?->users_count ?? 0,
                    'sessions' => CampSession::count(),
                    'products' => Product::count(),
                    'votes' => Vote::count(),
                    'clipCards' => MusicClipCard::count(),
                    'questRoutes' => WednesdayQuestRoute::count() + ZootopiaQuestRoute::count(),
                ],
            ]);
        } catch (Throwable $exception) {
            if (!$exception instanceof QueryException) {
                report($exception);
            }

            return view('admin.dashboard', [
                'databaseUnavailable' => true,
                'activeSession' => null,
                'recentSessions' => collect(),
                'latestVotes' => collect(),
                'stats' => [
                    'activeStudents' => null,
                    'sessions' => null,
                    'products' => null,
                    'votes' => null,
                    'clipCards' => null,
                    'questRoutes' => null,
                ],
            ]);
        }
    }
}
