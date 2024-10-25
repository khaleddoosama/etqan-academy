<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait LogsActivityForModels
{
    use LogsActivity;

    protected static $logAttributes = ['*'];


    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        $isApiRequest = request()->is('api/*');

        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['last_login', 'updated_at'])
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getTable() . ($isApiRequest ? '_api' : '_web'))
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($isApiRequest) {
                $username = Auth::check() ? Auth::user()->name : 'Guest';
                $userId = Auth::check() ? Auth::user()->id : 'Guest';
                $requestType = $isApiRequest ? 'via API' : 'via Web';
                if ($eventName == 'updated') {
                    $eventName = '<span class="badge badge-warning">Updated</span>';
                } elseif ($eventName == 'created') {
                    $eventName = '<span class="badge badge-success">Created</span>';
                } elseif ($eventName == 'deleted') {
                    $eventName = '<span class="badge badge-danger">Deleted</span>';
                } 

                return "This <b>{$this->getTable()}</b>
                <b> model has been {$eventName} </b>
                <b> by " . $username . ' #' . $userId . " </b>
                <b>" . $requestType . " </b>
                <b> from " . request()->ip() . " </b>
                <b> at " . Carbon::now()->toDateTimeString() . "</b>";
            });
    }
}
