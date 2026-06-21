<?php

namespace App\Http\Controllers;

use App\Models\WednesdayQuestRoute;
use Illuminate\Http\Request;

class WednesdayQuestController extends Controller
{
    public function index()
    {
        return view('wednesday-quest.index');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'victim_code' => ['required', 'string', 'max:255'],
        ], [
            'victim_code.required' => 'Введіть код жертви.',
        ]);

        $victimCode = trim($request->input('victim_code'));
        $questRoute = WednesdayQuestRoute::where('victim_code', $victimCode)->first();

        if (!$questRoute) {
            return back()
                ->withInput()
                ->withErrors(['victim_code' => 'Код жертви не знайдено.']);
        }

        return redirect()->route('wednesday.quest.route', $questRoute);
    }

    public function route(WednesdayQuestRoute $wednesdayQuestRoute)
    {
        return $this->showRoute(
            $wednesdayQuestRoute,
            session($this->hintSessionKey($wednesdayQuestRoute)),
            (bool) session($this->finalSessionKey($wednesdayQuestRoute), false)
        );
    }

    public function redirectFromHint(WednesdayQuestRoute $wednesdayQuestRoute)
    {
        return redirect()->route('wednesday.quest.route', $wednesdayQuestRoute);
    }

    public function hint(Request $request, WednesdayQuestRoute $wednesdayQuestRoute)
    {
        $request->validate([
            'step_code' => ['required', 'string', 'max:20'],
        ], [
            'step_code.required' => 'Введіть код підказки.',
        ]);

        $stepCode = trim($request->input('step_code'));

        if ($stepCode === WednesdayQuestRoute::SAFE_CODE) {
            session([
                $this->hintSessionKey($wednesdayQuestRoute) => null,
                $this->finalSessionKey($wednesdayQuestRoute) => true,
            ]);

            return redirect()->route('wednesday.quest.route', $wednesdayQuestRoute);
        }

        $hintNumber = array_flip(WednesdayQuestRoute::HINT_CODES)[$stepCode] ?? null;

        if (!$hintNumber) {
            return back()
                ->withInput()
                ->withErrors(['step_code' => 'Невірний код підказки.']);
        }

        session([
            $this->hintSessionKey($wednesdayQuestRoute) => $hintNumber,
            $this->finalSessionKey($wednesdayQuestRoute) => false,
        ]);

        return redirect()->route('wednesday.quest.route', $wednesdayQuestRoute);
    }

    private function showRoute(
        WednesdayQuestRoute $questRoute,
        ?int $hintNumber = null,
        bool $isFinal = false
    ) {
        return view('wednesday-quest.route', [
            'questRoute' => $questRoute,
            'hintNumber' => $hintNumber,
            'hintText' => $hintNumber ? $questRoute->hintFor($hintNumber) : null,
            'isFinal' => $isFinal,
        ]);
    }

    private function hintSessionKey(WednesdayQuestRoute $questRoute): string
    {
        return "wednesday_quest.{$questRoute->getKey()}.hint";
    }

    private function finalSessionKey(WednesdayQuestRoute $questRoute): string
    {
        return "wednesday_quest.{$questRoute->getKey()}.final";
    }
}
