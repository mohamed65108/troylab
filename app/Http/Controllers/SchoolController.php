<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Http\Resources\SchoolCollection;
use App\Http\Resources\SchoolResource;
use App\Models\School;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new SchoolCollection(School::paginate(25));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreSchoolRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchoolRequest $request)
    {
        $school = School::create($request->validated());

        return new SchoolResource($school);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        return new SchoolResource($school);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateSchoolRequest $request
     * @param \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        $school->update($request->validated());

        return new SchoolResource($school);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        $school->delete();

        return response()->json(null, 204);
    }
}
