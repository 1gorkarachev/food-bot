@extends('orders.layouts.order')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>
                <a href="https://www.pdf2go.com/ru/pdf-to-word" target="_blank">
                    Если файл не импортируется в формате PDF!
                </a>
            </h5>
            @if (session('success'))
                <div class="alert alert-success mb-2">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-2">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('menu.upload-menu') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="formFile" class="form-label">Выберите файл для импорта меню (pdf, docx):</label>
                <div class="row">
                    <div class="col-10">
                        <input class="form-control" type="file" id="formFile" name="file">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success">Импорт</button>
                    </div>
                </div>
            </form>
            @if ($errors->any())
                <div class="alert alert-danger mt-2">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class='card-title mx-1'>Меню</h3>
                <a class="btn btn-success rounded-circle btn-sm" title="Добавить позицию" href="{{ route('menu.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="mb-1 bi bi-plus-lg" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                    </svg>
                </a>
            </div>
            <a class="btn btn-success" href="{{ route('orders.send-message', \App\Models\Order::MENU_MESSAGE) }}">
                Отправить в чат
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telegram mb-1" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
                </svg>
            </a>
        </div>
        <div class="card-body">
            @if(!$menu->isEmpty())
                @foreach($menu as $key => $value)
                    <h4>{{ $key }}</h4>
                    <hr>
                    <ol>
                        @foreach($value as $menuItem)
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="{{ session('edited_item') && session('edited_item') == $menuItem->id ? 'border border-success rounded bg-success' : '' }}">
                                        {{ $menuItem->menu_item }}
                                    </div>
                                    <a href="{{ route('menu.edit', $menuItem) }}" title="Редактировать">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-pencil-square mb-1 mx-1" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('menu.destroy', $menuItem) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn" type="submit" title="Удалить">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-lg mb-1 text-danger" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    <hr>
                @endforeach
            @else
                <div class="alert alert-info mt-2">
                    <h3>Menu is empty</h3>
                </div>
            @endif
        </div>
    </div>
@endsection