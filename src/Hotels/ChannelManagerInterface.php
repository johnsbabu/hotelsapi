<?php 

namespace App\Http\Hotels;

use App\Model\HotelSearchRequest;
use App\Model\HotelAvailableRequest;
use App\Model\HotelRoomSelectRequest;
use App\Model\HotelBookRequest;
use App\Model\HotelCancelRequest;

interface ChannelManagerInterface {

    public function searchHotel(HotelSearchRequest $hotelSearchRequest);

    public function checkAvailabity($parameters);

    public function selectRoom(HotelRoomSelectRequest $hotelRoomSelectRequest);

    public function bookHotel(HotelBookRequest $hotelBookRequest);

    public function cancelBooking(HotelCancelRequest $hotelCancelRequest);

}