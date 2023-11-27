<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Chirp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('chirps.index', [
            'chirps' => Chirp::with('user')->where('user_id', Auth::id())->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $request->user()->chirps()->create($validated);

        return redirect(route('chirps.index'));
    }
        /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($validated);

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return redirect(route('chirps.index'));
    }

    public function all_chirps(): View
    {
        return view('chirps.all_chirps', [
            'chirps' => Chirp::with('user')->latest()->get(),
        ]);
    }

    public function search(Request $request): View
    {
        $keyword = $request->input('message');
        if(!empty($keyword)) {
            $chirps = Chirp::where('message', 'like', "%{$keyword}%")->with('user')->latest()->get();
            // $companies->where('company_name', 'LIKE', "%{$keyword}%")
            // ->orwhereHas('products', function ($query) use ($keyword) {
            //     $query->where('product_name', 'LIKE', "%{$keyword}%");
            // })->get();
        }
        else{
            $validated = $request->validate([
                'message' => 'required',
            ]);
        }

        return view('chirps.all_chirps', [
            'chirps' => $chirps
        ]);
    }
}
