<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\PurchaseLine;
use App\Models\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Console\Command;

class MapPurchaseSell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:mapPurchaseSell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete existing mapping and Add mapping for purchase & Sell for all transactions of all businesses.';

    protected $transactionUtil;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        parent::__construct();

        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            DB::beginTransaction();

            // Refresh database:
            // ==================
            // 1. Set variation_location_details as 0.
            // 2. set variation_location_details as per purchases.
            // 3. Reset mapping table.
            // 4. map the purchase to sales.

            //STEP1
            //DB::statement('Update variation_location_details set qty_available = 0');

            //Step 2
            // $qty_sums = DB::select('Select SUM(pl.quantity) as qty, pl.product_id, pl.variation_id, transactions.location_id from purchase_lines as pl join transactions on pl.transaction_id = transactions.id group by transactions.location_id, pl.product_id, pl.variation_id');
            // foreach ($qty_sums as $key => $value) {
            //     DB::statement('update variation_location_details set qty_available = qty_available + ? where variation_id = ? and product_id = ? and location_id = ?', [$value->qty, $value->variation_id, $value->product_id, $value->location_id]);
            // }

            //Step 3: Delete existing mapping and sold quantity.
            DB::table('transaction_sell_lines_purchase_lines')->delete();

            PurchaseLine::whereNotNull('created_at')
                ->update(['quantity_sold' => 0]);

            //Get all business
            $businesses = Business::all();

            foreach ($businesses as $business) {
                //Get all transactions
                $transactions = Transaction::where('business_id', $business->id)
                                    ->with('sell_lines')
                                    ->where('type', 'sell')
                                    ->where('status', 'final')
                                    ->orderBy('created_at', 'asc')
                                    ->get();

                $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
                $pos_settings['allow_overselling'] = 1;
                //Iterate through all transaction and add mapping. First go throught sell_lines having lot number.
                foreach ($transactions as $transaction) {
                    $business_formatted = ['id' => $business->id,
                        'accounting_method' => $business->accounting_method,
                        'location_id' => $transaction->location_id,
                        'pos_settings' => $pos_settings,
                    ];

                    foreach ($transaction->sell_lines as $line) {
                        if (! empty($line->lot_no_line_id)) {
                            $this->transactionUtil->mapPurchaseSell($business_formatted, [$line], 'purchase', false);
                        }
                    }
                }

                //Then through sell_lines not having lot number
                foreach ($transactions as $transaction) {
                    $business_formatted = ['id' => $business->id,
                        'accounting_method' => $business->accounting_method,
                        'location_id' => $transaction->location_id,
                        'pos_settings' => $pos_settings,
                    ];

                    foreach ($transaction->sell_lines as $line) {
                        if (empty($line->lot_no_line_id)) {
                            $this->transactionUtil->mapPurchaseSell($business_formatted, [$line], 'purchase', false);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            exit($e->getMessage());
        }
    }
}
