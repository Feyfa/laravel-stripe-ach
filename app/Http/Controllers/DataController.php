<?php

namespace App\Http\Controllers;

use App\Models\MetadataStripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    public function index()
    {
        $invoice_id = 111;
        $metadataStripe = [];
        
        $metadataStripe = array_merge($metadataStripe, [
            'function' => 'createInvoice',
            'try_charge_credit_card' => false,
            'user' => 'usrInfo[0]',
            'acc_connect_id' => 'accConID',
            'total_amount' => 'totalAmount',
            'platformfee' => 'platformfee',
            'cust_email' => 'custEmail',
            'default_invoice' => 'defaultInvoice',
        ]);




        $details = [
            'name'  => 'companyName',
            'invoiceNumber' => 'invoiceNum' . '-' . 'invoiceID',
            'min_leads' => 'minLeads',
            'exceed_leads' => 'exceedLeads',
            'total_leads' => 'ongoingLeads',
            'min_cost' => 'minCostLeads',
            'platform_min_cost' => 'platform_LeadspeekMinCostMonth',
            'cost_leads' => 'costLeads',
            'platform_cost_leads' => 'platform_LeadspeekCostperlead',
            'total_amount' => 'totalAmount',
            'platform_total_amount' => 'platformfee_ori',
            'invoiceDate' => "date('m-d-Y',strtotime(todayDate))",
            'startBillingDate' => "date('m-d-Y H:i:s',strtotime(startBillingDate))",
            'endBillingDate' =>  "date('m-d-Y H:i:s',strtotime(endBillingDate))",
            'invoiceStatus' => 'statusPayment',
            'cardlast' => "trim(cardlast)",
            'leadspeekapiid' => '_leadspeek_api_id',
            'paymentterm' => 'clientPaymentTerm',
            'invoicetype' => 'agency',
            'agencyname' => "rootCompanyInfo['company_name']",
            'defaultadmin' => 'AdminDefaultEmail',
            'agencyNet' => 'agencyNet',
            'rootFee' => 'rootFee',
            'cleanProfit' => 'cleanProfit',
        ];
        $attachement = array();
        $from = [
            'address' => 'noreply@' . 'defaultdomain',
            'name' => 'Invoice',
            'replyto' => 'support@' . 'defaultdomain',
        ];
        $metadataStripe = array_merge($metadataStripe, [
            'invoice_id' => $invoice_id,
            'company_name' => 'companyName',
            'attachement' => $attachement,
            'details1' => $details,
            'from1' => $from,
        ]);




        $details['invoicetype'] = 'client';
        $details['agencyname'] = 'agencyname';
        $details['defaultadmin'] = 'AdminDefaultEmail';
        $details['invoiceStatus'] = str_replace("and Agency's Card Charged For Overage","",$details['invoiceStatus']);
        $from = [
            'address' => 'AdminDefaultEmail',
            'name' => 'Invoice',
            'replyto' => 'AdminDefaultEmail',
        ];
        $metadataStripe = array_merge($metadataStripe, [
            'details2' => $details,
            'from2' => $from,
        ]);

        MetadataStripe::create([
            'invoice_id' => $invoice_id,
            'metadata' => json_encode($metadataStripe),
        ]);

        dd("Succedd");
    }

    public function getData()
    {
        $metadataStripe = MetadataStripe::where('invoice_id', 111)->first();
        $metadata = json_decode($metadataStripe->metadata, true);

        Log::info([
            'try_charge_credit_card' => $metadata['try_charge_credit_card'],
            'attachement' => $metadata['attachement'],
            'details1' => $metadata['details1'],
            'from1' => $metadata['from1'],
            'details2' => $metadata['details2'],
            'from2' => $metadata['from2'],
        ]);
    }
}
