@extends('layouts.install')
@section('title', 'Installation/Update')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <br/><br/>

            <div class="box box-primary active">
                <!-- /.box-header -->
                <div class="box-body">

              @if(session('error'))
                <div class="alert alert-danger">
                    {!! session('error') !!}
                </div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                  </ul>
                </div>
              @endif

              <form class="form" id="details_form" method="post" 
                      action="{{$action_url}}">
                    {{ csrf_field() }}

                    <h2> License Details - <code>{{$module_display_name}} Module</code><br/><small class="text-danger"> Make sure to provide correct licensing information</small></h2>
                    <hr/>

                    @if($intruction_type == 'cc')
                        @include('install.partials.e_license')
                    @endif

                    <div class="col-md-12">
                        <button type="submit" id="install_button" class="btn btn-primary pull-right">I Agree, SUBMIT</button>
                    </div>
              </form>
            </div>
          <!-- /.box-body -->
          </div>

            
        </div>

    </div>
</div>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function(){
      $('form#details_form').submit(function(){
        $('button#install_button').attr('disabled', true).text('Installing...');
        $('div.install_msg').removeClass('hide');
        $('.back_button').hide();
      });
    })
  </script>
@endsection
