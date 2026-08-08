$r = DB::table("manifestation_breakdown as mb")
    ->leftJoin("consignee_main as co", "mb.ConsigneeID", "=", "co.ConsigneeID")
    ->where("mb.MainBL", "LATESTBL2217")
    ->get(["mb.HouseBL", "mb.ItemType", "mb.Unit", "mb.Description", "co.FullName"]);

foreach ($r as $x) {
    foreach ((array) $x as $k => $v) {
        if (is_string($v) && !mb_check_encoding($v, "UTF-8")) {
            echo $x->HouseBL . " -> " . $k . " -> " . bin2hex($v) . PHP_EOL;
        }
    }
}
echo "done" . PHP_EOL;
