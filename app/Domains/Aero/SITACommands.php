<?php

namespace App\Domains\Aero;

use App\Models\Agent;
use App\Models\SpecialNeed;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class SITACommands
{
    public static $dateFormat = 'dMy';

    public function tripData($ticket)
    {
        if ($ticket->flight->departure && $ticket->flight->destination) {
            return Str::upper(
                sprintf(
                    'AY%s%s%s',
                    $ticket->flight->departure->IATA,
                    $ticket->flight->destination->IATA,
                    Date::parse($ticket->flight->departs_at)->format(static::$dateFormat)
                )
            );
        }

        return '';
    }

    public function generateTickets($tickets)
    {
        $count = count($tickets);

        return "SD1Y{$count}";
    }

    public function generatePhone($ticket, ?Agent $agent = null)
    {
        if ($ticket->passenger && $ticket->passenger->phone) {
            return "CTM/{$ticket->passenger->phone}";
        }

        if ($agent && ($agent->user && $agent->user->phone)) {
            return "CTM/{$agent->user->phone}";
        }

        return 'CTM/';
    }

    public function generatePassengers($tickets, $prefix = 'NM')
    {
        $passengers = $prefix;

        foreach ($tickets as $ticket) {
            $passengers .= Str::upper(
                sprintf(
                    '1%s/%s %s ',
                    str_replace('-', ' ', $ticket->passenger->last_name),
                    str_replace('-', ' ', $ticket->passenger->first_name),
                    $ticket->passenger->title
                )
            );
        }

        return $passengers;
    }

    public function generateTKT()
    {
        return 'TKT/';
    }

    public function generateDOCS($tickets)
    {
        $passengerCount = 0;
        $infantCount = 0;

        $DOCS = [];

        foreach ($tickets as $ticket) {
            $ageGroup = Str::lower(optional($ticket->passenger->ageGroup ?? $ticket->ageGroup)->name);
            $birthdate = Date::parse($ticket->passenger->birthdate)->format(static::$dateFormat);
            $isInfant = $ageGroup === 'infant';
            $lastName = $ticket->passenger->last_name;
            $firstName = $ticket->passenger->first_name;
            $ageGroupIndentifier = $isInfant ? 'I' : '';

            $SSR = sprintf(
                'SSR:ALLDOCSIAHK1/P/%s/%s/%s/%s/%s/%s/%s/%s/P%d',
                country($ticket->passenger->citizenship)['alpha-3'] ?? 'UNK',
                $ticket->passenger->passport,
                country($ticket->passenger->nationality)['alpha-3'] ?? 'UNK',
                $birthdate,
                substr($ticket->passenger->gender, 0, 1) . $ageGroupIndentifier,
                Date::parse($ticket->passenger->passport_expires_at)->format(static::$dateFormat),
                $lastName,
                $firstName,
                $isInfant ? $infantCount = $infantCount+1 : $passengerCount = $passengerCount+1
            );

            $DOCS[] = Str::upper(
                str_replace(['-', ' '], '', $SSR)
            );

            if ($ticket->passenger->specialNeeds->isNotEmpty()) {
                $specialNeedDOCS = $ticket
                    ->passenger
                    ->specialNeeds
                    ->map(function ($specialNeed) use ($passengerCount) {
                        $command = sprintf($specialNeed->sita_command, $passengerCount);

                        return Str::upper(
                            trim($command)
                        );
                    })
                    ->toArray();

                $DOCS = array_merge($DOCS, $specialNeedDOCS);
            }

            if ($ageGroup === 'child') {
                $DOCS[] = Str::upper(
                    "SSR:CHLDIAHK1/{$birthdate}/P{$passengerCount}"
                );
            }

            if ($isInfant) {
                $DOCS[] = "SSR:ALLINFTIAHK1 .{$lastName}/{$firstName} {$ticket->passenger->title} {$birthdate}/P{$infantCount}";
            }
        }

        return $DOCS;
    }
}
