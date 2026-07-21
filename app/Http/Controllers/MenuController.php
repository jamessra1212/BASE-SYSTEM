<?php

namespace App\Http\Controllers;

use App\DataTables\Menu\MenuDataTable;
use App\DataTables\Menu\SubmenuDataTable;
use App\Http\Requests\MenuFormRequest;
use App\Models\Menu;
use App\Services\BackEnd\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(MenuDataTable $dataTable)
    {
        return $dataTable->render('BackEnd.content.menu.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('BackEnd.content.menu.extras.entry');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuFormRequest $request): JsonResponse
    {
        $valData = $request->validated();
        $menu = $this->menuService->create($valData);

        return response()->json([
            'message' => 'Menu created successfully',
            'data' => $menu,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubmenuDataTable $dataTable, string $slug)
    {
        return $dataTable->setSlug($slug)->render('BackEnd.content.menu.extras.subMenus');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        return view('BackEnd.content.menu.extras.edit')->with([
            'menu' => $this->menuService->findbySlug($slug)
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, menu $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(menu $menu)
    {
        //
    }
}
