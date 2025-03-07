<form action="{{route('iso_dic.procedures.saveConfigure',$purposes->id)}}" method="POST" id="form-purpose">
    @csrf
    <div class="row align-items-center pb-2">
        <h3 class="col">{{ __('purpose') }}</h3>
    </div>


    <div class="mb-3">
        <table class="table" id="dynamic-table-purpose">
            <thead>
                <tr>
                    <th style="width: 50px;">التسلسل</th>
                    <th>المحتوى</th>
                    <th style="width: 50px;">
                        <button type="button" class="btn btn-sm btn-success add-row px-3" data-tab="purpose">+</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purposes->content as $index => $row)
                <tr>
                    <td style="width: 50px;">
                        <input type="text" name="content[{{ $index }}][sequence]" class="form-control" readonly value="{{ $row['sequence'] }}">
                    </td>
                    <td>
                        <textarea name="content[{{ $index }}][value]" class="form-control" rows="1">{{ $row['value'] }}</textarea>
                    </td>
                    <td style="width: 50px;">
                        <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-info col-auto text-start float-end save-and-continue"  data-next-tab="scope">حفظ واستمرار</button>

    <button type="submit" class="btn btn-info">حفظ</button>
</form>