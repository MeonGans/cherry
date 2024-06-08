<?php

namespace App\Http\Controllers;

use App\Models\Liceum;
use Illuminate\Http\Request;

class LiceumController extends Controller
{
    public function index()
    {
        $liceums = Liceum::all();
        return view('liceums.index', compact('liceums'));
    }

    public function create()
    {
        return view('liceums.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Liceum::create($request->all());
        return redirect()->route('liceums.index')->with('success', 'Liceum created successfully.');
    }

    public function show(Liceum $liceum)
    {
        return view('liceums.show', compact('liceum'));
    }

    public function edit(Liceum $liceum)
    {
        return view('liceums.edit', compact('liceum'));
    }

    public function update(Request $request, Liceum $liceum)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255'
        ]);

        $liceum->update($request->all());
        return redirect()->route('liceums.index')->with('success', 'Liceum updated successfully.');
    }

    public function destroy(Liceum $liceum)
    {
        $liceum->delete();
        return redirect()->route('liceums.index')->with('success', 'Liceum deleted successfully.');
    }
}
