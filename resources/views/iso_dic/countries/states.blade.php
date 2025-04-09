<table class="table table-hover basic-datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>اسم المدينة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($states as $state)
        <tr>
            <td>{{ $state->id }}</td>
            <td>{{ $state->name_ar }}</td>
            <td>
                <a href="" class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></a>
                <form action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')"><i class="ti ti-trash"></i></button>
                </form>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>