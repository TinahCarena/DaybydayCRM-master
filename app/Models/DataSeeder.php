<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DataSeeder extends Model
{
    protected $table = null;

    public static function insertUsers($count)
    {
        DB::statement("CALL insert_users(?)", [$count]);
    }

    public static function insertDepartments($count)
    {
        DB::statement("CALL insert_departments(?)", [$count]);
    }

    public static function insertDepartmentUser($count)
    {
        DB::statement("CALL insert_department_user(?)", [$count]);
    }

    public static function insertRandomComments($count)
    {
        DB::statement("CALL insert_random_comments(?)", [$count]);
    }

    public static function insertRandomMails($count)
    {
        DB::statement("CALL insert_random_mails(?)", [$count]);
    }

    public static function insertRandomAbsences($count)
    {
        DB::statement("CALL insert_random_absences(?)", [$count]);
    }

    public static function insertRandomIndustries($count)
    {
        DB::statement("CALL insert_random_industries(?)", [$count]);
    }

    public static function insertRandomClients($count)
    {
        DB::statement("CALL insert_random_clients(?)", [$count]);
    }

    public static function insertRandomContacts($count)
    {
        DB::statement("CALL insert_random_contacts(?)", [$count]);
    }

    public static function insertRandomAppointments($count)
    {
        DB::statement("CALL insert_random_appointments(?)", [$count]);
    }

    public static function insertRandomLeads($count)
    {
        DB::statement("CALL insert_random_leads(?)", [$count]);
    }

    public static function insertRandomProjects($count)
    {
        DB::statement("CALL insert_random_projects(?)", [$count]);
    }

    public static function insertRandomTasks($count)
    {
        DB::statement("CALL insert_random_tasks(?)", [$count]);
    }

    public static function insertRandomOffers($count)
    {
        DB::statement("CALL insert_random_offers(?)", [$count]);
    }

    public static function insertRandomInvoices($count)
    {
        DB::statement("CALL insert_random_invoices(?)", [$count]);
    }

    public static function insertRandomInvoiceLines($count)
    {
        DB::statement("CALL insert_random_invoice_lines(?)", [$count]);
    }

    public static function insertRandomPayments($count)
    {
        DB::statement("CALL insert_random_payments(?)", [$count]);
    }

    public static function insertRandomProducts($count)
    {
        DB::statement("CALL insert_random_products(?)", [$count]);
    }

    public static function insertRandomDocuments($count)
    {
        DB::statement("CALL insert_random_documents(?)", [$count]);
    }


    public static function insertUser(string $name): int
    {
        return DB::select('SELECT insert_user(?) AS user_id', [$name])[0]->user_id;
    }

    public static function insertClientForUser(int $userId): int
    {
        $result = DB::select('CALL insert_client_for_user(?)', [$userId]);
        return $result[0]->new_client_id;
    }

    public static function insertTaskForProject($projectId,$taskTitle): int
    {
        $result = DB::select('CALL insert_task_for_project(?, ?)', [$projectId, $taskTitle]);
        return $result[0]->new_task_id;
    }
    public static function insertProjectForClient($clientId,$projectTitle): int
    {
        $result = DB::select('CALL insert_project_for_client(?, ?)', [$clientId, $projectTitle]);
        return $result[0]->new_project_id;
    }
    public static function createLead($title, $clientId) : int
    {
        $result = DB::select('CALL insert_single_lead(?, ?)', [$title, $clientId]);
        return $result[0]->new_lead_id;
    }

    public static function createProduct($name) : string
    {
        $result = DB::select('CALL insert_single_product(?)', [$name]);
        Log::info(" kdjjj = ". print_r($result,true));
        return $result[0]->new_product;

    }

    public static function createInvoiceLine($invoice_id,$p_offer_id,$p_price,$p_quantity,$p_product_id) : int
    {
        $result = DB::select('CALL insert_single_invoice_line(?,?,?,?,?)', [$invoice_id,$p_offer_id,$p_price,$p_quantity,$p_product_id]);
        return $result[0]->new_invoice_line_id;
    }

    public static function createInvoice($p_client_id,$p_offer_id) : int
    {
        $result = DB::select('CALL insert_single_invoice(?,?)', [$p_client_id,$p_offer_id]);
        return $result[0]->new_invoice_id;
    }

    public static function createOffer($p_client_id,$p_source_id) : int
    {
        $result = DB::select('CALL insert_single_offer(?,?)', [$p_client_id,$p_source_id]);
        return $result[0]->new_offre_id;
    }
}