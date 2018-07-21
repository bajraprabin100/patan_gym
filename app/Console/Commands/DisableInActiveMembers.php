<?php

namespace App\Console\Commands;

use App\Models\Admin\Member;
use Illuminate\Console\Command;
use DB;
use App\Models\Admin\Notifications;

class DisableInActiveMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DisableInActiveMembers:disableMembers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable Inactive members';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $members=Member::where('user_status','=','Active')
             ->where('user_valid_date','<=',date('Y-m-d'))
            ->get();
         foreach($members as $m){
           Notifications::create(['message'=>$m->name.' account has been disabled','reference_id'=>$m->membership_no,'type'=>'member_disable']);
          $m->user_status='Deactive';
          $m->save();
         }
    }
}
