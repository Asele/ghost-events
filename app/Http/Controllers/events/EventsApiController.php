<?php

namespace App\Http\Controllers\events;

use App\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class EventsApiController extends Controller
{

    private $events = null;

    function __construct(Events $events)
    {
        $this->events = $events;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = $this->events->create(1, $this->eventValidator($request->all())->validate());

//      $event = Event::create($validated);
        return $event; // Response::make("Ok");
    }

    public function join(int $eventId) {
        $this->events->setAsGuest(Auth()::user->id, $eventId);
    }

    public function leave(int $eventId) {
        $this->events->removeGuest(Auth()::user->id, $eventId);
    }

    public function invite(Request $request, int $eventId) {
        foreach ($request->get('usersIds') as $userId) {
            $this->events->invite($userId, $eventId);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = $this->eventValidator($request->all(), false);
        $event = $this->events->update($id, $validator->validate());

        return $event;
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param  array $data
     * @param bool $strict
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function eventValidator(array $data, bool $strict = true)
    {
        $rules = [
            'name' => ['string', 'max:255'],
            'description' => ['string', 'max:1600'],
            'maxGuests' => ['integer', 'min:1'],
            'starts_at' => ['date_format:Y-m-d H:i', 'after:today'],
            'ends_at' => ['date_format:Y-m-d H:i', 'after:starts_at'],
            'closes_at' => ['date_format:Y-m-d H:i', 'before_or_equal:ends_at'],
            'street' => ['string', 'min:3'],
            'city' => ['string', 'min:3'],
            'zipCode' => ['string', 'regex:/^[0-9]{2}-[0-9]{3}$/'], // zip code format 00-000
            'latitude' => ['numeric'],
            'longitude' => ['numeric'],
            'private' => ['boolean'],
        ];

        if ($strict) {
            foreach ($rules as $conditions) {
                array_push($conditions, 'required');
            }
        }

        return Validator::make($data, $rules);
    }
}