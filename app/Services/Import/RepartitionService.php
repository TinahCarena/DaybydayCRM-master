<?php
namespace App\Services\Import;

use App\Enums\InvoiceStatus;
use App\Enums\OfferStatus;
use App\Models\Client;
use App\Models\Project;
use App\Models\TempProject;
use App\Events\ClientAction;
use App\Http\Controllers\ClientsController;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Task;
use App\Models\TempOffer;
use App\Models\TempProjectTask;
use App\Services\ClientNumber\ClientNumberService;
use App\Services\InvoiceNumber\InvoiceNumberService;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;

class RepartitionService
{
    public function repartitionTempProject()
    {
        
        $tempProjects = TempProject::all();

        foreach ($tempProjects as $tempProject) {
            $client = Client::where('company_name', $tempProject->client_name)->first();

            if (!$client) {
                $client = Client::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'vat' => 'N/A', 
                    'company_name' => $tempProject->client_name,
                    'address' => 'N/A', 
                    'zipcode' => 'N/A', 
                    'city' => 'N/A', 
                    'company_type' => 'N/A', 
                    'industry_id' => 1, 
                    'user_id' => 1,
                    'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
                ]);
                
                 
                $contact = Contact::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'name' => 'Contact principal', 
                    'email' =>$this->generateFakeEmail($tempProject->client_name),
                    'primary_number' => null,
                    'secondary_number' => null, 
                    'client_id' => $client->id,
                    'is_primary' => true
                ]);
            }

            $projectExist = Project::where('title', $tempProject->project_title)->first();

             
            if( !$projectExist ) {
                $project = Project::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'title' => $tempProject->project_title,
                    'description' => 'Description factice', 
                    'client_id' => $client->id,
                    'user_created_id' => 1, 
                    'user_assigned_id' => 1, 
                    'status_id' => 1, 
                    'deadline' => now()->addDays(30), 
                ]);
            }

             
            event(new ClientAction($client, ClientsController::CREATED));

            
            $tempProject->delete();
        }
    }
   
    private function generateFakeEmail($clientName)
    {
        return strtolower(str_replace(' ', '.', $clientName)) . '@fictif.com';
    }








    public function repartitionTempProjectTask()
    {
        $tempProjectTasks = TempProjectTask::all();

        foreach ($tempProjectTasks as $tempProjectTask) {
            $projectExist = Project::where('title', $tempProjectTask->project_title)->first();

            if (!$projectExist) {
                $project = Project::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'title' => $tempProjectTask->project_title,
                    'description' => 'Description factice', 
                    'client_id' => 1, 
                    'user_created_id' =>auth()->id(), 
                    'user_assigned_id' => auth()->id(),
                    'status_id' => 1, 
                    'deadline' => now()->addDays(30), 
                ]);
            }

            
            $taskExist = Task::where('title', $tempProjectTask->task_title)
                             ->where('project_id', $projectExist ? $projectExist->id : $project->id) // Associer la tâche au bon projet
                             ->first();

            if (!$taskExist) {
                
                Task::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'title' => $tempProjectTask->task_title,
                    'description' => 'Description factice', 
                    'status_id' => 1, 
                    'user_created_id' =>auth()->id(), 
                    'user_assigned_id' =>auth()->id(), 
                    'client_id' => $projectExist->client_id, 
                    'project_id' => $projectExist ? $projectExist->id : $project->id, 
                    'deadline' => now()->addDays(15),  
                ]);
            }

           
            $tempProjectTask->delete();
        }
    }


    public function repartitionTempOffer()
    {
        $tempOffers = TempOffer::all();
    
        foreach ($tempOffers as $tempOffer) {
            try {
            
                $client = Client::where('company_name', $tempOffer->client_name)->first();
    
                if (!$client) {
                    $client = Client::create([
                        'external_id' => Uuid::uuid4()->toString(),
                        'vat' => 'N/A', 
                        'company_name' => $tempOffer->client_name,
                        'address' => 'N/A', 
                        'zipcode' => 'N/A', 
                        'city' => 'N/A', 
                        'company_type' => 'N/A', 
                        'industry_id' => 1, 
                        'user_id' => auth()->id(),
                        'client_number' => app(ClientNumberService::class)->setNextClientNumber(),
                    ]);
                    
                    $contact = Contact::create([
                        'external_id' => Uuid::uuid4()->toString(),
                        'name' => 'Contact principal', 
                        'email' => $this->generateFakeEmail($tempOffer->client_name),
                        'primary_number' => null,
                        'secondary_number' => null, 
                        'client_id' => $client->id,
                        'is_primary' => true
                    ]);
    
                    // event(new ClientAction($client, ClientsController::CREATED));
                }
    
                
                $lead = Lead::where('title', $tempOffer->lead_title)
                          ->where('client_id', $client->id)
                          ->first();
    
                if (!$lead) {
                    $lead = Lead::create([
                        'title' => $tempOffer->lead_title,
                        'description' => 'Description fictive',
                        'user_assigned_id' => auth()->id(),
                        'deadline' => now()->addDays(15),
                        'status_id' => 1,
                        'user_created_id' => auth()->id(),
                        'external_id' => Uuid::uuid4()->toString(),
                        'client_id' => $client->id
                    ]);
                }
    
                
                $product = Product::where('name', $tempOffer->produit)->first();
                
                if (!$product) {
                    $product = Product::create([
                        'name' => $tempOffer->produit,
                        'external_id' => Uuid::uuid4()->toString(),
                        'description' => 'Fictive',
                        'number' => Uuid::uuid1()->toString(),
                        'price' => $tempOffer->prix,
                        'default_type' => 'pieces',
                        'archived' => false
                    ]);
                }
                else if($product->price!= $tempOffer->prix) {
                    
                }   
    
                
                // $offer = Offer::where('source_type', "App\Models\Lead")
                //             ->where('source_id', $lead->id)
                //             ->where('client_id', $client->id)
                //             ->first();
    
                // if (!$offer) {
                    $offer = Offer::create([
                        'status' => OfferStatus::inProgress()->getStatus(),
                        'client_id' => $client->id,
                        'external_id' => Uuid::uuid4()->toString(),
                        'source_id' => $lead->id,
                        'source_type' => Lead::class
                    ]);

    
                    
                // }

                $invoiceLine = InvoiceLine::where('product_id', $product->id)
                ->where('offer_id', $offer->id)
                ->whereNull('invoice_id')
                ->first();
                if(!$invoiceLine){
                    $invoiceLine = InvoiceLine::make([
                        'title' => $product->name,
                        'type' => $product->default_type,
                        'quantity' => $tempOffer->quantite,
                        'external_id'=>Uuid::uuid4()->toString(),
                        'comment' => '',
                        'price' => $tempOffer->prix,
                        'product_id' => $product->id,
                        'offer_id' => $offer->id
                    ]);

                    $offer->invoiceLines()->save($invoiceLine);
                }

                $invoice=Invoice::where('source_type','App\Models\Lead')->where('source_id', $lead->id)->where('offer_id', $offer->id)->first();
                if ($tempOffer->type == "invoice") {
                    $offer->setAsWon();
                    if(!$invoice){
                       
    
                        $invoice = Invoice::create($offer->toArray());
                        $invoice->offer_id = $offer->id;
                        $invoice->invoice_number = app(InvoiceNumberService::class)->setNextInvoiceNumber();
                        $invoice->status = InvoiceStatus::draft()->getStatus();
                    }
                    // $invoice->created_at= now()->addSecond(20);
                    // $invoice->updated_at= now()->addSecond(20);

                    $invoice->save();
                    
                    
                    $invoiceLine->offer_id = null;
                    $invoiceLine->invoice_id = $invoice->id;
                    // $invoiceLine->external_id="";
                    // $invoiceLine->created_at= now()->addSecond(20);
                    // $invoiceLine->updated_at= now()->addSecond(20);
                    $newInvoiceLine=InvoiceLine::make($invoiceLine->toArray());
                    $invoice->invoiceLines()->save($newInvoiceLine);

                }
    
                 
                $tempOffer->delete();
    
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

}
