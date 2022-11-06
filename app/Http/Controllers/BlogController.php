<?php

namespace App\Http\Controllers;

use App\Enums\AuditCategory;
use App\Enums\Roles;
use App\Jobs\SendBlogMail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Occupied;
use Carbon\Carbon;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// This controller is commonly referred to as blog / news controller. Previous PR #12 caused a naming nightmare. (May or may not have been me.)
class BlogController extends Controller
{
    private VerificationController $verificationController;
    private PaymentController $paymentController;

    public function __construct() {
        $this->verificationController = new VerificationController();
        $this->paymentController = new PaymentController();
    }

    public function showPosts(): Factory|View|Application
    {
        $posts = Blog::orderBy('created_at', 'desc')->where('show','1')->get();
        $lastBlog = Blog::where('show', '1')->latest()->first();

        $dateForIntro = Carbon::parse('2022-08-22');
        $dateNow = Carbon::now();

        $diffDate = $dateForIntro->diffInDays($dateNow) + 1;

        $occupied = Occupied::all()->first();

        return view('blogs', ['posts' => $posts, 'date' => $diffDate, 'occupied' => $occupied, 'lastBlog' => $lastBlog]);
    }

    public function showPostsAdmin(): Factory|View|Application
    {
        $posts = Blog::all();
        $occupied = Occupied::all()->first();
        return view('admin/blogs', ['posts' => $posts, 'occupied' => $occupied]);
    }

    public function updateOccupiedPercentage(Request $request): Redirector|Application|RedirectResponse
    {
        if(Occupied::all()->first() != null) {
            $occupied = Occupied::all()->first();
        } else {
            $occupied = new Occupied();
        }

        $occupied->occupied = $request->input('occupied');
        $occupied->save();
        AuditLogController::Log(AuditCategory::Other(),"Heeft percentage beschikbare plekken aangepast naar: " . $occupied->occupied);
        return redirect('/blogsadmin')->with('success', 'percentage is geupdated!');
    }

    public function showPost(Request $request) {
        $postId = $request->postId;
    }

    public function savePost(Request $request): Redirector|Application|RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'content' => 'required',
        ]);

        if($request->input('blogId')) {
            $post = Blog::find($request->input('blogId'));
        } else {
            $post = new Blog;
        }

        $post->name =  $request->input('name');
        $post->content =  $request->input('content');

        $post->save();
        if(isset($request->addBlog)) {
            AuditLogController::Log(AuditCategory::BlogManagement(),"Heeft blog toegevoegd of bewerkt: " . $post->name,null, $post);
            $post->show = true;
            $post->save();
        }

        if(isset($request->sendEmail)) {
            AuditLogController::Log(AuditCategory::BlogManagement(),"Verstuurde emails van blog " . $post->name,null, $post);
            $this->sendEmails($post, $request);
        }

        return redirect('/blogsadmin')->with('success', 'Blog is opgeslagen!');
    }

    public function showPostInputs(Request $request): Factory|View|Application
    {
        $post = null;
        if($request->blogId) {
            $post = Blog::find($request->blogId);
        }
        return view('admin/blogInput',['post' => $post]);
    }

    public function deletePost(Request $request): Redirector|Application|RedirectResponse
    {
        if($request->blogId) {
            $blog = Blog::find($request->blogId);
            if($blog != null) {
                AuditLogController::Log(AuditCategory::BlogManagement(),"Heeft blog " . $blog->name . " verwijderd.", null, $blog);
                $blog->delete();
                return redirect('/blogsadmin')->with('success', 'Blog is verwijderd!');
            }
            return redirect('/blogsadmin')->with('error', 'Blog kon niet gevonden worden!');
        }
        return redirect('/blogsadmin')->with('erroor', 'Er ging iets niet helemaal goed, probeer het later nog een keer.');
    }

    private function sendEmails(Blog $blog, Request $request) {
        $verifiedParticipants = $this->verificationController->getVerifiedParticipants()->where('role', Roles::participant);
        $nonVerifiedParticipants = $this->verificationController->getNonVerifiedParticipants()->where('role', Roles::participant);
        $paidParticipants = $this->paymentController->getAllPaidUsers()->where('role', Roles::participant);
        $unPaidParticipants = $this->paymentController->getAllNonPaidUsers()->where('role', Roles::participant);

        $userArr = [];

        if(isset($request->NotVerified)) {
            foreach($nonVerifiedParticipants as $participant) {
                array_push($userArr, $participant);
            }
        }

        if(isset($request->Verified)) {
            foreach($verifiedParticipants as $participant) {
                if(!$participant->hasPaid()) {
                    array_push($userArr, $participant);
                }
            }
        }

        if(isset($request->UnPaid)) {
            foreach($unPaidParticipants as $participant) {
                array_push($userArr, $participant);
            }
        }

        if(isset($request->Paid)) {
            foreach($paidParticipants as $participant) {
                array_push($userArr, $participant);
            }
        }
        $filtered = collect($userArr)->unique('id');
        foreach($filtered as $participant) {
            if(isset($participant)) {
                Log::info('Send blog email to: '. $participant->email);
                if(isset($request->addPaymentLink)){
                    SendBlogMail::dispatch($participant, $blog, true);
                } else {
                    SendBlogMail::dispatch($participant, $blog, false);
                }
            }
        }
    }
}
