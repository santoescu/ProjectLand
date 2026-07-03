<?php

namespace App\Console\Commands;

use App\Models\Insurance;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringInsurances extends Command
{
    protected $signature = 'insurances:notify-expiring';

    protected $description = 'Email admin, director, and accounting_assistant users when an insurance policy enters the expiring-soon window';

    public function handle(): int
    {
        $insurances = Insurance::with('contractor')
            ->whereNull('notified_at')
            ->get()
            ->filter(fn (Insurance $insurance) => $insurance->status === 'expiring_soon');

        if ($insurances->isEmpty()) {
            $this->info('No insurances entering the expiring-soon window.');
            return self::SUCCESS;
        }

        $users = app()->environment('production')
            ? User::whereIn('role', ['admin', 'director', 'accounting_assistant'])->get()
            : collect([(object) ['email' => 'santoescu@gmail.com']]);

        foreach ($insurances as $insurance) {
            foreach ($users as $user) {
                Mail::send('emails.insurance_expiring', ['insurance' => $insurance], function ($message) use ($user, $insurance) {
                    $message->to($user->email)
                        ->subject(($insurance->contractor->company_name ?? '') . ' - ' . __(':name Expiring Soon', ['name' => __('Insurance')]));
                });
            }

            $insurance->notified_at = now();
            $insurance->save();

            $this->info("Notified for insurance {$insurance->_id}.");
        }

        return self::SUCCESS;
    }
}
