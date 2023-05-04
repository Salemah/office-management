<?php

namespace App\Console\Commands;

use App\Mail\MailNotify;
use App\Models\CRM\Client\ClientReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder Email Notification Send';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reminders = ClientReminder::with('createdBy','clients')->where('email_status',1)->where('date',Carbon::now()->format('Y-m-d'))->get();
        foreach ($reminders as $reminder) {

        $data = [
            "subject"=>"Tanvir",
            "body"=> $reminder->createdBy->name,
            "details"=> $reminder->reminder_note,
            "client"=>$reminder->clients->name,
            "time"=>Carbon::parse($reminder->time)->format('h:i a'),
            "date"=>Carbon::parse($reminder->date)->format('d M, Y'),

            ];
        $user =User::where('id',$reminder->created_by)->where('user_type',1)->first();
        Mail::to($user->email)->send(new MailNotify($data));

        // $reminder = ClientReminder::findOrFail($reminder->id);
        // $reminder->email_status = 2;
        // $reminder->update();

        }
         $this->info('Successfully sent Reminder Details.');
    }
}
