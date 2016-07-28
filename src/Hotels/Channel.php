<?php 

namespace App\Http\Hotels;

use Log;

use App\Http\Hotels\Channels\AxisRoom;
use Softon\Indipay\Gateways\ChannelManagerInterface;

use App\Model\ChannelModel;
use App\Model\HotelSearchRequest;
use App\Model\HotelAvailableRequest;
use App\Model\HotelRoomSelectRequest;
use App\Model\HotelBookRequest;
use App\Model\HotelCancelRequest;


class Channel {

    protected $channelManagerInterface;

    /**
     * @param ChannelManagerInterface $gateway
     */
    // function __construct(ChannelManagerInterface $channelManagerInterface)
    // {
    //     $this->channelManagerInterface = $channelManagerInterface;
    // }

    public function search(HotelSearchRequest $hotelSearchRequest,$channelId){

        $this->getChannel($channelId);
        return $this->channelManagerInterface->searchHotel($hotelSearchRequest);

    }

    public function check($parameters,$channelId){

        $this->getChannel($channelId);
        $response =  $this->channelManagerInterface->checkAvailabity($parameters);
        return $response;

    }

    public function select(HotelRoomSelectRequest $hotelRoomSelectRequest,$channelId){

        $this->getChannel($channelId);
        return $this->channelManagerInterface->selectRoom($hotelRoomSelectRequest);

    }

    public function book(HotelBookRequest $hotelBookRequest,$channelId){

        $this->getChannel($channelId);
        return $this->channelManagerInterface->bookHotel($hotelBookRequest);

    }

    public function cancel(HotelCancelRequest $hotelCancelRequest,$channelId){

        $this->getChannel($channelId);
        return $this->channelManagerInterface->cancelBooking($hotelCancelRequest);

    }

    public function getChannel($channelId)
    {

        $channel        =   ChannelModel::find($channelId);
        $channelName    =   $channel->name;
        switch($channelName)
        {
            case 'axisroom':
                $this->channelManagerInterface = new AxisRoom();
                break;
        }

        return $this;
    }



}