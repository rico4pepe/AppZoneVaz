<?php

namespace App\Observers;

use App\Models\Fixture;
use App\Services\ChatSystemMessageService;

class MatchObserver
{
    /**
     * Handle the Fixture "created" event.
     *
     * @param  \App\Models\Fixture  $fixture
     * @return void
     */
    public function created(Fixture $match)
    {
        //
    }

    /**
     * Handle the Fixture "updated" event.
     *
     * @param  \App\Models\Fixture  $fixture
     * @return void
     */
    public function updated(Fixture $match)
    {
        //
        if ($match->wasChanged(['home_score', 'away_score'])) {

        app(ChatSystemMessageService::class)->goal($match);

    }
    }

    /**
     * Handle the Fixture "deleted" event.
     *
     * @param  \App\Models\Fixture  $fixture
     * @return void
     */
    public function deleted(Fixture $match)
    {
        //
    }

    /**
     * Handle the Fixture "restored" event.
     *
     * @param  \App\Models\Fixture  $fixture
     * @return void
     */
    public function restored(Fixture $match)
    {
        //
    }

    /**
     * Handle the Fixture "force deleted" event.
     *
     * @param  \App\Models\Fixture  $fixture
     * @return void
     */
    public function forceDeleted(Fixture $match)
    {
        //
    }
}
