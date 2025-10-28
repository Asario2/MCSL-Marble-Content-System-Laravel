@php
    use App\Http\Controllers\FormController;
@endphp
@foreach ($posts as $row)
    <tr data-id="{{ $row->id }}" class='table-row'>
        <td>{{ $row->id }}</td>
        <td>

            @if (CheckRights(Auth::id(), $table, 'edit'))
                <a class="btn btn-primary btn-sm"
                    href="{{ route('tables.edit', ['table' => $table, 'id' => $row->id]) }}">Bearbeiten</a>
            @endif
            @if (CheckRights(Auth::id(), $table, 'delete'))
                <form class='inl' action="{{ route('tables.destroy', ['table' => $table, 'id' => $row->id]) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="cursor-pointer btn btn-danger btn-sm" type="submit"
                        onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');">Löschen</button>
                </form>
            @endif

        </td>

        @foreach ($columns as $column)
            {!! FormController::selectFieldText($column, $row->$column, $table, '', $row->id) !!}
        @endforeach

        {{-- @php
//                 \Log::info('rid:' . $row->id);
            @endphp --}}


    </tr>
    <script>
        upload_files({{ $row->id }});
        select_form({{ $row->id }});
        text_form({{ $row->id }});
        init_date();
        dpicker_hide({{ $row->id }})
        // date_form({{ $row->id }});
    </script>
@endforeach
