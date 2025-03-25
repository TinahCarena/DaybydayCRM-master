<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Offer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailApiController extends Controller
{
    public function getAllClient()
    {
        // Récupérer les clients avec leurs informations
        $clients = DB::table('clients')
            ->leftJoin('users', 'clients.user_id', '=', 'users.id')
            ->leftJoin('industries', 'clients.industry_id', '=', 'industries.id')
            ->select(
                'clients.id',
                'clients.external_id',
                'clients.address',
                'clients.zipcode',
                'clients.city',
                'clients.company_name',
                'clients.vat',
                'clients.company_type',
                'clients.client_number',
                'users.name as user_name',
                'industries.name as industry_name',
                'clients.created_at',
                'clients.updated_at'
            )
            ->whereNull('clients.deleted_at')
            ->get();

        // Retourner les clients sous forme de réponse JSON
        return response()->json($clients);
    }

    public function getAllProject()
    {
        // Récupérer les projets avec leurs informations
        $projects = DB::table('projects')
            ->leftJoin('users as assigned_users', 'projects.user_assigned_id', '=', 'assigned_users.id')
            ->leftJoin('users as created_users', 'projects.user_created_id', '=', 'created_users.id')
            ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
            ->select(
                'projects.id',
                'projects.external_id',
                'projects.title',
                'projects.description',
                'projects.status_id',
                'assigned_users.name as assigned_user_name',
                'created_users.name as created_user_name',
                'clients.company_name as client_name',
                'projects.deadline'
            )
            ->whereNull('projects.deleted_at')
            ->get();

        // Retourner les projets sous forme de réponse JSON
        return response()->json($projects);
    }

    public function getAllTask()
    {
        // Récupérer les tâches avec leurs informations associées
        $tasks = DB::table('tasks')
            ->leftJoin('users as assigned_users', 'tasks.user_assigned_id', '=', 'assigned_users.id')
            ->leftJoin('users as created_users', 'tasks.user_created_id', '=', 'created_users.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->select(
                'tasks.id',
                'tasks.external_id',
                'tasks.title',
                'tasks.description',
                'tasks.status_id',
                'assigned_users.name as assigned_user_name',
                'created_users.name as created_user_name',
                'clients.company_name as client_name',
                'projects.title as project_name',
                'tasks.deadline'
            )
            ->whereNull('tasks.deleted_at')
            ->get();

        // Retourner les tâches sous forme de réponse JSON
        return response()->json($tasks);
    }

    public function getAllInvoices()
    {
        // Récupérer les factures avec leurs informations
        $invoices = DB::table('invoices')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->leftJoin('offers', 'invoices.offer_id', '=', 'offers.id')
            ->select(
                'invoices.id',
                'invoices.status',
                'invoices.sent_at',
                'invoices.due_at',
                'clients.company_name as client_name',
                'invoices.integration_invoice_id',
                'invoices.integration_type',
                'invoices.source_id',
                'invoices.source_type',
                'invoices.external_id'
            )
            ->whereNull('invoices.deleted_at')
            ->get();

        // Retourner les factures sous forme de réponse JSON
        return response()->json($invoices);
    }


}
