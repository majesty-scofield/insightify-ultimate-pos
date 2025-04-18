<?php

namespace App\Http\Controllers;

use App\Models\GroupSubTax;
use App\Models\TaxRate;
use App\Utils\TaxUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxRateController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $taxUtil;

    /**
     * Constructor
     *
     * @param  TaxUtil  $taxUtil
     * @return void
     */
    public function __construct(TaxUtil $taxUtil)
    {
        $this->taxUtil = $taxUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('tax_rate.view') && ! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $tax_rates = TaxRate::where('business_id', $business_id)
                        ->where('is_tax_group', '0')
                        ->select(['name', 'amount', 'id', 'for_tax_group']);

            return Datatables::of($tax_rates)
                ->addColumn(
                    'action',
                    '@can("tax_rate.update")
                    <button data-href="{{action(\'App\Http\Controllers\TaxRateController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary edit_tax_rate_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("tax_rate.delete")
                        <button data-href="{{action(\'App\Http\Controllers\TaxRateController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_tax_rate_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('name', '@if($for_tax_group == 1) {{$name}} <small>(@lang("lang_v1.for_tax_group_only"))</small> @else {{$name}} @endif')
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->removeColumn('for_tax_group')
                ->removeColumn('id')
                ->rawColumns([0, 2])
                ->make(false);
        }

        return view('tax_rate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('tax_rate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'amount']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['amount'] = $this->taxUtil->num_uf($input['amount']);
            $input['for_tax_group'] = ! empty($request->for_tax_group) ? 1 : 0;

            $tax_rate = TaxRate::create($input);
            $output = ['success' => true,
                'data' => $tax_rate,
                'msg' => __('tax_rate.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $tax_rate = TaxRate::where('business_id', $business_id)->find($id);

            return view('tax_rate.edit')
                ->with(compact('tax_rate'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'amount']);
                $business_id = $request->session()->get('user.business_id');

                $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);
                $tax_rate->name = $input['name'];
                $tax_rate->amount = $this->taxUtil->num_uf($input['amount']);
                $tax_rate->for_tax_group = ! empty($request->for_tax_group) ? 1 : 0;
                $tax_rate->save();

                //update group tax amount
                $group_taxes = GroupSubTax::where('tax_id', $id)
                                            ->get();

                foreach ($group_taxes as $group_tax) {
                    $this->taxUtil->updateGroupTaxAmount($group_tax->group_tax_id);
                }

                $output = ['success' => true,
                    'msg' => __('tax_rate.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('tax_rate.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //update group tax amount
                $group_taxes = GroupSubTax::where('tax_id', $id)
                                            ->get();
                if ($group_taxes->isEmpty()) {
                    $business_id = request()->user()->business_id;

                    $tax_rate = TaxRate::where('business_id', $business_id)->findOrFail($id);
                    $tax_rate->delete();

                    $output = ['success' => true,
                        'msg' => __('tax_rate.deleted_success'),
                    ];
                } else {
                    $output = ['success' => false,
                        'msg' => __('tax_rate.can_not_be_deleted'),
                    ];
                }
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
