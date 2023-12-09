<?

if ($_REQUEST['mpage'] === '1c') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/1c.php");
}
if ($_REQUEST['mpage'] === 'other') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/other.php");
}
if ($_REQUEST['mpage'] === 'blagoToday') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/blagoToday.php");
}
if ($_REQUEST['mpage'] === 'blagoKiosk') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/blagoKiosk.php");
}
if ($_REQUEST['mpage'] === 'blagoPurposes') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/blagoPurposes.php");
}
if ($_REQUEST['mpage'] === 'blagoFeedback') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/blagoFeedback.php");
}
if ($_REQUEST['mpage'] === 'hrLink') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/hrLink.php");
}
if ($_REQUEST['mpage'] === 'eStaff') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/blagoEStaff.php");
}
if ($_REQUEST['mpage'] === 'applications') {
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/admin/applications.php");
}