@extends('cms::layout.layout') 
@section('main-content')

<section class="content">
    <div class="container-fluid">

        <ul>
            @foreach ($errors->all() as $error)
                <li style="color:red">{{ $error }}</li>
            @endforeach
       </ul>
    
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title" style="text-transform: capitalize;">{{$table}}</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                    <form method="POST" action="/cms/{{$action}}/{{$table}}" enctype="multipart/form-data">
                            @csrf
                        @foreach($fields as $k => $field)
                            @if($field->field_type == 'foreign')
                            {{-- foreign --}}
                            <div class="form-group">
                                <label>{{$field->display_name}}</label>
                            <select class="form-control select2bs4" style="width: 100%;" name="{{$field->field_name}}">
                                     <option value="">Select value</option>
                                    @foreach($field->foreign_values as $foreign)
                                    <option value="{{$foreign['id']}}" {{ $field->field_value == $foreign['id'] || old($field->field_name) == $foreign['id'] ? "selected" : "" }}>{{$foreign[$field->display_foreign_field]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                        {{-- date --}}
                        @if($field->field_type == 'date')
                        <div class="form-group">
                            <label>{{$field->display_name}}</label>
                            <div class='input-group date'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type='text' class="form-control datetimepicker"  name="{{$field->field_name}}" value="{{old($field->field_name, $field->field_value ? $field->field_value : "")}}"/>
                            </div>
                        </div>
                        @endif
                        

                        @if($field->field_type == 'datetime')
                        <div class="form-group">
                            <label>{{$field->display_name}}</label>
                            <div class='input-group date'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type='text' class="form-control datetimepicker"  name="{{$field->field_name}}" value="{{old($field->field_name, $field->field_value ? $field->field_value : "")}}"/>
                            </div>
                        </div>
                        @endif

                        
                        @if($field->field_type == 'textfield')
                        {{-- textfield --}}
                        <div class="form-group">
                            <label>{{$field->display_name}}</label>
                            <input class="form-control" type="text" placeholder="Default input" value="{{old($field->field_name, $field->field_value ? $field->field_value : "")}}" name="{{$field->field_name}}"/>
                        </div>
                        @endif

                        @if($field->field_type == 'checkbox')
                        {{-- checkbox --}}
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{old($field->field_name, $field->field_value ? $field->field_value : "")}}" checked name="{{$field->field_name}}"/>
                                <label class="form-check-label">{{$field->display_name}}</label>
                            </div>
                        </div>
                        @endif

                        @if($field->field_type == 'image')
                        {{-- image upload --}}
                        <div class="form-group">
                            <label for="customFile">{{$field->display_name}}</label>

                            <div class="custom-file">
                                <input type="file" class="custom-file-input" value="{{old($field->field_name, $field->field_value ? $field->field_value : "")}}" id="customFile" name="{{$field->field_name}}"/>
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        @endif

                        @if($field->field_type == 'textarea')
                        {{-- textarea --}}
                        <div class="form-group">
                            <label for="customFile">{{$field->display_name}}</label>

                            <div class="mb-3">
                            <textarea class="textarea" placeholder="Place some text here"
                                        style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="{{$field->field_name}}">{{old($field->field_name, $field->field_value ? $field->field_value : "")}}</textarea>
                            </div>
                    </div>

                    
                    @endif
                    @endforeach
                        <input type="hidden" name="table" value="{{$table}}"/>
                        @if($action == 'update')
                        <input type="hidden" name="entry" value="{{$entryId}}"/>
                        @endif
                        <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.card-body -->

    </div>
    <!-- /.card -->

</section>
@endsection