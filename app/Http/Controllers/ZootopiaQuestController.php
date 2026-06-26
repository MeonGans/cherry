<?php

namespace App\Http\Controllers;

use App\Models\ZootopiaQuestRoute;
use Illuminate\Http\Request;

class ZootopiaQuestController extends Controller
{
    public function index()
    {
        return view('zootopia-quest.index');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'agent_code' => ['required', 'string', 'max:255'],
        ], [
            'agent_code.required' => 'Введіть код агента.',
        ]);

        $agentCode = ZootopiaQuestRoute::normalizeCode($request->input('agent_code'));
        $questRoute = ZootopiaQuestRoute::where('agent_code', $agentCode)->first();

        if (!$questRoute) {
            return back()
                ->withInput()
                ->withErrors(['agent_code' => 'Код агента не знайдено в базі Зоотрополіса.']);
        }

        return redirect()->route('zootopia.quest.route', $questRoute);
    }

    public function route(ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        return $this->showRoute(
            $zootopiaQuestRoute,
            session($this->hintSessionKey($zootopiaQuestRoute)),
            (bool) session($this->finalSessionKey($zootopiaQuestRoute), false)
        );
    }

    public function redirectFromHint(ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        return redirect()->route('zootopia.quest.route', $zootopiaQuestRoute);
    }

    public function hint(Request $request, ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        $request->validate([
            'step_code' => ['required', 'string', 'max:255'],
        ], [
            'step_code.required' => 'Введіть код доступу.',
        ]);

        $stepCode = ZootopiaQuestRoute::normalizeCode($request->input('step_code'));

        if ($stepCode === ZootopiaQuestRoute::normalizeCode(ZootopiaQuestRoute::FINAL_CODE)) {
            session([
                $this->hintSessionKey($zootopiaQuestRoute) => null,
                $this->finalSessionKey($zootopiaQuestRoute) => true,
            ]);

            return redirect()->route('zootopia.quest.route', $zootopiaQuestRoute);
        }

        $hintNumber = ZootopiaQuestRoute::hintNumberForCode($stepCode);

        if (!$hintNumber) {
            return back()
                ->withInput()
                ->withErrors(['step_code' => 'Невірний код доступу.']);
        }

        session([
            $this->hintSessionKey($zootopiaQuestRoute) => $hintNumber,
            $this->finalSessionKey($zootopiaQuestRoute) => false,
        ]);

        return redirect()->route('zootopia.quest.route', $zootopiaQuestRoute);
    }

    private function showRoute(
        ZootopiaQuestRoute $questRoute,
        ?int $hintNumber = null,
        bool $isFinal = false
    ) {
        return view('zootopia-quest.route', [
            'questRoute' => $questRoute,
            'hintNumber' => $hintNumber,
            'hintText' => $hintNumber ? $questRoute->hintFor($hintNumber) : null,
            'isFinal' => $isFinal,
        ]);
    }

    private function hintSessionKey(ZootopiaQuestRoute $questRoute): string
    {
        return "zootopia_quest.{$questRoute->getKey()}.hint";
    }

    private function finalSessionKey(ZootopiaQuestRoute $questRoute): string
    {
        return "zootopia_quest.{$questRoute->getKey()}.final";
    }
}
