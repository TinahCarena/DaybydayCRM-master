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
use App\Services\Invoice\InvoiceCalculator;



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
        // Récupérer les paiements avec les informations du client, source de paiement, date, montant, offer_id et idPayment
        $payments = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id') // Jointure avec la table 'invoices'
            ->join('clients', 'invoices.client_id', '=', 'clients.id') // Jointure avec la table 'clients'
            ->join('offers', 'invoices.offer_id', '=', 'offers.id') // Jointure avec la table 'offers'
            ->whereNull('payments.deleted_at') // Condition pour ne récupérer que les paiements non supprimés
            ->select(
                'payments.id as idPayment', // Ajouter l'ID du paiement
                'clients.company_name', // Nom de l'entreprise du client
                'payments.payment_source', // Source du paiement
                'payments.payment_date', // Date du paiement
                'payments.amount', // Montant du paiement
                'invoices.offer_id' // ID de l'offre associée à la facture
            )
            ->get();

        // Retourner les paiements sous forme de réponse JSON
        return response()->json($payments);
    }

    public function getPaymentById($id)
    {
        // Rechercher le paiement par ID
        $payment = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->join('offers', 'invoices.offer_id', '=', 'offers.id')
            ->select(
                'payments.id as idPayment',
                'invoices.id as invoice_id',
                'clients.company_name', 
                'payments.payment_source',
                'payments.payment_date',
                'payments.amount',
                'invoices.offer_id'
            )
            ->where('payments.id', $id)
            ->first();  // Utilise 'first' pour récupérer un seul paiement

        // Retourner la réponse JSON
        return response()->json($payment);
    }
    public function updatePayment(Request $request)
    {
        $payment = Payment::find($request->idPayment);
        $invoice = Invoice::find($request->idInvoice);

        if (!$invoice) {
            return response()->json(['message' => __("Invoice not found.")], 404);
        }

        // Maintenant, on peut instancier InvoiceCalculator avec un vrai objet Invoice
        if ($payment) {
            
            $invoiceCalculator = new InvoiceCalculator($invoice);
            $amount_reste_a_paye = $invoiceCalculator->getMontantResteAPaye()->getAmount();
            
            if ($request->amount * 100 > $amount_reste_a_paye) {
                // Renvoi d'une réponse JSON avec un message d'avertissement
                return response()->json(['message' => __("The payment amount exceeds !")], 400);
            }
            else{
                $payment->amount = $request->amount * 100;
                $payment->save();
    
                // Réponse réussie
                return response()->json(['message' => 'Payment updated successfully'], 200);
            }
           
        }

        // Si le paiement n'a pas été trouvé
        return response()->json(['message' => 'Payment not found'], 404);
    }

    public function deletePayment(Request $request) {
        // Validation de la présence du paramètre 'idPayment' dans la requête
        $request->validate([
            'idPayment' => 'required|exists:payments,id', // Vérifie que l'idPayment existe dans la table payments
        ]);
    
        // Récupérer le paiement à "supprimer" (en fait, on mettra à jour la colonne deleted_at)
        $payment = Payment::find($request->idPayment);
    
        // Vérifier si le paiement existe avant de mettre à jour la colonne deleted_at
        if ($payment) {
            // Mettre à jour la colonne deleted_at avec la date actuelle
            $payment->deleted_at = Carbon::now();
            $payment->save(); // Sauvegarder les modifications
    
            // Retourner une réponse JSON avec succès
            return response()->json(['message' => 'Payment marked as deleted successfully'], 200);
        }
    
        // Si le paiement n'a pas été trouvé
        return response()->json(['message' => 'Payment not found'], 404);
    }
  
}
