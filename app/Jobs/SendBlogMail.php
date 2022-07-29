<?php

namespace App\Jobs;

use App\Mail\participantMail;
use App\Models\Blog;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBlogMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Participant $participant;
    private Blog $blog;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, Blog $blog)
    {
        $this->participant = $participant;
        $this->blog = $blog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Mail::bcc($this->participant)
            ->send(new participantMail($this->participant, $this->blog));
    }
}
