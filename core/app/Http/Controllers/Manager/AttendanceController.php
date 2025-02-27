<?php
namespace App\Http\Controllers\Manager;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\User;
use App\Models\Leave;
use App\Models\Holiday;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\Department;
use Carbon\CarbonInterval;
use App\Http\Helpers\Reply;
use App\Models\Designation;
use App\Traits\ImportExcel;
use Illuminate\Http\Request;
use App\Models\EmployeeShift;
use App\Models\EmployeeDetail;
use App\Exports\AttendanceExport;
use App\Imports\AttendanceImport;
use App\Jobs\ImportAttendanceJob;
use App\Models\AttendanceSetting;
use App\Models\CooperativeAddress;
use Illuminate\Support\Facades\DB; 
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EmployeeShiftSchedule;
use App\Exports\AttendanceByMemberExport;
use App\Http\Requests\ClockIn\ClockInRequest; 
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\Attendance\StoreAttendance;
use App\Http\Requests\Admin\Employee\ImportRequest;
use App\Http\Requests\Attendance\StoreBulkAttendance;
use App\Http\Requests\Admin\Employee\ImportProcessRequest;
 
class AttendanceController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    { 
        parent::__construct();
        $this->pageTitle = 'Gestion des Présences';
         
    }

    public function index(Request $request)
    {
        $this->attendance = Attendance::find($request->employee_id);
        // $this->summaryData($request);
        if (request()->ajax()) {
            return $this->summaryData($request);
        }
        
        //$this->employees = EmployeeDetail::with('user')->get();
        $this->employees = User::allEmployees(null, true, 'all', auth()->user()->cooperative_id);
        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->departments = Department::where('cooperative_id',auth()->user()->cooperative_id)->get();
        $this->designations = Designation::where('cooperative_id',auth()->user()->cooperative_id)->get();
         

        return view('manager.attendances.index', $this->data);
    }

    public function summaryData($request)
    {
        
        $employees = User::with(
            [
                'attendance' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                        ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);
                },
                'leaves' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(leaves.leave_date) = ?', [$request->month])
                        ->whereRaw('YEAR(leaves.leave_date) = ?', [$request->year])
                        ->where('status', 'approved');
                },
                'shifts' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(employee_shift_schedules.date) = ?', [$request->month])
                        ->whereRaw('YEAR(employee_shift_schedules.date) = ?', [$request->year]);
                },
                'leaves.type', 'shifts.shift', 'attendance.shift']
        )->join('employee_details', 'employee_details.user_id', '=', 'users.id')
        ->where('users.cooperative_id', auth()->user()->cooperative_id)
            ->select('users.id', 'users.firstname','users.lastname', 'users.email', 'users.created_at', 'employee_details.department_id','employee_details.employee_id','employee_details.joining_date', 'users.image') 
            ->groupBy('users.id');
             
        if ($request->department != 'all') {
            $employees = $employees->where('employee_details.department_id', $request->department);
        }

        if ($request->designation != 'all') {
            $employees = $employees->where('employee_details.designation_id', $request->designation);
        }

        if ($request->userId != 'all') {
            $employees = $employees->where('users.id', $request->userId);
        }
 
        $employees = $employees->get();

        $holidaysEmp = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $holidayOccasions = [];
        $leaveReasons = [];

        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = now()->timezone($this->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {

            $dataBeforeJoin = null;

            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');
            $dataTillRequestedDate = array_fill(1, (int)$this->daysInMonth, 'Absent');
            $daysTofill = ((int)$this->daysInMonth - (int)$now->copy()->format('d'));

            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), '-');
            }
            else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), 'Absent');
            }

            if (!$requestedDate->isPast()) {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($dataTillToday, $dataFromTomorrow);

            } else {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($dataTillRequestedDate, $dataFromTomorrow);
            }

            $shiftScheduleCollection = $employee->shifts->keyBy('date');


            foreach ($employee->shifts as $shifts) {
                if ($shifts->shift->shift_name == 'Day Off') {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$shifts->date->day] = 'Day Off';
                }

            }

            foreach ($employee->attendance as $attendance) {
                $clockInTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time->timezone($this->timezone)->toDateTimeString(), 'UTC');

                if (isset($shiftScheduleCollection[$clockInTime->copy()->startOfDay()->toDateTimeString()])) {
                    $shiftStartTime = Carbon::parse($clockInTime->copy()->toDateString() . ' ' . $shiftScheduleCollection[$clockInTime->copy()->startOfDay()->toDateTimeString()]->shift->office_start_time);
                    $shiftEndTime = Carbon::parse($clockInTime->copy()->toDateString() . ' ' . $shiftScheduleCollection[$clockInTime->copy()->startOfDay()->toDateTimeString()]->shift->office_end_time);

                    if ($clockInTime->between($shiftStartTime, $shiftEndTime)) {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$clockInTime->day] = '<a href="javascript:;" data-toggle="tooltip" data-original-title="'.($attendance->employee_shift_id ? $attendance->shift->shift_name : __('app.present')).'" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-'.($attendance->half_day == 'yes' ? 'star-half-alt' : ($attendance->late == 'yes' ? 'exclamation-circle' : 'check')).' text-primary"></i></a>';

                    }
                    else if ($attendance->employee_shift_id == $shiftScheduleCollection[$clockInTime->copy()->startOfDay()->toDateTimeString()]->shift->id) {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$clockInTime->day] = '<a href="javascript:;" data-toggle="tooltip" data-original-title="'.($attendance->employee_shift_id ? $attendance->shift->shift_name : __('app.present')).'" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-'.($attendance->half_day == 'yes' ? 'star-half-alt' : ($attendance->late == 'yes' ? 'exclamation-circle' : 'check')).' text-primary"></i></a>';

                    }
                    elseif ($clockInTime->betweenIncluded($shiftStartTime->copy()->subDay(), $shiftEndTime->copy()->subDay())) {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$clockInTime->copy()->subDay()->day] = '<a href="javascript:;" data-toggle="tooltip" data-original-title="'.($attendance->employee_shift_id ? $attendance->shift->shift_name : __('app.present')).'" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-'.($attendance->half_day == 'yes' ? 'star-half-alt' : ($attendance->late == 'yes' ? 'exclamation-circle' : 'check')).' text-primary"></i></a>';

                    }
                    else {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$clockInTime->day] = '<a href="javascript:;" data-toggle="tooltip" data-original-title="'.($attendance->employee_shift_id ? $attendance->shift->shift_name : __('app.present')).'" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-'.($attendance->half_day == 'yes' ? 'star-half-alt' : ($attendance->late == 'yes' ? 'exclamation-circle' : 'check')).' text-primary"></i></a>';
                    }

                }
                else {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$clockInTime->day] = '<a href="javascript:;" class="view-attendance" data-attendance-id="' . $attendance->id . '"><i class="fa fa-'.($attendance->half_day == 'yes' ? 'star-half-alt' : ($attendance->late == 'yes' ? 'exclamation-circle' : 'check')).' text-primary"></i></a>';
                }
            }

            $emplolyeeName = view('components.employee', [
                'user' => $employee
            ]);

            $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][] = $emplolyeeName;
            
            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if ($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) {
                    if ($employee->employeeDetail->joining_date->format('d') == '01') {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->format('d'), '-');
                    }
                    else {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                    }
                }

                if (($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y')) {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if (Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()) {
                $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }

            if (!is_null($dataBeforeJoin)) {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname], $dataBeforeJoin);
            }

            foreach ($employee->leaves as $leave) {
                if ($leave->duration == 'half day') {
                    if ($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] == '-' || $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] == 'Absent') {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] = 'Half Day';
                    }
                }
                else {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] = 'Leave';
                    $leaveReasons[$employee->id][$leave->leave_date->day] = $leave->type->type_name.': '.$leave->reason;
                }

            }
            
            foreach ($holidaysEmp as $holiday) {
               if(!in_array($holiday->date->day,[25,26])){
                if($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] == 'Absent' || $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] == '-') { 
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] = 'Holiday';
                    $holidayOccasions[$holiday->date->day] = $holiday->occassion;
                }
               }
                
                
            }
        }

        $this->employeeAttendence = $final;
        $this->holidayOccasions = $holidayOccasions;
        $this->leaveReasons = $leaveReasons;

        $this->weekMap = Holiday::weekMap('D');

        $this->month = $request->month;
        $this->year = $request->year;

        $view = view('manager.attendances.ajax.summary_data', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
        $attendance = Attendance::with('user', 'user.employeeDetail', 'location')->findOrFail($id);

        $attendanceSettings = EmployeeShiftSchedule::with('shift')->where('user_id', $attendance->user_id)->where('date', $attendance->clock_in_time->format('Y-m-d'))->first();

        if ($attendanceSettings) {
            $attendanceSettingss = $attendanceSettings->shift;

        }
        else {
            $attendanceSettingss = AttendanceSetting::first()->shift; // Do not get this from session here
        }

        

        $attendanceActivity = Attendance::userAttendanceByDate($attendance->clock_in_time->format('Y-m-d'), $attendance->clock_in_time->format('Y-m-d'), $attendance->user_id);

        // $attendanceActivity = $attendanceActivity;
        $attendanceActivity = $attendanceActivity->reverse()->values();

        $settingStartTime = Carbon::createFromFormat('H:i:s', $attendanceSettingss->office_start_time, cooperative()->timezone);
        $defaultEndTime = $settingEndTime = Carbon::createFromFormat('H:i:s', $attendanceSettingss->office_end_time, cooperative()->timezone);

        if ($settingStartTime->gt($settingEndTime)) {
            $settingEndTime->addDay();
        }

        if ($settingEndTime->greaterThan(now()->timezone(cooperative()->timezone))) {
            $defaultEndTime = now()->timezone(cooperative()->timezone);
        }

        $this->totalTime = 0;

        foreach ($attendanceActivity as $key => $activity) {
            if ($key == 0) {
                $this->firstClockIn = $activity;
                $this->attendanceDate = ($activity->shift_start_time) ? Carbon::parse($activity->shift_start_time) : Carbon::parse($this->firstClockIn->clock_in_time)->timezone(cooperative()->timezone);
                $this->startTime = Carbon::parse($this->firstClockIn->clock_in_time)->timezone(cooperative()->timezone);
            }

            $this->lastClockOut = $activity;

            if (!is_null($this->lastClockOut->clock_out_time)) {
                $this->endTime = Carbon::parse($this->lastClockOut->clock_out_time)->timezone(cooperative()->timezone);

            }
            elseif (($this->lastClockOut->clock_in_time->timezone(cooperative()->timezone)->format('Y-m-d') != now()->timezone(cooperative()->timezone)->format('Y-m-d')) && is_null($this->lastClockOut->clock_out_time)) {
                $this->endTime = Carbon::parse($this->startTime->format('Y-m-d') . ' ' . $attendanceSettingss->office_end_time, cooperative()->timezone);

                if ($this->startTime->gt($this->endTime)) {
                    $this->endTime->addDay();
                }

                $this->notClockedOut = true;

            }
            else {
                $this->endTime = $defaultEndTime;
                $this->notClockedOut = true;
            }

            $this->totalTime = $this->totalTime + $this->endTime->timezone(cooperative()->timezone)->diffInSeconds($activity->clock_in_time->timezone(cooperative()->timezone));
        }

        $this->maxClockIn = $attendanceActivity->count() < $attendanceSettingss->clockin_in_day;

        /** @phpstan-ignore-next-line */
        $this->totalTime = CarbonInterval::formatHuman($this->totalTime);
        $this->attendanceActivity = $attendanceSettingss;
        $this->attendance = $attendance;

        return view('manager.attendances.ajax.show', $this->data);

    }

    public function edit($id)
    {

        $attendance = Attendance::findOrFail($id);
      
        $attendanceSettings = EmployeeShiftSchedule::with('shift')->where('user_id', $attendance->user_id)->where('date', $attendance->clock_in_time->format('Y-m-d'))->first();


        if ($attendanceSettings) {
            $this->attendanceSettings = $attendanceSettings->shift;

        }
        else {

            $this->attendanceSettings = AttendanceSetting::with('shift')->first(); // Do not get this from session here
        }


        $this->date = $attendance->clock_in_time->format('Y-m-d');
        $this->row = $attendance;
        $this->clock_in = 1;
        $this->userid = $attendance->user_id;
        $this->total_clock_in = Attendance::where('user_id', $attendance->user_id)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $this->date)
            ->whereNull('attendances.clock_out_time')->count();
        $this->type = 'edit';
        $this->location = CooperativeAddress::all();
        $this->attendanceUser = User::findOrFail($attendance->user_id);

        $this->maxAttendanceInDay = $this->attendanceSettings->clockin_in_day;

        return view('manager.attendances.ajax.edit', $this->data);
    }

    public function update(ClockInRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $date = Carbon::parse($request->attendance_date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_in_time, $this->timezone);
        $clockIn->setTimezone('UTC');

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_out_time, $this->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                $clockOut = $clockOut->addDay();
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }
        else {
            $clockOut = null;
        }

        $attendance->user_id = $request->user_id;
        $attendance->cooperative_id = auth()->user()->cooperative_id;
        $attendance->clock_in_time = $clockIn;
        $attendance->clock_in_ip = $request->clock_in_ip;
        $attendance->clock_out_time = $clockOut;
        $attendance->clock_out_ip = $request->clock_out_ip;
        $attendance->working_from = $request->working_from;
        $attendance->work_from_type = $request->work_from_type;
        $attendance->location_id = $request->location;
        $attendance->late = ($request->has('late')) ? 'yes' : 'no';
        $attendance->half_day = ($request->has('halfday')) ? 'yes' : 'no';
        $attendance->save();

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    public function mark(Request $request, $userid, $day, $month, $year)
    {
        $date = Carbon::createFromFormat('d-m-Y', $day . '-' . $month . '-' . $year)->format('Y-m-d');

        $attendanceSettings = EmployeeShiftSchedule::with('shift')->where('user_id', $userid)->where('date', $date)->first();
        
        if ($attendanceSettings) {
            $attendanceSettings = $attendanceSettings->shift;

        }
        else {
            $attendanceSettings = AttendanceSetting::with('shift')->first(); // Do not get this from session here
        }
        
         
        $this->row = Attendance::attendanceByUserDate($userid, $date);
        $this->clock_in = 0;
        $this->total_clock_in = Attendance::where('user_id', $userid)
            ->where(DB::raw('DATE(attendances.clock_in_time)'), '=', $date)
            ->whereNull('attendances.clock_out_time')->count();

        $this->userid = $userid;
        $this->attendanceUser = User::findOrFail($userid);
        $this->type = 'add';
        $this->date = $date;
         
        $this->maxAttendanceInDay = $attendanceSettings->clockin_in_day;
        $this->attendanceSettings = $attendanceSettings;
        $this->location = CooperativeAddress::all();
 
        return view('manager.attendances.ajax.edit', $this->data);
    }

    public function store(StoreAttendance $request)
    {
        $date = Carbon::parse($request->attendance_date)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_in_time, $this->timezone);
        $clockIn->setTimezone('UTC');

        $attendanceSettings = EmployeeShiftSchedule::with('shift')->where('user_id', $request->user_id)->where('date', $clockIn->toDateString())->first();

        if ($attendanceSettings) {
            $attendanceSettings = $attendanceSettings->shift;

        }
        else {
            $attendanceSettings = AttendanceSetting::first()->shift; // Do not get this from session here
        }

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_out_time, $this->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                $clockOut = $clockOut->addDay();
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }
        else {
            $clockOut = null;
        }

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
            ->whereNull('clock_out_time')
            ->first();

        $startTimestamp = $date . ' ' . $attendanceSettings->office_start_time;
        $endTimestamp = $date . ' ' . $attendanceSettings->office_end_time;
        $officeStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTimestamp, $this->timezone);
        $officeEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTimestamp, $this->timezone);
        $officeStartTime = $officeStartTime->setTimezone('UTC');
        $officeEndTime = $officeEndTime->setTimezone('UTC');
        $clockInCount = Attendance::getTotalUserClockInWithTime($officeStartTime, $officeEndTime, $request->user_id);

        $employeeShiftId = $attendanceSettings->id;

        $shiftStartTime = Carbon::parse($clockIn)->toDateString() . ' ' . $attendanceSettings->office_start_time;

        if (Carbon::parse($attendanceSettings->office_start_time)->gt(Carbon::parse($attendanceSettings->office_end_time))) {
            $shiftEndTime = Carbon::parse($clockIn)->addDay()->toDateString() . ' ' . $attendanceSettings->office_end_time;

        }
        else {
            $shiftEndTime = Carbon::parse($clockIn)->toDateString() . ' ' . $attendanceSettings->office_end_time;
        }

        if ($attendance && $request->user_id) {
            return Reply::error(__('messages.attendanceMarked'));
        }
        else {
            $attendances = Attendance::where('user_id', $request->user_id)
            ->where(DB::raw('DATE(`clock_in_time`)'), $date)
            ->get();

            foreach ($attendances as $item) {
                if(!(!$item->clock_in_time->lt($clockIn) && $item->clock_out_time->gt($clockIn) && !$item->clock_in_time->lt($clockOut))) {
                    if(!($item->clock_out_time->lt($clockIn)))
                    {
                        return Reply::error(__('messages.attendanceMarked'));
                    }
                }
            }
        }

        if (!is_null($attendance) && !$request->user_id) {
            $attendance->update([
                'user_id' => $request->user_id,
                'cooperative_id' => auth()->user()->cooperative_id,
                'clock_in_time' => $clockIn,
                'clock_in_ip' => $request->clock_in_ip,
                'clock_out_time' => $clockOut,
                'clock_out_ip' => $request->clock_out_ip,
                'working_from' => $request->working_from,
                'location_id' => $request->location,
                'work_from_type' => $request->work_from_type,
                'employee_shift_id' => $employeeShiftId,
                'shift_start_time' => $shiftStartTime,
                'shift_end_time' => $shiftEndTime,
                'late' => ($request->has('late')) ? 'yes' : 'no',
                'half_day' => ($request->has('halfday')) ? 'yes' : 'no'
            ]);
        }
        else {

            // Check maximum attendance in a day
            if ($clockInCount < $attendanceSettings->clockin_in_day || $request->user_id) {
                Attendance::create([
                    'user_id' => $request->user_id,
                    'cooperative_id' => auth()->user()->cooperative_id,
                    'clock_in_time' => $clockIn,
                    'clock_in_ip' => $request->clock_in_ip,
                    'clock_out_time' => $clockOut,
                    'clock_out_ip' => $request->clock_out_ip,
                    'working_from' => $request->working_from,
                    'location_id' => $request->location,
                    'late' => ($request->has('late')) ? 'yes' : 'no',
                    'employee_shift_id' => $employeeShiftId,
                    'shift_start_time' => $shiftStartTime,
                    'shift_end_time' => $shiftEndTime,
                    'work_from_type' => $request->work_from_type,
                    'half_day' => ($request->has('halfday')) ? 'yes' : 'no'
                ]);
            }
            else {
                return Reply::error(__('messages.maxClockin'));
            }
        }

        return Reply::success(__('messages.attendanceSaveSuccess'));
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function byMember()
    {
        $this->pageTitle = 'modules.attendance.attendanceByMember';
 
        $this->employees = EmployeeDetail::with('user')->get();

        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        return view('manager.attendances.by_member', $this->data);
    }

    public function employeeData(Request $request, $startDate = null, $endDate = null, $userId = null)
    {
        $ant = []; // Array For attendance Data indexed by similar date
        $dateWiseData = []; // Array For Combine Data

        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->startOfMonth()->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        $userId = $request->userId;

        $attendances = Attendance::userAttendanceByDate($startDate, $endDate, $userId); // Getting Attendance Data
       
        $holidays = Holiday::getHolidayByDates($startDate, $endDate); // Getting Holiday Data
        $userId = $request->userId;

        $totalWorkingDays = $startDate->daysInMonth;

        $totalWorkingDays = $totalWorkingDays - count($holidays);
        $daysPresent = Attendance::countDaysPresentByUser($startDate, $endDate, $userId);
        $daysLate = Attendance::countDaysLateByUser($startDate, $endDate, $userId);
        $halfDays = Attendance::countHalfDaysByUser($startDate, $endDate, $userId);
        $daysAbsent = (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent);
        $holidayCount = Count($holidays);

        // Getting Leaves Data
        $leavesDates = Leave::where('user_id', $userId)
            ->where('leave_date', '>=', $startDate)
            ->where('leave_date', '<=', $endDate)
            ->where('status', 'approved')
            ->select('leave_date', 'reason', 'duration')
            ->get()->keyBy('date')->toArray();

        $holidayData = $holidays->keyBy('holiday_date');
        $holidayArray = $holidayData->toArray();

        // Set Date as index for same date clock-ins
        foreach ($attendances as $attand) {
            $clockInTime = Carbon::createFromFormat('Y-m-d H:i:s', $attand->clock_in_time->timezone($this->timezone)->toDateTimeString(), 'UTC');

            if (!is_null($attand->employee_shift_id)) {
                $shiftStartTime = Carbon::parse($clockInTime->copy()->toDateString() . ' ' . $attand->shift->office_start_time);
                $shiftEndTime = Carbon::parse($clockInTime->copy()->toDateString() . ' ' . $attand->shift->office_end_time);

                if ($shiftStartTime->gt($shiftEndTime)) {
                    $shiftEndTime = $shiftEndTime->addDay();
                }

                $shiftSchedule = EmployeeShiftSchedule::with('shift')->where('user_id', $attand->user_id)->where('date', $attand->clock_in_time->format('Y-m-d'))->first();

                if (($shiftSchedule && $attand->employee_shift_id == $shiftSchedule->shift->id) || is_null($shiftSchedule)) {
                    $ant[$clockInTime->copy()->toDateString()][] = $attand; // Set attendance Data indexed by similar date

                }
                elseif ($clockInTime->betweenIncluded($shiftStartTime, $shiftEndTime)) {
                    $ant[$clockInTime->copy()->toDateString()][] = $attand; // Set attendance Data indexed by similar date

                }
                elseif ($clockInTime->betweenIncluded($shiftStartTime->copy()->subDay(), $shiftEndTime->copy()->subDay())) {
                    $ant[$clockInTime->copy()->subDay()->toDateString()][] = $attand; // Set attendance Data indexed by previous date
                }
            }
            else {
                $ant[$attand->clock_in_date][] = $attand; // Set attendance Data indexed by similar date
            }
        }

        // Set All Data in a single Array
        // @codingStandardsIgnoreStart

        for ($date = $endDate; $date->diffInDays($startDate) > 0; $date->subDay()) {
            // @codingStandardsIgnoreEnd

            if ($date->isPast() || $date->isToday()) {

                // Set default array for record
                $dateWiseData[$date->toDateString()] = [
                    'holiday' => false,
                    'attendance' => false,
                    'leave' => false
                ];

                // Set Holiday Data
                if (array_key_exists($date->toDateString(), $holidayArray)) {
                    $dateWiseData[$date->toDateString()]['holiday'] = $holidayData[$date->toDateString()];
                }

                // Set Attendance Data
                if (array_key_exists($date->toDateString(), $ant)) {
                    $dateWiseData[$date->toDateString()]['attendance'] = $ant[$date->toDateString()];
                }

                // Set Leave Data
                if (array_key_exists($date->toDateString(), $leavesDates)) {
                    $dateWiseData[$date->toDateString()]['leave'] = $leavesDates[$date->toDateString()];
                }

            }

        }

        if ($startDate->isPast() || $startDate->isToday()) {
            // Set default array for record
            $dateWiseData[$startDate->toDateString()] = [
                'holiday' => false,
                'attendance' => false,
                'leave' => false
            ];

            // Set Holiday Data
            if (array_key_exists($startDate->toDateString(), $holidayArray)) {
                $dateWiseData[$startDate->toDateString()]['holiday'] = $holidayData[$startDate->toDateString()];
            }

            // Set Attendance Data
            if (array_key_exists($startDate->toDateString(), $ant)) {
                $dateWiseData[$startDate->toDateString()]['attendance'] = $ant[$startDate->toDateString()];
            }

            // Set Leave Data
            if (array_key_exists($startDate->toDateString(), $leavesDates)) {
                $dateWiseData[$startDate->toDateString()]['leave'] = $leavesDates[$startDate->toDateString()];
            }
        }

        // Getting View data
        $view = view('manager.attendances.ajax.user_attendance', ['dateWiseData' => $dateWiseData, 'global' => $this->cooperative])->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view, 'daysPresent' => $daysPresent, 'daysLate' => $daysLate, 'halfDays' => $halfDays, 'totalWorkingDays' => $totalWorkingDays, 'absentDays' => $daysAbsent, 'holidays' => $holidayCount]);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         
        $this->employees = User::where('cooperative_id',auth()->user()->cooperative_id)->with('employeeDetail')->get();
        
        $this->departments = Department::where('cooperative_id',auth()->user()->cooperative_id)->get();
        $this->pageTitle = __('modules.attendance.markAttendance');
        $this->year = now()->format('Y');
        $this->month = now()->format('m');
        $this->location = CooperativeAddress::where('cooperative_id',auth()->user()->cooperative_id)->get();

        if (request()->ajax()) {
            $html = view('manager.attendances.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.attendances.ajax.create';

        return view('manager.attendances.create', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkMark(StoreBulkAttendance $request)
    {
         
        $employees = $request->user_id;
        $employeeData = User::with('employeeDetail')->whereIn('id', $employees)->get();

        $date = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->format('Y-m-d');
        $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_in_time, $this->timezone);
        $clockIn->setTimezone('UTC');

        if ($request->clock_out_time != '') {
            $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->clock_out_time, $this->timezone);
            $clockOut->setTimezone('UTC');

            if ($clockIn->gt($clockOut) && !is_null($clockOut)) {
                $clockOut = $clockOut->addDay();
            }

            $clockIn = $clockIn->toDateTimeString();
            $clockOut = $clockOut->toDateTimeString();
        }

        if ($request->mark_attendance_by == 'month') {
            $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $request->month . '-' . $request->year)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $period = CarbonPeriod::create($startDate, $endDate);

            $holidays = Holiday::getHolidayByDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))->pluck('holiday_date')->toArray();

        }
        else {
            $dates = explode(',', $request->multi_date);

            $multiDates = CarbonPeriod::create($dates[0], $dates[1]);

            foreach($multiDates as $multiDate)
            {
                $dateRange[] = $multiDate->format('Y-m-d');
            }

            $period = [];
            $holidays = [];

            /** @phpstan-ignore-next-line */
            foreach ($dateRange as $dateData) {
                array_push($period, Carbon::parse($dateData));
                $isHoliday = Holiday::checkHolidayByDate(Carbon::parse($dateData)->format('Y-m-d'));

                if (!is_null($isHoliday)) {
                    $holidays[] = $isHoliday->date->format('Y-m-d');
                }
            }
        }

        $insertData = [];
        $currentDate = now();
        $showClockIn = AttendanceSetting::first();

        foreach ($employees as $key => $userId) {
            $userData = $employeeData->filter(function ($value) use ($userId) {
                return $value->id == $userId;
            })->first();

            if (request()->has('overwrite_attendance')) {

                Attendance::where('user_id', $userId)->delete();

            }

            foreach ($period as $date) {

                $leaveDates = Leave::where('user_id', $userId)
                    ->where('leave_date', $date)
                    ->where('status', 'approved')
                    ->pluck('leave_date')->toArray();

                if(isset($leaveDates[0])) {

                    if($date->format('Y-m-d') == $leaveDates[0]->format('Y-m-d')) {
                        continue;
                    }
                }

                $this->attendanceSettings = $this->attendanceShift($showClockIn, $userId, $date, $request->clock_in_time);

                $attendance = Attendance::where('user_id', $userId)
                    ->where(DB::raw('DATE(`clock_in_time`)'), $date->format('Y-m-d'))
                    ->first();

                if (is_null($attendance)
                && $date->greaterThanOrEqualTo($userData->employeeDetail->joining_date)
                && $date->lessThanOrEqualTo($currentDate)
                && !in_array($date->format('Y-m-d'), $holidays)
                && @$this->attendanceSettings->shift_name != 'Day Off'
                ) { // Attendance should not exist for the user for the same date

                    $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $request->clock_in_time, $this->timezone);
                    $clockIn->setTimezone('UTC');

                    $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $request->clock_out_time, $this->timezone);
                    $clockOut->setTimezone('UTC');

                    if ($clockIn->gt($clockOut) && !is_null($clockOut)) {

                        $clockOut = $clockOut->addDay();
                    }

                    $insertData[] = [
                        'user_id' => $userId,
                        'cooperative_id' => auth()->user()->cooperative_id,
                        'clock_in_time' => $clockIn,
                        'clock_in_ip' => request()->ip(),
                        'clock_out_time' => $clockOut,
                        'clock_out_ip' => request()->ip(),
                        'working_from' => $request->working_from,
                        'work_from_type' => $request->work_from_type,
                        'location_id' => $request->location,
                        'late' => $request->late,
                        'half_day' => $request->half_day,
                        'added_by' => user()->id,
                        'employee_shift_id' => @$this->attendanceSettings->id,
                        'overwrite_attendance' => request()->has('overwrite_attendance') ? $request->overwrite_attendance : 'no',
                        'last_updated_by' => user()->id
                    ];
                }
            }
        }

        Attendance::insertOrIgnore($insertData);

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('manager.hr.attendances.index');
        }

        //return Reply::redirect($redirectUrl, __('messages.attendanceSaveSuccess'));
        return Reply::successWithData(__('messages.attendanceSaveSuccess'), ['redirectUrl' => $redirectUrl]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        Attendance::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function importAttendance()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.menu.attendance');

        

        if (request()->ajax()) {
            $html = view('manager.attendances.ajax.import', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'manager.attendances.ajax.import';

        return view('manager.attendances.create', $this->data);
    }

    public function importStore(ImportRequest $request)
    {
        $this->importFileProcess($request, AttendanceImport::class);

        $view = view('manager.attendances.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }

    public function importProcess(ImportProcessRequest $request)
    {
        $batch = $this->importJobProcess($request, AttendanceImport::class, ImportAttendanceJob::class);

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }

    public function exportAttendanceByMember($year, $month, $id)
    {
        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $month . '-' . $year)->startOfMonth()->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        $obj = User::findOrFail($id);
        $date = $endDate->lessThan(now()) ? $endDate : now();

        return Excel::download(new AttendanceByMemberExport($year, $month, $id, $obj->name, $startDate, $endDate), $obj->name . '_' . $startDate->format('d-m-Y') . '_To_' . $date->format('d-m-Y') . '.xlsx');
    }

    public function exportAllAttendance($year, $month, $id, $department, $designation)
    {
        $startDate = Carbon::createFromFormat('d-m-Y', '01-' . $month . '-' . $year)->startOfMonth()->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $date = $endDate->lessThan(now()) ? $endDate : now();

        return Excel::download(new AttendanceExport($year, $month, $id, $department, $designation, $startDate, $endDate), 'Attendance_From_' . $startDate->format('d-m-Y') . '_To_' . $date->format('d-m-Y') . '.xlsx');
    }

    public function byHour(Request $request)
    {
        $this->pageTitle = 'modules.attendance.attendanceByHour';
 
        $this->employees = EmployeeDetail::with('user')->get();
        $now = Carbon::now($this->timezone);
        $this->year = $now->format('Y');
        $this->month = $now->format('m');
        $this->departments = Department::all();
        $this->designations = Designation::all();

        return view('manager.attendances.by_hour', $this->data);
    }

    public function hourSummaryData($request)
    {
        $employees = User::with(
            ['attendance' => function ($query) use ($request) {
                $query->whereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    ->whereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);
 
            },
            'leaves' => function ($query) use ($request) {
                $query->whereRaw('MONTH(leaves.leave_date) = ?', [$request->month])
                    ->whereRaw('YEAR(leaves.leave_date) = ?', [$request->year])
                    ->where('status', 'approved');
            },
            'shifts' => function ($query) use ($request) {
                $query->whereRaw('MONTH(employee_shift_schedules.date) = ?', [$request->month])
                    ->whereRaw('YEAR(employee_shift_schedules.date) = ?', [$request->year]);
            },
            'leaves.type', 'shifts.shift']
        )->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.firstname','users.lastname', 'users.email', 'users.created_at', 'employee_details.department_id', 'users.image') 
            ->groupBy('users.id');

        if ($request->department != 'all') {
            $employees = $employees->where('employee_details.department_id', $request->department);
        }

        if ($request->designation != 'all') {
            $employees = $employees->where('employee_details.designation_id', $request->designation);
        }

        if ($request->userId != 'all') {
            $employees = $employees->where('users.id', $request->userId);
        }
 

        $employees = $employees->get();

        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $holidayOccasions = [];
        $total = [];

        $leaveReasons = [];
        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = now()->timezone($this->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $count => $employee) {

            $dataBeforeJoin = null;

            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');
            $dataTillRequestedDate = array_fill(1, (int)$this->daysInMonth, 'Absent');
            $daysTofill = ((int)$this->daysInMonth - (int)$now->copy()->format('d'));

            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), '-');
            }
            else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), 'Absent');
            }

            if (!$requestedDate->isPast()) {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($dataTillToday, $dataFromTomorrow);

            } else {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($dataTillRequestedDate, $dataFromTomorrow);
            }

            $totalMinutes = 0;

            $previousdate = null;

            foreach ($employee->shifts as $shifts) {
                if ($shifts->shift->shift_name == 'Day Off') {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$shifts->date->day] = 'Day Off';
                }

            }

            foreach ($employee->attendance as $index => $attendance) {

                $from = $attendance->clock_in_time ?: null;

                $defaultEndDateAndTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time->format('Y-m-d') . ' ' . attendance_setting()->shift->office_end_time);

                $to = $attendance->clock_out_time ?: $defaultEndDateAndTime;

                // totalTime() function is used to calculate the total time of an employee on particular date
                $diffInMins = ($to && $from) ? $attendance->totalTime($from, $to, $employee->id, 'm') : 0;

                // previous date is used to store the previous date of an attendance, so that we can calculate the total time of an employee on particular date
                if ($index == 0) {
                    $previousdate = $from->format('Y-m-d');
                    $totalMinutes += $diffInMins;

                }
                elseif ($previousdate != $from->format('Y-m-d')) {
                    $totalMinutes += $diffInMins;
                    $previousdate = $from->format('Y-m-d');
                }

                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][Carbon::parse($attendance->clock_in_time)->timezone($this->timezone)->day] = '<a href="javascript:;" class="view-attendance" data-attendance-id="' . $attendance->id . '">' . intdiv($diffInMins, 60) . ':' . ($diffInMins % 60) . '</a>';
            }

            // Convert minutes to hours
            /** @phpstan-ignore-next-line */
            $resultTotalTime =  CarbonInterval::formatHuman($totalMinutes);

            $total[$count] = $resultTotalTime;

            $emplolyeeName = view('components.employee', [
                'user' => $employee
            ]);

            $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][] = $emplolyeeName;

            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if ($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) {
                    if ($employee->employeeDetail->joining_date->format('d') == '01') {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->format('d'), '-');
                    }
                    else {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                    }
                }

                if (($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y')) {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if (Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()) {
                $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }

            if (!is_null($dataBeforeJoin)) {
                $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname] = array_replace($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname], $dataBeforeJoin);
            }

            foreach ($employee->leaves as $leave) {
                if ($leave->duration == 'half day') {
                    if ($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] == '-' || $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] == 'Absent') {
                        $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] = 'Half Day';
                    }
                }
                else {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$leave->leave_date->day] = 'Leave';
                    $leaveReasons[$employee->id][$leave->leave_date->day] = $leave->type->type_name.': '.$leave->reason;
                }

            }

            foreach ($employee->shifts as $shifts) {
                if ($shifts->shift->shift_name == 'Day Off') {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$shifts->date->day] = 'Day Off';
                }

            }

            foreach ($this->holidays as $holiday) {
                if ($final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] == 'Absent' || $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] == '-') {
                    $final[$employee->id . "#" . $employee->lastname." ".$employee->firstname][$holiday->date->day] = 'Holiday';
                    $holidayOccasions[$holiday->date->day] = $holiday->occassion;
                }
            }
        }

        $this->employeeAttendence = $final;
        $this->holidayOccasions = $holidayOccasions;
        $this->total = $total;
        $this->month = $request->month;
        $this->year = $request->year;
        $this->leaveReasons = $leaveReasons;

        $view = view('manager.attendances.ajax.hour_summary_data', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function byMapLocation(Request $request)
    {
         
        $this->employees = EmployeeDetail::with('user')->get();
        $this->departments = Department::all();

        return view('manager.attendances.by_map_location', $this->data);
    }

    protected function byMapLocationData($request)
    {
        $this->attendances = Attendance::with('user')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('manager.attendances.*')
            ->whereDate('clock_in_time', Carbon::createFromFormat($this->cooperative->date_format, $request->attendance_date)->toDateString())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->department != 'all') {
            $this->attendances = $this->attendances->where('employee_details.department_id', $request->department);
        }

        if ($request->userId != 'all') {
            $this->attendances = $this->attendances->where('users.id', $request->userId);
        }
 
        if ($request->late != 'all') {
            $this->attendances = $this->attendances->where('manager.attendances.late', $request->late);
        }

        $this->attendances = $this->attendances->get();

        $view = view('manager.attendances.ajax.map_location_data', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function attendanceShift($defaultAttendanceSettings, $userId, $date, $clockInTime)
    {
        $checkPreviousDayShift = EmployeeShiftSchedule::without('shift')->where('user_id', $userId)
            ->where('date', $date->copy()->subDay()->toDateString())
            ->first();

        $checkTodayShift = EmployeeShiftSchedule::without('shift')->where('user_id', $userId)
            ->where('date', $date->copy()->toDateString())
            ->first();

        $backDayFromDefault = Carbon::parse($date->copy()->subDay()->format('Y-m-d') . ' ' . $defaultAttendanceSettings->office_start_time);

        $backDayToDefault = Carbon::parse($date->copy()->subDay()->format('Y-m-d') . ' ' . $defaultAttendanceSettings->office_end_time);

        if ($backDayFromDefault->gt($backDayToDefault)) {
            $backDayToDefault->addDay();
        }

        $nowTime = Carbon::createFromFormat('Y-m-d H:i', $date->copy()->toDateString() . ' ' . $clockInTime, 'UTC');

        if ($checkPreviousDayShift && $nowTime->betweenIncluded($checkPreviousDayShift->shift_start_time, $checkPreviousDayShift->shift_end_time)) {
            $attendanceSettings = $checkPreviousDayShift;

        }
        else if ($nowTime->betweenIncluded($backDayFromDefault, $backDayToDefault)) {
            $attendanceSettings = $defaultAttendanceSettings;

        }
        else if ($checkTodayShift &&
            ($nowTime->betweenIncluded($checkTodayShift->shift_start_time, $checkTodayShift->shift_end_time) || $nowTime->gt($checkTodayShift->shift_end_time))
        ) {
            $attendanceSettings = $checkTodayShift;

        }
        else {
            $attendanceSettings = $defaultAttendanceSettings;
        }

        return $attendanceSettings->shift;

    }

    public function addAttendance($userID, $day, $month, $year)
    {
        $this->date = Carbon::createFromFormat('d-m-Y', $day . '-' . $month . '-' . $year)->format('Y-m-d');
        $this->attendance = Attendance::whereUserId($userID)->first();

        $attendanceSettings = EmployeeShiftSchedule::where('user_id', $userID)->where('date', $this->date)->first();

        if ($attendanceSettings) {
            $this->attendanceSettings = $attendanceSettings->shift;

        }
        else {
            $this->attendanceSettings = AttendanceSetting::first()->shift; // Do not get this from session here

        }

        $this->location = CooperativeAddress::all();
        $this->attendanceUser = User::findOrFail($userID);

        return view('manager.attendances.ajax.add_user_attendance', $this->data);
    }

}
