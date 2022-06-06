<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    public function getMenu()
    {
        return Cache::remember('menu', 60, function () {
            return Menu::all()->groupBy('category');
        });
    }

    public function updateMenu(Menu $menu, $data)
    {
        $menu->update($data);

        Cache::forget('menu');
    }

    public function storeMenu($data)
    {
        $menu = Menu::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'weight' => $data['weight'] ?? null,
            'category' => $data['category'],
            'slug' => str_slug($data['name']),
        ]);

        Cache::forget('menu');

        return $menu;
    }

    public function deleteMenu(Menu $menu)
    {
        $menu->delete();

        Cache::forget('menu');
    }

    public function importMenu()
    {
        $this->prepareTable();

        $menu_items = (new MenuParser)->getParseMenu();

        foreach ($menu_items as $category => $items)
        {
            foreach ($items as $item) {
                $menu = new Menu();
                $menu->category = $category;
                $menu->name = $item[0];
                $menu->weight = $item[1];
                $menu->price = (int) $item[2];
                $menu->slug = str_slug($item[0]);
                $menu->save();
            }
        }
    }

    protected function prepareTable()
    {
        $old_menu = Menu::all()->pluck('id')->toArray();

        Menu::destroy($old_menu);

        Cache::forget('menu');
    }
}