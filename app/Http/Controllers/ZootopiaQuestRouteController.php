<?php

namespace App\Http\Controllers;

use App\Models\ZootopiaQuestRoute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZootopiaQuestRouteController extends Controller
{
    public function index()
    {
        $questRoutes = ZootopiaQuestRoute::latest()->get();

        return view('zootopia-quest-routes.index', compact('questRoutes'));
    }

    public function create()
    {
        return view('zootopia-quest-routes.create', [
            'questRoute' => new ZootopiaQuestRoute(),
        ]);
    }

    public function store(Request $request)
    {
        ZootopiaQuestRoute::create($this->validatedRouteData($request));

        return redirect()
            ->route('zootopia-quest-routes.index')
            ->with('success', 'Маршрут квесту створено.');
    }

    public function edit(ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        return view('zootopia-quest-routes.edit', [
            'questRoute' => $zootopiaQuestRoute,
        ]);
    }

    public function update(Request $request, ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        $zootopiaQuestRoute->update($this->validatedRouteData($request, $zootopiaQuestRoute));

        return redirect()
            ->route('zootopia-quest-routes.index')
            ->with('success', 'Маршрут квесту оновлено.');
    }

    public function destroy(ZootopiaQuestRoute $zootopiaQuestRoute)
    {
        $zootopiaQuestRoute->delete();

        return redirect()
            ->route('zootopia-quest-routes.index')
            ->with('success', 'Маршрут квесту видалено.');
    }

    private function validatedRouteData(Request $request, ?ZootopiaQuestRoute $questRoute = null): array
    {
        $request->merge([
            'agent_code' => ZootopiaQuestRoute::normalizeCode($request->input('agent_code')),
        ]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'agent_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zootopia_quest_routes', 'agent_code')->ignore($questRoute?->id),
            ],
        ];

        foreach (ZootopiaQuestRoute::HINT_NUMBERS as $number) {
            $rules["hint_{$number}"] = ['required', 'string'];
        }

        $data = $request->validate($rules, [
            'name.required' => 'Вкажіть назву маршруту.',
            'agent_code.required' => 'Вкажіть код агента.',
            'agent_code.unique' => 'Такий код агента вже використовується.',
            'hint_*.required' => 'Заповніть усі підказки маршруту.',
        ]);

        $data['name'] = trim($data['name']);
        $data['agent_code'] = ZootopiaQuestRoute::normalizeCode($data['agent_code']);

        foreach (ZootopiaQuestRoute::HINT_NUMBERS as $number) {
            $data["hint_{$number}"] = trim($data["hint_{$number}"]);
        }

        return $data;
    }
}
