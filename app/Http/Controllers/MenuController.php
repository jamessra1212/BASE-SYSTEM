<?php

namespace App\Http\Controllers;

use App\DataTables\Menu\MenuDataTable;
use App\DataTables\Menu\SubmenuDataTable;
use App\Http\Requests\MenuFormRequest;
use App\Models\Menu;
use App\Models\Submenu;
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
    public function index(MenuDataTable $menuDataTable, SubmenuDataTable $submenuDataTable)
    {
        return $menuDataTable->render('BackEnd.content.menu.index',compact('submenuDataTable'));
    }

    // public function submenus(Request $request, string $slug)
    // {
    //     $menu = Menu::where('menu_id', $slug)->firstOrFail();

    //     return datatables()
    //         ->eloquent(
    //             Submenu::where('x_menu_id', $slug)
    //         )
    //         ->addColumn('action', function ($row) {
    //             return view(
    //                 'BackEnd.content.menu.Submenu.su_action',
    //                 compact('row')
    //             );
    //         })
    //         ->rawColumns(['action'])
    //         ->toJson();
    // }

    public function submenus(SubmenuDataTable $datatable, string $slug)
    {
        return $datatable
            ->filter('menu', $slug)
            ->make();
    }

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
    public function show()
    {
        //
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
