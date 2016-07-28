<?php 

namespace App\Http\Hotels\Channels;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Http\Hotels\Exceptions\IndipayParametersMissingException;
// use GuzzleHttp\Client;
use App\Http\Hotels\ChannelManagerInterface;
use App\Http\Helpers\GuzzleHelper;

use App\Model\HotelSearchRequest;
use App\Model\HotelAvailableRequest;
use App\Model\HotelRoomSelectRequest;
use App\Model\HotelBookRequest;
use App\Model\HotelCancelRequest;


class AxisRoom implements ChannelManagerInterface {

    protected $parameters = array();
    protected $testMode;
    protected $api_key = '';
    protected $buyer_id = '';
    protected $production;
    protected $test;
    protected $channelName;
    public $response = '';

    function __construct(){

        $this->parameters['key']                =   Config::get('hotels.axisroom.key');
        $this->parameters['buyer_id']           =   Config::get('hotels.axisroom.buyer_id');
        $this->parameters['corporate_buyer_id'] =   Config::get('hotels.axisroom.corporate_buyer_id');
        $this->parameters['sub_user_email']     =   Config::get('hotels.axisroom.sub_user_email');
        $this->parameters['access_level']       =   Config::get('hotels.axisroom.access_level');
        $this->parameters['selected_buyer_id']  =   Config::get('hotels.axisroom.selected_buyer_id');
        $this->parameters['pos']                =   Config::get('hotels.axisroom.pos');

        $this->testMode         =   Config::get('hotels.testMode');
        $this->test             =   Config::get('hotels.axisroom.test_url');
        $this->production       =   Config::get('hotels.axisroom.production_url');


    }

    private function getEndPoint($param=''){

        $endpoint = $this->testMode?$this->test:$this->production;
        return $endpoint.$param;

    }

    public function searchHotel(HotelSearchRequest $hotelSearchRequest){

       $response = $this->prepare($hotelSearchRequest,'hotelsearch/');
        return $response;

    }

    public function checkAvailabity($parameters){

       $response =  $this->prepare($parameters,'checkavailibility/');
        return $response;

    }

    public function selectRoom(HotelRoomSelectRequest $hotelRoomSelectRequest){

       $response =  $this->prepare($hotelRoomSelectRequest,'selectedroom/');
        return $response;

    }

    public function bookHotel(HotelBookRequest $hotelBookRequest){

        $response = $this->prepare($hotelBookRequest,'confirmbooking/');
        return $response;

    }

    public function cancelBooking(HotelCancelRequest $hotelCancelRequest){

        $response =$this->prepare($hotelCancelRequest,'cancelorder/');
        return $response;
        

    }

    private function prepare($arguments,$url){
        $values         =   array();
        foreach ($arguments as $key => $value) {
            # code...
            $value->fill($this->parameters);
            array_push($values, $value);
        }

        // $argument->fill($this->parameters);
        // $this->checkParameters($argument);
      $response =   $this->sendRequest($values,$url);
        return $response;

    }

    // private function sendRequest($parameters,$url){

    //     $client         =   new Client();
    //     $response       =   $client->post($this->getEndPoint($url),
    //                                     [
    //                                         'json' => $parameters->toArray(),
    //                                         // 'debug' => true
    //                                     ]);
    //     if($response->getStatusCode() != 200){
    //         die("Non OK response!!");   
    //     }else{
    //         Log::info("Response: " . $response->getStatusCode());
    //     }
    //     $body = $response->getBody();
    //     $contents = $body->getContents();
    //     $jsonResponse   =   json_decode($contents);

    //     if($jsonResponse->success == "Success"){
    //        return $jsonResponse;
    //     }

    // }


    private function sendRequest($parameters,$url){

        Log::info("Parameters   :-  ",[$parameters]);

        $guzzleHelper   =   new GuzzleHelper();
        $roomDetails    =   $guzzleHelper->sentRequest($parameters,$this->getEndPoint($url));

        // $client         =   new Client();
        // $promise        =   array();
        // $roomDetails    =   array();
        // $responseCount  = 0;
        // foreach ($parameters as $key => $parameter) {
            
        //     Log::info("Parameter   :-  ",[$parameter]);
        //     $hotelId    =   $parameter->hotel_id;
        //     $promise[$hotelId] = $client->requestAsync('POST', $this->getEndPoint($url),
        //                                 [
        //                                     'json' => $parameter->toArray(),
        //                                     // 'debug' => true
        //                                 ]);
        //     Log::info("Promise  :-  ",[$promise[$hotelId]]);

        //     $promise[$hotelId]->then(function ($response) use (&$responseCount, $hotelId, &$roomDetails) {

        //         $responseCount++;
        //         Log::info("Got a response! " . $response->getStatusCode());
        //         if($response->getStatusCode() == 200){
                    
        //             $body           =   $response->getBody();
        //             $contents       =   $body->getContents();
        //             $jsonResponse   =   json_decode($contents);
        //             $roomDetails[$hotelId]    =   $jsonResponse;
        //             Log::info("Response  :- ".$hotelId,[$jsonResponse]);

        //         }

        //     });
        //     $promise[$hotelId]->wait();


        // }
        //             // Log::info("Response  :- ",$roomDetails);
        // while($responseCount < count($parameters)){
        //     usleep(100 * 1000);
        // }
        Log::info("Room details: ", $roomDetails);
    

        // die();

        // $response       =   $client->post($this->getEndPoint($url),
        //                                 [
        //                                     'json' => $parameters->toArray(),
        //                                     // 'debug' => true
        //                                 ]);
        // if($response->getStatusCode() != 200){
        //     die("Non OK response!!");   
        // }else{
        //     Log::info("Response: " . $response->getStatusCode());
        // }
        // $body = $response->getBody();
        // $contents = $body->getContents();
        // $jsonResponse   =   json_decode($contents);

        // if($jsonResponse->success == "Success"){
           return $roomDetails;
        // }

    }

    private function checkParameters($parameters){

        $validator = Validator::make($parameters, [

            'key'           => 'required',
            'buyer_id'      => 'required|numeric',
            'latitude'      => 'required_without_all:city_id',
            'longitude'     => 'required_without_all:city_id',
            'city_id'       => 'required_without_all:latitude,longitude'  

        ]);

        if ($validator->fails()) {

            throw new ChannelManagerParametersMissingException("Invalid parameters");

        }

    }




}