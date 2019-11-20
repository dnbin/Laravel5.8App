<table
        class="table table-sm"
        id="SearchesTable"
        style="width:100%"
        data-url="{{route('searches.list')}}"
>    <thead>
    @include('searches.table_columns')
    </thead>
    <tbody></tbody>
    <tfoot>
    @include('searches.table_columns')
    </tfoot>
</table>