<?php

namespace App\Jobs;

use App\Mail\participantMail;
use App\Models\Blog;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendBlogMail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private Collection $participants;
    private Blog $blog;
    private bool $sendToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $participants, Blog $blog, bool $sendToken)
    {
        $this->participants = $participants;
        $this->blog = $blog;
        $this->sendToken = $sendToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        foreach($this->participants as $participant) {
            Mail::bcc($participant)
                ->send(new participantMail($participant, $this->blog, $this->sendToken));
        }
        sleep(70);
    }
}
