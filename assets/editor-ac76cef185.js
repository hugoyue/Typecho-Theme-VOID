function insertAtCursor(t,e){var n=t.scrollTop,o=document.documentElement.scrollTop;if(document.selection){t.focus();var s=document.selection.createRange();s.text=e,s.select()}else if(t.selectionStart||"0"==t.selectionStart){var l=t.selectionStart,c=t.selectionEnd;t.value=t.value.substring(0,l)+e+t.value.substring(c,t.value.length),t.focus(),t.selectionStart=l+e.length,t.selectionEnd=l+e.length}else t.value+=e,t.focus();t.scrollTop=n,document.documentElement.scrollTop=o}$(function(){0<$("#wmd-button-row").length&&($("#wmd-button-row").append('<li class="wmd-spacer wmd-spacer1"></li><li class="wmd-button" id="wmd-photoset-button" style="" title="插入图集">图集</li>'),$("#wmd-button-row").append('<li class="wmd-spacer wmd-spacer1"></li><li class="wmd-button" id="wmd-owo-button" style="" title="插入表情"><span style="width:unset" class="OwO"></span></li>'),new OwO({logo:"OωO",container:document.getElementsByClassName("OwO")[0],target:document.getElementById("text"),api:"/usr/themes/VOID/assets/libs/owo/OwO_03.json",position:"down",width:"400px",maxHeight:"250px"})),$(document).on("click","#wmd-photoset-button",function(){myField=document.getElementById("text"),insertAtCursor(myField,"\n\n[photos]\n\n[/photos]\n\n")})});