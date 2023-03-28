<?php

namespace App\Jobs;

use App\Mail\emailNonVerifiedParticipants;
use App\Models\Participant;
use App\Models\VerificationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class resendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $backoff = 70;

    private Participant $participant;
    private VerificationToken $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, VerificationToken $token)
    {
        $this->participant = $participant;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->participant->email)
            ->send(new emailNonVerifiedParticipants($this->participant, $this->token));
    }
}
