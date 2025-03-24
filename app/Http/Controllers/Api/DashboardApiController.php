<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function getDashboardData()
    {
        $totalClients = Client::count();
        $totalProjects = Project::count();
        $totalTasks = Task::count();
        $totalInvoices = Invoice::count();
        $totalPayments = Payment::sum('amount')/100; // Somme des paiements

        return response()->json([
            'total_clients' => $totalClients,
            'total_projects' => $totalProjects,
            'total_tasks' => $totalTasks,
            'total_invoices' => $totalInvoices,
            'total_payments' => $totalPayments,
        ]);
    }

    public function getPaymentsPerMonth()
    {
        $startDate = Carbon::now()->subMonths(6); // Derniers 6 mois
        $endDate = Carbon::now();
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);

        $data = [];

        foreach ($period as $date) {
            $month = $date->format('Y-m');
            $data[$month] = Payment::whereYear('created_at', $date->year)
                                   ->whereMonth('created_at', $date->month)
                                   ->sum('amount');
        }

        return response()->json($data);
    }

    public function getPaymentsByOffer()
    {
        $paymentsByOffer = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('offers', 'invoices.offer_id', '=', 'offers.id')
            ->select(
                DB::raw('DATE(payments.created_at) as date'),
                'offers.id as offer_id',
                DB::raw('SUM(payments.amount/100) as total_amount')
            )
            ->whereNull('payments.deleted_at') // Condition pour ne prendre que les offres actives
            ->groupBy('date', 'offer_id')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json($paymentsByOffer);
    }

    public function getPaymentsByTask()
    {
        $paymentsByTask = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('tasks', 'invoices.source_id', '=', 'tasks.id')
            ->select(DB::raw('DATE(payments.created_at) as date'), 'tasks.id as task_id', DB::raw('SUM(payments.amount/100) as total_amount'))
            ->groupBy('date', 'task_id')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json($paymentsByTask);
    }

    public function getPaymentsByLead()
    {
        $paymentsByLead = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('leads', 'invoices.source_id', '=', 'leads.id')
            ->select(DB::raw('DATE(payments.created_at) as date'), 'leads.id as lead_id', DB::raw('SUM(payments.amount/100) as total_amount'))
            ->groupBy('date', 'lead_id')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json($paymentsByLead);
    }
    public function getAllPayment()
    {
        // Récupérer les paiements avec les informations du client, source de paiement, date et montant
        $payments = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id') // Jointure avec la table 'invoices'
            ->join('clients', 'invoices.client_id', '=', 'clients.id') // Jointure avec la table 'clients'
            ->select(
                'clients.company_name', // Ajouter le nom de l'entreprise du client
                'payments.payment_source', // Source du paiement
                'payments.payment_date', // Date du paiement
                'payments.amount' // Montant du paiement
            )
            ->get();

        // Retourner les paiements sous forme de réponse JSON
        return response()->json($payments);
    }
}
