@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')
@include('superadmin::layouts.nav')
<!-- Main content -->
<section class="content">

	  {{-- <div class="box box-solid">
      <div class="box-header">
        	<h3 class="box-title">@lang( 'superadmin::lang.add_new_business' ) <small>(@lang( 'superadmin::lang.add_business_help' ))</small></h3>
        </div>

        <div class="box-body">
                {!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\BusinessController::class, 'store']), 'method' => 'post', 'id' => 'business_register_form','files' => true ]) !!}
                    @include('business.partials.register_form')
                    <div class="clearfix"></div>
                    <div class="col-md-12"><hr></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('package_id', __( 'superadmin::lang.subscription_packages' ) . ':') !!}
                            {!! Form::select('package_id', $packages, null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('paid_via', __( 'superadmin::lang.paid_via' ) . ':') !!}
                            {!! Form::select('paid_via', $gateways, null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('payment_transaction_id', __( 'superadmin::lang.payment_transaction_id' ) . ':') !!}
                            {!! Form::text('payment_transaction_id', null, ['class' => 'form-control', 'placeholder' => __( 'superadmin::lang.payment_transaction_id' ) ]); !!}
                         </div>
                    </div>

                <div class="col-md-12 text-center">
                    {!! Form::submit(__('messages.submit'), ['class' => 'btn btn-success btn-big']) !!}
                </div>
                    
                {!! Form::close() !!}
        </div> --}}


        <div
	class="tw-transition-all lg:tw-col-span-1 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md  tw-ring-gray-200">
	<div class="tw-p-4 sm:tw-p-5">
		<div class="tw-flex tw-justify-start tw-gap-2.5">
			<h3 class="box-title">@lang( 'superadmin::lang.add_new_business' ) <small>(@lang( 'superadmin::lang.add_business_help' ))</small></h3>
		</div>
		<div class="tw-flow-root tw-mt-5 tw-border-b tw-border-gray-200">
			<div class="tw-mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
				<div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
					{!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\BusinessController::class, 'store']), 'method' => 'post', 'id' => 'business_register_form','files' => true ]) !!}
                    @include('business.partials.register_form')
                    <div class="clearfix"></div>
                    <div class="col-md-12"><hr></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('package_id', __( 'superadmin::lang.subscription_packages' ) . ':') !!}
                            {!! Form::select('package_id', $packages, null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('paid_via', __( 'superadmin::lang.paid_via' ) . ':') !!}
                            {!! Form::select('paid_via', $gateways, null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('payment_transaction_id', __( 'superadmin::lang.payment_transaction_id' ) . ':') !!}
                            {!! Form::text('payment_transaction_id', null, ['class' => 'form-control', 'placeholder' => __( 'superadmin::lang.payment_transaction_id' ) ]); !!}
                         </div>
                    </div>

                <div class="col-md-12 text-center">
                    {!! Form::submit(__('messages.submit'), ['class' => 'tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg']) !!}
                </div>
                    
                {!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
    </div>

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection


@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();
            $("form#business_register_form").validate({
                errorPlacement: function(error, element) {
                    if(element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    name: "required",
                    email: {
                        email: true,
                        remote: {
                            url: "/business/register/check-email",
                            type: "post",
                            data: {
                                email: function() {
                                    return $( "#email" ).val();
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        equalTo: "#password"
                    },
                    paid_via: {
                        required: function(element){
                                return $('#package_id').val() != '';
                            }
                    },
                    username: {
                        required: true,
                        minlength: 4,
                        remote: {
                            url: "/business/register/check-username",
                            type: "post",
                            data: {
                                username: function() {
                                    return $( "#username" ).val();
                                }
                            }
                        }
                    }
                },
                messages: {
                    name: LANG.specify_business_name,
                    password: {
                        minlength: LANG.password_min_length,
                    },
                    confirm_password: {
                        equalTo: LANG.password_mismatch
                    },
                    username: {
                        remote: LANG.invalid_username
                    },
                    email: {
                        remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
                    }
                }
            });

            $("#business_logo").fileinput({'showUpload':false, 'showPreview':false, 'browseLabel': LANG.file_browse_label, 'removeLabel': LANG.remove});
        });
    </script>
@endsection