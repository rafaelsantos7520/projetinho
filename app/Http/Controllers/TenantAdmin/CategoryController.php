<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('name')->get();
        return view('tenant_admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('tenant_admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        return view('tenant_admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria removida com sucesso!');
    }
}
