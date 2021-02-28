<?php

namespace App\Http\Controllers;

use App\Http\Resources\YearModulesCollection;
use App\Models\Module;
use Illuminate\Http\Request;

class YearsModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $modules = Module::all();
        $years = [];

        foreach ($modules as $module) {
            if (isset($years[$module->year])) {
                $years[$module->year][] = $module;
            } else {
                $years[$module->year] =  [$module];
            }
        }
        return (new YearModulesCollection($years))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
