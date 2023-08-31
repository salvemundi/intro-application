<?php

namespace App\Jobs;

use App\Mail\emailConfirmationSignup;
use App\Models\ConfirmationToken;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class resendConfirmationEmailToAllUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Participant $participant;
    private ConfirmationToken $newConfirmationToken;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, ConfirmationToken $newConfirmationToken)
    {
        $this->participant = $participant;
        $this->newConfirmationToken = $newConfirmationToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->participant->email)
            ->send(new emailConfirmationSignup($this->participant, $this->newConfirmationToken));
    }
}
