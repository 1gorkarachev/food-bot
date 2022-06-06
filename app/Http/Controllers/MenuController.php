<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\MenuImportRequest;
use App\Http\Requests\Menu\StoreRequest;
use App\Http\Requests\Menu\UpdateRequest;
use App\Services\Converter\Contracts\Converter;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(MenuService $service)
    {
        $menu = $service->getMenu();

        return view('orders.menu', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $categories = DB::table('menu')->select('category')->distinct()->get();

        return view('orders.menu-edit', compact('menu', 'categories'));
    }

    public function update(UpdateRequest $request, Menu $menu, MenuService $service)
    {
        $data = $request->validated();

        $service->updateMenu($menu, $data);

        return redirect()->route('menu.index')->with('edited_item', $menu->id);
    }

    public function create()
    {
        $categories = DB::table('menu')->select('category')->distinct()->get();

        return view('orders.menu-create', compact('categories'));
    }

    public function store(StoreRequest $request, MenuService $service)
    {
        $data = $request->validated();

        $menu = $service->storeMenu($data);

        return redirect()->route('menu.index')->with('edited_item', $menu->id);
    }

    public function destroy(Menu $menu, MenuService $service)
    {
        $service->deleteMenu($menu);

        return redirect()->route('menu.index');
    }

    public function uploadMenu(MenuImportRequest $request, MenuService $menuService, Converter $converter)
    {
        $file = Arr::get($request->validated(), 'file');
        $store = Storage::putFileAs('', $file, 'menu.'.$file->extension());

        if (!$store) {
            return back()->with('error', 'Error saving file!');
        }

        if ($file->extension() == "pdf") {
            $convert = $converter->convert(storage_path('app/menu.pdf'));

            if (!$convert) {
                return back()->with('error', 'Error convertation file!');
            }
        }

        $menuService->importMenu();

        return back()->with('success', 'All good!');
    }
}
