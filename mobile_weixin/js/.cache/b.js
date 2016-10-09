/*TMODJS:{"version":44,"md5":"2d780ca1f2851dbbf428bf0d015703cd"}*/
template('b',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,title=$data.title,time=$data.time,b=$data.b,$out='';$out+='<br> ';
$out+=$escape(title);
$out+='<br> ';
$out+=$escape(time);
$out+='<br> ';
$out+=$escape(b);
$out+='444';
return new String($out);
});