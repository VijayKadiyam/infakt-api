<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\BaReportEmail;
use Carbon\Carbon;
use App\User;

class EmailBaReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:ba_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email daily BA Report';

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
        ini_set('max_execution_time', 0);

        $todayDate = Carbon::now()->addDays(-1)->format('Y-m-d');
        // $todayDate = Carbon::now()->format('Y-m-d');

        $this->info('Email Report for Date: ' . $todayDate);

        $supervisors = User::with('roles')
            ->where('active', '=', 1)
            ->whereHas('roles',  function ($q) {
                $q->where('name', '=', 'SUPERVISOR');
            })->orderBy('name')
            // ->take(1)
            ->get();
        $count = 1;
        foreach ($supervisors as $supervisor) {
            $name = $supervisor->name;
            $rsm = 'nancy.t@mamaearth.in';
            if ($supervisor->region == 'EAST')
                $rsm = 'swarupa.c@mamaearth.in';
            if ($supervisor->region == 'WEST')
                $rsm = 'casilda.r@mamaearth.in';
            if ($supervisor->region == 'NORTH')
                $rsm = 'rajlakshmi.s@mamaearth.in';


            Mail::to($supervisor->email)
                ->cc(['bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in', $rsm])
                ->send(new BaReportEmail($todayDate, $name));

            $this->info("$count. $name BAs Report Emailed...");

            $count++;
        }

        // // Compelete BA Report
        // Mail::to('deepika.k@mamaearth.in')
        //     ->cc(['ac.north@pousse.in', 'kirit.sayani@pousse.in', 'bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in'])
        //     ->send(new BaReportEmail($todayDate));

        // $this->info('BA Report Emailed...');

        // Brand wise
        $brands = [
            'Mamaearth',
            'Derma',
        ];

        foreach ($brands as $key => $brand) {
            Mail::to('deepika.k@mamaearth.in')
                ->cc(['ac.north@pousse.in', 'kirit.sayani@pousse.in', 'bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in'])
                ->send(new BaReportEmail($todayDate, $brand));
        }

        // Region wise
        $regions = [
            'North',
            'South',
            'East',
            'West',
        ];

        foreach ($regions as $key => $region) {
            // $rsm = 'ksohail.sk32@gmail.com';
            $rsm = 'nancy.t@mamaearth.in';
            if ($region == 'East')
                $rsm = 'swarupa.c@mamaearth.in';
            if ($region == 'West')
                $rsm = 'casilda.r@mamaearth.in';
            if ($region == 'North')
                $rsm = 'rajlakshmi.s@mamaearth.in';

            if ($region == 'North')
                Mail::to($rsm)
                    ->cc(['bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in', 'sunita.mamaearth@yahoo.com', 'mis.me2@pousse.in'])
                    ->send(new BaReportEmail($todayDate, $region));
            else if ($region == 'East')
                Mail::to($rsm)
                    ->cc(['bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in', 'anirban.choudhury@pousse.in', 'ac.north@pouuse.in'])
                    ->send(new BaReportEmail($todayDate, $region));
            else if ($region == 'West')
                Mail::to($rsm)
                    ->cc(['bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in', 'mis.me3@pousse.in'])
                    ->send(new BaReportEmail($todayDate, $region));
            else
                Mail::to($rsm)
                    ->cc(['bharat.upreti@pousse.in', 'kvjkumr@gmail.com', 'pc.me@pousse.in', 'mis.me1@pousse.in'])
                    ->send(new BaReportEmail($todayDate, $region));
        }

        // Channel wise
        $channels = [
            'IIA',
            // 'GT',
            // 'MT',
            // 'MT - CNC',
        ];

        foreach ($channels as $key => $channel) {
            if ($channel == 'IIA')
                Mail::to('rashi.j@mamaearth.in')
                    ->cc(['kvjkumr@gmail.com'])
                    ->send(new BaReportEmail($todayDate, $channel));
        }
    }
}
