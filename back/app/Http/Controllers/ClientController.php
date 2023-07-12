<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function index()
    {
        // $clients = Client::on("mysqlis")->orderBy("name")->take(100)->get()4;
        // return response()->json($clients);
        $clients = Client::orderBy("name")->get();

        return response()->json($clients);
    }

    public function limited()
    {
        $clients = Client::orderBy('name')->take(20)->get();

        return response()->json($clients);
    }
    public function show($id)
    {
        $client =
            DB::table('clients')->where('id',  $id)->first();

        if (!empty($client))
            return response()->json($client);

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function store(Request $request)
    {

        $availableID = DB::table('available_ids')->limit(1)->value('id');

        $client = Client::where('name', '=', ucwords($request->name))->first();

        if (empty($client)) {
            try {

                DB::table('clients')
                    ->insert([
                        'id' => $availableID,
                        'account_no' => $availableID,
                        'region_id' => $request->region_id,
                        'name' => $request->name,
                        'owner_name' => $request->name,
                        'contact_person' => $request->contact_person,
                        'contact' => $request->contact,
                        'email_add' => $request->email_add,
                        'location' => $request->location,
                        'class' => $request->class,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now()
                    ]);


                DB::table('available_ids')->where('id', $availableID)->delete();

                return "saved!";
            } catch (\Exception $ex) {
                return response()->json(['error' =>  $ex->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'Client already exists!'], 500);
        }
    }

    public function update(Request $request, $id)
    {

        DB::table('clients')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'contact_person' => $request->contact_person,
                'contact' => $request->contact,
                'email_add' => $request->email_add,
                'location' => $request->location,
                'class' => $request->class,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        return response()->json($this->limited()->original);
    }

    public function showSearch(Request $request)
    {
        $clients = DB::table('clients')
            // ->select('name')
            ->where('name', 'like', '%' . $request->client . '%')
            ->get();
        return response()->json($clients);
    }

    public function countClient()
    {
        $clients = Client::all();
        return count($clients);
    }

    public function updateClients()
    {
        // return "connected";
        // $clients = Client::on("mysqlis")->orderBy("name")->take(500)->get();
        try {
            $current = Client::max('id');
            $dataSet = Client::on("mysqlis")->orderBy("id")->where("id", ">", $current)->take(1000)->get();
            // return $dataSet;
            $temp = [];
            foreach ($dataSet as $i => $d) {
                if (empty($d->contact_person)) {
                    $d->contact_person = "no contact person";
                } else if (empty($d->business_type)) {
                    $d->business_type = "no contact person";
                } else if (empty($d->contact)) {
                    $d->contact = "no contact";
                } else if (empty($d->email_add)) {
                    $d->email_add = "no email";
                }


                $temp[$i] = [
                    'id' => $d->id,
                    'account_no' => $d->id,
                    'region_id' => $d->region_id,
                    'name' => $d->name,
                    'owner_name' => $d->owner_name,
                    'class' => "INET CLIENTS",
                    'location' => $d->location,
                    'contact_person' => $d->contact_person,
                    'business_type' => $d->business_type,
                    'contact' => $d->contact,
                    'email_add' => $d->email_add,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()


                ];
            }

            DB::table('clients')->insert($temp);

            return "ok";
            // return response()->json($this->index());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
