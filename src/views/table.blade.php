
@extends('calculator::layout.layout')

@section('main-content')
<section class="content">
    <div class="row">
      <div class="col-12">
        <!-- /.card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title" style="text-transform: capitalize">{{$table}} data</h3>
            <a href="/cms/createEntry/{{$table}}" class="btn btn-app" style="float:right">
                <i class="fas fa-plus-circle"></i>
                Add</a>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Actions</th>
                @foreach($columns as $column)
                <th>{{$column['column_display_name']}}</th>
                 @endforeach
              </tr>
              </thead>
              <tbody>

                @foreach($results as $result)
                <tr>
                <td>
                <a href="/cms/edit/{{$table}}/{{$result['id']}}" style="margin:0 5px">Edit</a> 
                    <a href="/cms/delete/{{$table}}/{{$result['id']}}" style="margin:0 5px">Delete</a>
                </td>
            @foreach($columns as $column)
                @if($column['column_type'] == "image")
                  <td>
                     <div style="height: 100px;
                     width: 100px;
                     margin: 0 auto;
                     background-repeat: no-repeat;
                     background-position: center center;
                     background-size: cover;
                     background-image: url({{URL::to('public'. $result[$column['column_name']] == '' ? 'Not set' : $result[$column['column_name']])}})" >
                     </div>
                    </td>
                @else
                  <td>{{$result[$column['column_name']] == '' ? 'Not set' : $result[$column['column_name']] }}</td>
                @endif
            @endForeach
                </tr>
          @endForeach
             
           
              </tbody>
              <tfoot>
              <tr>
                  <th>Actions</th>
                 @foreach($columns as $column)
                <th>{{$column['column_display_name']}}</th>
                 @endforeach
              </tr>
              </tfoot>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
@endsection