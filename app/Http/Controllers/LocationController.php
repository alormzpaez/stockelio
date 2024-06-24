<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Locations/Create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * By default, the system only accepts shippings to Mexico.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $user = $request->user()->loadCount('locations');

        $isPreferred = ($user->locations_count == 0);

        $user->locations()->create(array_merge(
            $request->validated(), 
            [
                'country_name' => 'Mexico',
                'country_code' => 'MX',
                'is_preferred' => $isPreferred,
            ]
        ));

        return to_route('profile.edit');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location): Response
    {
        Gate::authorize('update', $location);

        return Inertia::render('Locations/Edit', [
            'location' => Arr::only($location->toArray(), [
                'id',
                'user_id',
                'state_name',
                'city',
                'locality',
                'address',
                'zip',
                'phone',
                'is_preferred',
                'full_address',
            ])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        Gate::authorize('update', $location);

        if ($request->has('is_preferred')) {
            $user = $request->user();

            $user->locations()->whereNot('id', $location->id)->update([
                'is_preferred' => false,
            ]);
        }

        $location->update($request->validated());

        return to_route('profile.edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        Gate::authorize('delete', $location);

        $location->delete();

        $user = Auth::user()->loadCount('locations');

        if ($user->locations_count > 0) {
            $user->locations()
                ->first()
            ->update([
                'is_preferred' => true,
            ]);
        }

        return to_route('profile.edit');
    }
}
