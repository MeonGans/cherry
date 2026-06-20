<?php

namespace App\Http\Controllers;

use App\Models\WednesdayQuestRoute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WednesdayQuestRouteController extends Controller
{
    public function index()
    {
        $questRoutes = WednesdayQuestRoute::latest()->get();

        return view('wednesday-quest-routes.index', compact('questRoutes'));
    }

    public function create()
    {
        return view('wednesday-quest-routes.create', [
            'questRoute' => new WednesdayQuestRoute(),
        ]);
    }

    public function store(Request $request)
    {
        WednesdayQuestRoute::create($this->validatedRouteData($request));

        return redirect()
            ->route('wednesday-quest-routes.index')
            ->with('success', 'Маршрут квесту створено.');
    }

    public function edit(WednesdayQuestRoute $wednesdayQuestRoute)
    {
        return view('wednesday-quest-routes.edit', [
            'questRoute' => $wednesdayQuestRoute,
        ]);
    }

    public function update(Request $request, WednesdayQuestRoute $wednesdayQuestRoute)
    {
        $wednesdayQuestRoute->update($this->validatedRouteData($request, $wednesdayQuestRoute));

        return redirect()
            ->route('wednesday-quest-routes.index')
            ->with('success', 'Маршрут квесту оновлено.');
    }

    public function destroy(WednesdayQuestRoute $wednesdayQuestRoute)
    {
        $wednesdayQuestRoute->delete();

        return redirect()
            ->route('wednesday-quest-routes.index')
            ->with('success', 'Маршрут квесту видалено.');
    }

    private function validatedRouteData(Request $request, ?WednesdayQuestRoute $questRoute = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'victim_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wednesday_quest_routes', 'victim_code')->ignore($questRoute?->id),
            ],
        ];

        foreach (WednesdayQuestRoute::HINT_NUMBERS as $number) {
            $rules["hint_{$number}"] = ['required', 'string'];
        }

        $data = $request->validate($rules, [
            'name.required' => 'Вкажіть назву маршруту.',
            'victim_code.required' => 'Вкажіть код жертви.',
            'victim_code.unique' => 'Такий код жертви вже використовується.',
            'hint_*.required' => 'Заповніть усі підказки маршруту.',
        ]);

        $data['name'] = trim($data['name']);
        $data['victim_code'] = trim($data['victim_code']);

        foreach (WednesdayQuestRoute::HINT_NUMBERS as $number) {
            $data["hint_{$number}"] = trim($data["hint_{$number}"]);
        }

        return $data;
    }
}
