@extends('orders.layouts.order')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class='card-title'>Редактирование</h3>
        </div>
        <div class="card-body row">
            <form method="POST" action="{{ route('menu.update', $menu) }}" class="col-6">
                @method('PUT')
                @csrf
                <fieldset>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Название</label>
                        <input type="text" id="textInput" class="form-control" name="name" value="{{ $menu->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="textInput" class="form-label">Количество</label>
                        <input type="text" id="textInput" class="form-control" name="weight" value="{{ $menu->weight }}">
                    </div>
                    <div class="mb-3">
                        <label for="numberInput" class="form-label">Стоимость</label>
                        <input type="number" id="numberInput" class="form-control" name="price" value="{{ $menu->price }}">
                    </div>
                    <div class="mb-3">
                        <label for="textSelect" class="form-label">Категория</label>
                        <select id="textSelect" class="form-select" name="category">
                            @foreach($categories as $category)
                                <option value="{{ $category->category }}" {{ $menu->category == $category->category ? 'selected' : ''}}>
                                    {{ $category->category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-end">
                    <button class="btn btn-success px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection