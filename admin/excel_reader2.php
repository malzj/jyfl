<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>reader</title>
</head>

<body>
<?php



//require_once 'PEAR.php';
require_once 'oleread.inc';
//require_once 'OLE.php';

define('SPREADSHEET_EXCEL_READER_BIFF8',             0x600);
define('SPREADSHEET_EXCEL_READER_BIFF7',             0x500);
define('SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS',   0x5);
define('SPREADSHEET_EXCEL_READER_WORKSHEET',         0x10);

define('SPREADSHEET_EXCEL_READER_TYPE_BOF',          0x809);
define('SPREADSHEET_EXCEL_READER_TYPE_EOF',          0x0a);
define('SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET',   0x85);
define('SPREADSHEET_EXCEL_READER_TYPE_DIMENSION',    0x200);
define('SPREADSHEET_EXCEL_READER_TYPE_ROW',          0x208);
define('SPREADSHEET_EXCEL_READER_TYPE_DBCELL',       0xd7);
define('SPREADSHEET_EXCEL_READER_TYPE_FILEPASS',     0x2f);
define('SPREADSHEET_EXCEL_READER_TYPE_NOTE',         0x1c);
define('SPREADSHEET_EXCEL_READER_TYPE_TXO',          0x1b6);
define('SPREADSHEET_EXCEL_READER_TYPE_RK',           0x7e);
define('SPREADSHEET_EXCEL_READER_TYPE_RK2',          0x27e);
define('SPREADSHEET_EXCEL_READER_TYPE_MULRK',        0xbd);
define('SPREADSHEET_EXCEL_READER_TYPE_MULBLANK',     0xbe);
define('SPREADSHEET_EXCEL_READER_TYPE_INDEX',        0x20b);
define('SPREADSHEET_EXCEL_READER_TYPE_SST',          0xfc);
define('SPREADSHEET_EXCEL_READER_TYPE_EXTSST',       0xff);
define('SPREADSHEET_EXCEL_READER_TYPE_CONTINUE',     0x3c);
define('SPREADSHEET_EXCEL_READER_TYPE_LABEL',        0x204);
define('SPREADSHEET_EXCEL_READER_TYPE_LABELSST',     0xfd);
define('SPREADSHEET_EXCEL_READER_TYPE_NUMBER',       0x203);
define('SPREADSHEET_EXCEL_READER_TYPE_NAME',         0x18);
define('SPREADSHEET_EXCEL_READER_TYPE_ARRAY',        0x221);
define('SPREADSHEET_EXCEL_READER_TYPE_STRING',       0x207);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA',      0x406);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA2',     0x6);
define('SPREADSHEET_EXCEL_READER_TYPE_FORMAT',       0x41e);
define('SPREADSHEET_EXCEL_READER_TYPE_XF',           0xe0);
define('SPREADSHEET_EXCEL_READER_TYPE_BOOLERR',      0x205);
define('SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN',      0xffff);
define('SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22);
define('SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS',  0xE5);

define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS' ,    25569);
define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107);
define('SPREADSHEET_EXCEL_READER_MSINADAY',          86400);
//define('SPREADSHEET_EXCEL_READER_MSINADAY', 24 * 60 * 60);

//define('SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT', "%.2f");
define('SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT',    "%s");



class Spreadsheet_Excel_Reader {
var $boundsheets = array();

var $formatRecords = array();

var $sst = array();

var $sheets = array();

var $data;

var $_ole;

var $_defaultEncoding;

var $_defaultFormat = SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT;

var $_columnsFormat = array();

var $_rowoffset = 1;

var $_coloffset = 1;

var $dateFormats = array (
0xe => "d/m/Y",
0xf => "d-M-Y",
0x10 => "d-M",
0x11 => "M-Y",
0x12 => "h:i a",
0x13 => "h:i:s a",
0x14 => "H:i",
0x15 => "H:i:s",
0x16 => "d/m/Y H:i",
0x2d => "i:s",
0x2e => "H:i:s",
0x2f => "i:s.S");

var $numberFormats = array(
0x1 => "%1.0f",     // "0"
0x2 => "%1.2f",     // "0.00",
0x3 => "%1.0f",     //"#,##0",
0x4 => "%1.2f",     //"#,##0.00",
0x5 => "%1.0f",    
0x6 => '$%1.0f',    
0x7 => '$%1.2f',    //"$#,##0.00;($#,##0.00)",
0x8 => '$%1.2f',    //"$#,##0.00;($#,##0.00)",
0x9 => '%1.0f%%',   // "0%"
0xa => '%1.2f%%',   // "0.00%"
0xb => '%1.2f',     // 0.00E00",
0x25 => '%1.0f',    // "#,##0;(#,##0)",
0x26 => '%1.0f',    //"#,##0;(#,##0)",
0x27 => '%1.2f',    //"#,##0.00;(#,##0.00)",
0x28 => '%1.2f',    //"#,##0.00;(#,##0.00)",
0x29 => '%1.0f',    //"#,##0;(#,##0)",
0x2a => '$%1.0f',   //"$#,##0;($#,##0)",
0x2b => '%1.2f',    //"#,##0.00;(#,##0.00)",
0x2c => '$%1.2f',   //"$#,##0.00;($#,##0.00)",
0x30 => '%1.0f');   //"##0.0E0";

// }}}
// {{{ Spreadsheet_Excel_Reader()

function Spreadsheet_Excel_Reader() {
$this->_ole =& new OLERead();
$this->setUTFEncoder('iconv');
}