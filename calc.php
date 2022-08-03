<?php
    function exitWithError($error = "Invalid Request") {
        exit(json_encode(["error" => $error]));
    }

    class Deposit {
        public $sum;
        protected $date;
        protected $percent;
        protected $days_year;
        protected $days_month;

        public function __construct($sum, $date, $percent) {
            $this->sum = $sum;
            $this->date = $date;
            $this->percent = $percent;
        }

        protected function calcDayCounts() {
            $this->days_year = 337 + cal_days_in_month(CAL_GREGORIAN, 2, intval($this->date->format("Y")));
            $this->days_month = cal_days_in_month(CAL_GREGORIAN, intval($this->date->format("m")), intval($this->date->format("Y")));
        }        

        public function spendMonth($sumAdd) {
            $this->calcDayCounts();
            $start_day = intval($this->date->format("d"));
            $this->sum += $sumAdd;
            $this->sum += $this->sum * ($this->days_month - $start_day + 1) * ($this->percent / 100 / $this->days_year);
            $this->date->add(new DateInterval("P" . strval($this->days_month - $start_day + 1) ."D"));
        }
    }

    if (!isset($_GET) || !array_key_exists("data", $_GET))
        exitWithError();
    $request_data = json_decode($_GET["data"], true);
    if (
        !array_key_exists("startDate", $request_data) || !DateTime::createFromFormat('d.m.Y', $request_data["startDate"]) ||

        !array_key_exists("sum", $request_data) || !is_numeric($request_data["sum"]) ||
        $request_data["sum"] < 1000 || $request_data["sum"] > 3000000 ||

        !array_key_exists("term", $request_data) || !is_numeric($request_data["term"]) || !is_int(+$request_data["term"]) ||
        $request_data["term"] < 1 || $request_data["term"] > 60 ||

        !array_key_exists("sumAdd", $request_data) || !is_numeric($request_data["sumAdd"]) ||
        $request_data["sumAdd"] < 0 || $request_data["sumAdd"] > 3000000 ||

        !array_key_exists("percent", $request_data) || !is_numeric($request_data["percent"]) ||!is_int(+$request_data["percent"]) ||
        $request_data["percent"] < 3 || $request_data["percent"] > 100
    )
        exitWithError();

    $deposit = new Deposit(
        $request_data["sum"],
        DateTime::createFromFormat('d.m.Y', $request_data["startDate"]),
        $request_data["percent"]);
    $deposit->spendMonth(0);
    for ($i = $request_data["term"] - 1; $i > 0; $i--)
        $deposit->spendMonth($request_data["sumAdd"]);
    
    echo json_encode(["sum" => $deposit->sum]);
?>